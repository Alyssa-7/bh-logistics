<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\DeliveryStop;
use App\Models\Item;
use App\Models\Notification;
use App\Models\ReceiveRecord;
use App\Models\ShortageLog;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeliveryOrderController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // LIST / SHOW / STORE / UPDATE / CANCEL
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = DeliveryOrder::with(['branch', 'driver', 'creator', 'items', 'stops.branch', 'stops.items']);

        if ($request->has('status')) {
            if ($request->status === 'in_delivery') {
                $query->whereIn('status', ['in_transit', 'delivered']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        return response()->json($query->latest()->paginate(min($request->integer('per_page', 20), 200)));
    }

    public function show(Request $request, DeliveryOrder $deliveryOrder)
    {
        $user = $request->user();
        if ($user->role === 'driver') {
            // อนุญาตออเดอร์ที่ยังไม่มีคนขับ (แท็บ "ใหม่") และออเดอร์ของตัวเอง
            if ($deliveryOrder->driver_id !== null && $deliveryOrder->driver_id !== $user->id) {
                return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง DO นี้'], 403);
            }
            if ($deliveryOrder->is_backorder && $deliveryOrder->sub_status !== 'bo_approved') {
                return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง DO นี้'], 403);
            }
        }
        if ($user->role === 'branch') {
            $hasAccess = $deliveryOrder->branch_id === $user->branch_id
                || $deliveryOrder->stops()->where('branch_id', $user->branch_id)->exists();
            if (!$hasAccess) {
                return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง DO นี้'], 403);
            }
        }

        return response()->json(
            $deliveryOrder->load([
                'branch', 'driver', 'creator', 'items',
                'stops.branch', 'stops.items',
                'receiveRecord.photos', 'approvedBy',
                'backorders.items', 'shortageLogs',
            ])
        );
    }

    public function store(Request $request)
    {
        // ─── New multi-stop format: { driver_id, scheduled_date, stops:[{branch_id, sequence, items:[]}] }
        if ($request->has('stops')) {
            $request->validate([
                'driver_id'                      => 'nullable|exists:users,id',
                'scheduled_date'                 => 'required|date',
                'stops'                          => 'required|array|min:1',
                'stops.*.branch_id'              => 'required|exists:branches,id',
                'stops.*.sequence'               => 'nullable|integer|min:1',
                'stops.*.items'                  => 'required|array|min:1',
                'stops.*.items.*.item_id'        => 'nullable|exists:items,id',
                'stops.*.items.*.product_name'   => 'nullable|string|max:255',
                'stops.*.items.*.unit'           => 'nullable|string|max:50',
                'stops.*.items.*.qty_ordered'    => 'required|numeric|min:0.01',
            ]);

            // Pre-load all referenced item masters
            $allItemIds = collect($request->stops)
                ->flatMap(fn($s) => collect($s['items'])->pluck('item_id'))
                ->filter()->unique()->values();
            $itemMaster = $allItemIds->isNotEmpty()
                ? Item::whereIn('id', $allItemIds)->where('is_active', true)->get()->keyBy('id')
                : collect();

            $stopsData    = $request->stops;
            $isMultiStop  = count($stopsData) > 1;
            $firstBranchId = $stopsData[0]['branch_id'] ?? null;

            return DB::transaction(function () use ($request, $stopsData, $itemMaster, $isMultiStop, $firstBranchId) {
                $order = DeliveryOrder::create([
                    'branch_id'      => $isMultiStop ? null : $firstBranchId,
                    'driver_id'      => $request->driver_id,
                    'created_by'     => $request->user()->id,
                    'scheduled_date' => $request->scheduled_date,
                    'is_multi_stop'  => $isMultiStop,
                ]);

                $scheduledDate = $order->scheduled_date?->format('d/m/Y') ?? '-';
                $notifiedBranches = [];

                foreach ($stopsData as $idx => $stopData) {
                    $stop = DeliveryStop::create([
                        'delivery_order_id' => $order->id,
                        'branch_id'         => $stopData['branch_id'],
                        'sequence'          => $stopData['sequence'] ?? ($idx + 1),
                        'status'            => 'pending',
                    ]);

                    foreach ($stopData['items'] as $row) {
                        if (!empty($row['item_id']) && isset($itemMaster[$row['item_id']])) {
                            $master = $itemMaster[$row['item_id']];
                            $order->items()->create([
                                'stop_id'      => $stop->id,
                                'item_id'      => $master->id,
                                'product_name' => $master->name,
                                'unit'         => $master->unit,
                                'qty_ordered'  => $row['qty_ordered'],
                            ]);
                        } else {
                            $order->items()->create([
                                'stop_id'      => $stop->id,
                                'product_name' => $row['product_name'] ?? '',
                                'unit'         => $row['unit'] ?? '',
                                'qty_ordered'  => $row['qty_ordered'],
                            ]);
                        }
                    }

                    // Notify each branch (deduplicate)
                    if (!in_array($stopData['branch_id'], $notifiedBranches)) {
                        $notifiedBranches[] = $stopData['branch_id'];
                        $branchUsers = User::where('branch_id', $stopData['branch_id'])
                            ->where('role', 'branch')->where('is_active', true)->pluck('id');
                        if ($branchUsers->isNotEmpty()) {
                            Notification::insert($branchUsers->map(fn($uid) => [
                                'user_id'    => $uid,
                                'type'       => 'do_new',
                                'do_id'      => $order->id,
                                'message'    => "มีออเดอร์ใหม่สำหรับสาขาคุณ: {$order->do_number} นัดส่งวันที่ {$scheduledDate}",
                                'is_read'    => false,
                                'created_at' => now(),
                            ])->all());
                        }
                    }
                }

                // Notify driver
                $stopNames = collect($stopsData)->map(fn($s) => Branch::find($s['branch_id'])?->name ?? '?')->join(', ');
                $push = app(PushNotificationService::class);
                if ($request->driver_id) {
                    Notification::create([
                        'user_id'    => $request->driver_id,
                        'type'       => 'do_assigned',
                        'do_id'      => $order->id,
                        'message'    => "มีออเดอร์ใหม่: {$order->do_number} ส่ง {$stopNames} วันที่ {$scheduledDate}",
                        'is_read'    => false,
                        'created_at' => now(),
                    ]);
                    $push->sendToUsers([$request->driver_id], 'ได้รับงานใหม่', "ออเดอร์ {$order->do_number} ส่ง {$stopNames} วันที่ {$scheduledDate}");
                } else {
                    $driverIds = User::where('role', 'driver')->where('is_active', true)->pluck('id');
                    if ($driverIds->isNotEmpty()) {
                        Notification::insert($driverIds->map(fn($uid) => [
                            'user_id'    => $uid,
                            'type'       => 'do_assigned',
                            'do_id'      => $order->id,
                            'message'    => "ออเดอร์ใหม่รอรับงาน: {$order->do_number} ส่ง {$stopNames} วันที่ {$scheduledDate}",
                            'is_read'    => false,
                            'created_at' => now(),
                        ])->all());
                        $push->sendToUsers($driverIds->toArray(), 'ออเดอร์ใหม่รอรับงาน', "ออเดอร์ {$order->do_number} ส่ง {$stopNames} วันที่ {$scheduledDate}");
                    }
                }
                // Push to all notified branch users
                $push->sendToUsers(array_unique(collect($notifiedBranches)->flatMap(fn($bid) =>
                    User::where('branch_id', $bid)->where('role', 'branch')->where('is_active', true)->pluck('id')->toArray()
                )->toArray()), 'ออเดอร์ใหม่', "มีออเดอร์ใหม่: {$order->do_number} นัดส่งวันที่ {$scheduledDate}");

                return response()->json($order->load(['branch', 'items', 'stops.branch', 'stops.items']), 201);
            });
        }

        // ─── Legacy single-stop format: { branch_id, driver_id, scheduled_date, items:[] }
        $request->validate([
            'branch_id'            => 'required|exists:branches,id',
            'driver_id'            => 'nullable|exists:users,id',
            'scheduled_date'       => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'nullable|exists:items,id',
            'items.*.product_name' => 'nullable|string|max:255',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.qty_ordered'  => 'required|numeric|min:0.01',
        ]);

        $itemIds = collect($request->items)->pluck('item_id')->filter()->values();
        $itemMaster = $itemIds->isNotEmpty()
            ? Item::whereIn('id', $itemIds)->where('is_active', true)->get()->keyBy('id')
            : collect();

        if ($itemMaster->count() < $itemIds->count()) {
            return response()->json(['message' => 'สินค้าบางรายการไม่พร้อมใช้งาน'], 422);
        }

        return DB::transaction(function () use ($request, $itemMaster) {
            $order = DeliveryOrder::create([
                'branch_id'      => $request->branch_id,
                'driver_id'      => $request->driver_id,
                'created_by'     => $request->user()->id,
                'scheduled_date' => $request->scheduled_date,
                'is_multi_stop'  => false,
            ]);

            $stop = DeliveryStop::create([
                'delivery_order_id' => $order->id,
                'branch_id'         => $request->branch_id,
                'sequence'          => 1,
                'status'            => 'pending',
            ]);

            foreach ($request->items as $row) {
                if (!empty($row['item_id']) && isset($itemMaster[$row['item_id']])) {
                    $master = $itemMaster[$row['item_id']];
                    $order->items()->create([
                        'stop_id'      => $stop->id,
                        'item_id'      => $master->id,
                        'product_name' => $master->name,
                        'unit'         => $master->unit,
                        'qty_ordered'  => $row['qty_ordered'],
                    ]);
                } else {
                    $order->items()->create([
                        'stop_id'      => $stop->id,
                        'product_name' => $row['product_name'] ?? '',
                        'unit'         => $row['unit'] ?? '',
                        'qty_ordered'  => $row['qty_ordered'],
                    ]);
                }
            }

            $order->load('branch');
            $scheduledDate = $order->scheduled_date?->format('d/m/Y') ?? '-';

            $push = app(PushNotificationService::class);
            $branchName = $order->branch?->name ?? '';

            if ($request->driver_id) {
                Notification::create([
                    'user_id'    => $request->driver_id,
                    'type'       => 'do_assigned',
                    'do_id'      => $order->id,
                    'message'    => "มีออเดอร์ใหม่: {$order->do_number} ส่ง {$branchName} วันที่ {$scheduledDate}",
                    'is_read'    => false,
                    'created_at' => now(),
                ]);
                $push->sendToUsers([$request->driver_id], 'ได้รับงานใหม่', "ออเดอร์ {$order->do_number} ส่ง {$branchName} วันที่ {$scheduledDate}");
            } else {
                $driverIds = User::where('role', 'driver')->where('is_active', true)->pluck('id');
                if ($driverIds->isNotEmpty()) {
                    Notification::insert($driverIds->map(fn($uid) => [
                        'user_id'    => $uid,
                        'type'       => 'do_assigned',
                        'do_id'      => $order->id,
                        'message'    => "ออเดอร์ใหม่รอรับงาน: {$order->do_number} ส่ง {$branchName} วันที่ {$scheduledDate}",
                        'is_read'    => false,
                        'created_at' => now(),
                    ])->all());
                    $push->sendToUsers($driverIds->toArray(), 'ออเดอร์ใหม่รอรับงาน', "ออเดอร์ {$order->do_number} ส่ง {$branchName} วันที่ {$scheduledDate}");
                }
            }

            $branchUsers = User::where('branch_id', $order->branch_id)
                ->where('role', 'branch')->where('is_active', true)->pluck('id');
            if ($branchUsers->isNotEmpty()) {
                Notification::insert($branchUsers->map(fn($uid) => [
                    'user_id'    => $uid,
                    'type'       => 'do_new',
                    'do_id'      => $order->id,
                    'message'    => "มีออเดอร์ใหม่สำหรับสาขาคุณ: {$order->do_number} นัดส่งวันที่ {$scheduledDate}",
                    'is_read'    => false,
                    'created_at' => now(),
                ])->all());
                $push->sendToUsers($branchUsers->toArray(), 'ออเดอร์ใหม่', "มีออเดอร์ใหม่: {$order->do_number} นัดส่งวันที่ {$scheduledDate}");
            }

            return response()->json($order->load(['branch', 'items', 'stops.branch', 'stops.items']), 201);
        });
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $request->validate([
            'branch_id'      => 'sometimes|exists:branches,id',
            'driver_id'      => 'sometimes|nullable|exists:users,id',
            'scheduled_date' => 'sometimes|date',
            'status'         => 'sometimes|in:pending,in_transit,delivered,completed,cancelled',
        ]);

        if (in_array($deliveryOrder->status, ['completed', 'cancelled'])) {
            return response()->json(['message' => 'ไม่สามารถแก้ไข DO ที่เสร็จสิ้นหรือยกเลิกแล้ว'], 422);
        }

        $data = $request->only(['branch_id', 'driver_id', 'scheduled_date', 'status']);

        if (isset($data['status']) && $data['status'] === 'in_transit') {
            $driverId = $data['driver_id'] ?? $deliveryOrder->driver_id;
            if (!$driverId) {
                return response()->json(['message' => 'กรุณาเลือกไรเดอร์ก่อนเริ่มส่ง'], 422);
            }
        }

        if (isset($data['status']) && $data['status'] === 'in_transit' && !$deliveryOrder->dispatched_at) {
            $data['dispatched_at'] = now();
        }

        if (isset($data['status']) && $data['status'] === 'delivered') {
            $data['delivered_at'] = now();
        }

        if (isset($data['status']) && $data['status'] === 'completed') {
            $data['delivered_at'] = $deliveryOrder->delivered_at ?? now();
            $data['received_at']  = now();
        }

        $oldDriverId = $deliveryOrder->driver_id;
        $deliveryOrder->update($data);

        if (array_key_exists('driver_id', $data) && $data['driver_id'] && $data['driver_id'] != $oldDriverId) {
            $deliveryOrder->load(['branch', 'stops.branch']);
            $scheduledDate = $deliveryOrder->scheduled_date?->format('d/m/Y') ?? '-';
            $destName = $deliveryOrder->is_multi_stop
                ? $deliveryOrder->stops->map(fn($s) => $s->branch?->name)->filter()->join(', ')
                : ($deliveryOrder->branch?->name ?? '-');
            Notification::create([
                'user_id'    => $data['driver_id'],
                'type'       => 'do_assigned',
                'do_id'      => $deliveryOrder->id,
                'message'    => "มีออเดอร์ใหม่: {$deliveryOrder->do_number} ส่ง {$destName} วันที่ {$scheduledDate}",
                'is_read'    => false,
                'created_at' => now(),
            ]);
        }

        return response()->json($deliveryOrder->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items']));
    }

    public function dispatch(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($request->user()->role === 'driver') {
            if ($deliveryOrder->driver_id !== $request->user()->id) {
                return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
            }
        }

        if ($deliveryOrder->status !== 'pending') {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ได้อยู่ในสถานะรอส่ง'], 422);
        }

        // ไรเดอร์ต้องอัปโหลดรูปก่อนออกเดินทาง
        if ($request->user()->role === 'driver' && !$deliveryOrder->depart_photo_path) {
            return response()->json(['message' => 'กรุณาถ่ายรูปหลักฐานก่อนออกเดินทาง (อย่างน้อย 2 รูป)'], 422);
        }

        $deliveryOrder->update([
            'status'        => 'in_transit',
            'dispatched_at' => $deliveryOrder->dispatched_at ?? now(),
        ]);

        $deliveryOrder->load(['branch', 'driver', 'stops.branch']);
        $scheduledDate = $deliveryOrder->scheduled_date?->format('d/m/Y') ?? '-';
        $driverName    = $deliveryOrder->driver?->name ?? 'คนขับ';

        // Notify all branch users across all stops
        $branchIds = $deliveryOrder->is_multi_stop
            ? $deliveryOrder->stops->pluck('branch_id')->unique()
            : collect([$deliveryOrder->branch_id])->filter();

        foreach ($branchIds as $branchId) {
            $branchUsers = User::where('branch_id', $branchId)
                ->where('role', 'branch')->where('is_active', true)->pluck('id');
            if ($branchUsers->isNotEmpty()) {
                Notification::insert($branchUsers->map(fn($uid) => [
                    'user_id'    => $uid,
                    'type'       => 'do_dispatched',
                    'do_id'      => $deliveryOrder->id,
                    'message'    => "{$driverName} กำลังออกส่ง {$deliveryOrder->do_number} นัดส่งวันที่ {$scheduledDate}",
                    'is_read'    => false,
                    'created_at' => now(),
                ])->all());
            }
        }

        return response()->json($deliveryOrder->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items']));
    }

    public function arrive(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($request->user()->role === 'driver') {
            if ($deliveryOrder->driver_id !== $request->user()->id) {
                return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
            }
        }

        if ($deliveryOrder->status !== 'in_transit') {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ได้อยู่ในสถานะกำลังส่ง'], 422);
        }

        $deliveryOrder->update([
            'status'       => 'completed',
            'arrived_at'   => now(),
            'delivered_at' => now(),
            'received_at'  => now(),
        ]);

        return response()->json($deliveryOrder->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items']));
    }

    public function cancel(Request $request, DeliveryOrder $deliveryOrder)
    {
        $user = $request->user();

        if ($deliveryOrder->status === 'cancelled') {
            return response()->json(['message' => 'DO นี้ถูกยกเลิกแล้ว'], 422);
        }

        if ($user->hasRole('hq')) {
            if ($deliveryOrder->created_by !== $user->id) {
                return response()->json(['message' => 'HQ ยกเลิกได้เฉพาะ DO ที่ตัวเองสร้าง'], 403);
            }
            if ($deliveryOrder->status !== 'pending') {
                return response()->json(['message' => 'HQ ยกเลิกได้เฉพาะ DO ที่อยู่ในสถานะ pending'], 422);
            }
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:1000',
        ]);

        $deliveryOrder->update([
            'status'        => 'cancelled',
            'cancel_reason' => $request->cancel_reason ?: null,
        ]);

        return response()->json($deliveryOrder->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items']));
    }

    // ─────────────────────────────────────────────────────────────
    // RECEIVE — Branch ยืนยัน / ปฏิเสธรับสินค้า
    // ─────────────────────────────────────────────────────────────

    public function receiveItems(Request $request, DeliveryOrder $deliveryOrder)
    {
        if (!in_array($deliveryOrder->status, ['in_transit', 'delivered'])) {
            return response()->json([
                'message' => 'รับสินค้าได้เฉพาะ DO ที่อยู่ในสถานะ in_transit หรือ delivered เท่านั้น',
            ], 422);
        }

        $stopId = $request->input('stop_id');

        // Validate items against the correct scope
        if ($stopId && $deliveryOrder->is_multi_stop) {
            $request->validate([
                'signature'            => 'nullable|string',
                'stop_id'              => 'required|exists:delivery_stops,id',
                'items'                => 'required|array',
                'items.*.id'           => [
                    'required',
                    Rule::exists('delivery_items', 'id')
                        ->where('delivery_order_id', $deliveryOrder->id)
                        ->where('stop_id', $stopId),
                ],
                'items.*.qty_received' => 'required|numeric|min:0',
                'items.*.item_note'    => 'nullable|string|max:500',
            ]);
        } else {
            $request->validate([
                'signature'            => 'nullable|string',
                'items'                => 'required|array',
                'items.*.id'           => [
                    'required',
                    Rule::exists('delivery_items', 'id')
                        ->where('delivery_order_id', $deliveryOrder->id),
                ],
                'items.*.qty_received' => 'required|numeric|min:0',
                'items.*.item_note'    => 'nullable|string|max:500',
            ]);
        }

        $deliveryOrder->load(['items', 'branch', 'stops']);

        DB::transaction(function () use ($request, $deliveryOrder, $stopId) {
            foreach ($request->items as $data) {
                $item    = $deliveryOrder->items->find($data['id']);
                $ordered = $item->qty_ordered ?? 0;
                $item->update([
                    'qty_received' => $data['qty_received'] ?? $ordered,
                    'shortage_qty' => 0,
                    'item_note'    => $data['item_note'] ?? null,
                ]);
            }

            if ($stopId && $deliveryOrder->is_multi_stop) {
                // Mark this stop as delivered
                $stop = $deliveryOrder->stops->find($stopId);
                if ($stop) {
                    $stop->update(['status' => 'delivered', 'delivered_at' => now()]);
                }

                // Check if all stops are delivered → complete the order
                $allDelivered = $deliveryOrder->stops->fresh()->every(fn($s) => $s->status === 'delivered');
                if ($allDelivered) {
                    $deliveryOrder->update([
                        'status'       => 'completed',
                        'sub_status'   => null,
                        'received_at'  => now(),
                        'delivered_at' => $deliveryOrder->delivered_at ?? now(),
                    ]);
                    ReceiveRecord::create([
                        'delivery_order_id' => $deliveryOrder->id,
                        'received_by'       => $request->user()->id,
                        'status'            => 'received_all',
                        'signature'         => $request->signature ?: null,
                        'received_at'       => now(),
                    ]);
                }
            } else {
                $deliveryOrder->stops()->whereNotIn('status', ['delivered', 'completed'])
                    ->update(['status' => 'delivered', 'delivered_at' => now()]);
                $deliveryOrder->update([
                    'status'       => 'completed',
                    'sub_status'   => null,
                    'received_at'  => now(),
                    'delivered_at' => $deliveryOrder->delivered_at ?? now(),
                ]);

                ReceiveRecord::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'received_by'       => $request->user()->id,
                    'status'            => 'received_all',
                    'signature'         => $request->signature ?: null,
                    'received_at'       => now(),
                ]);
            }
        });

        return response()->json(
            $deliveryOrder->fresh()->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items', 'backorders'])
        );
    }

    // ─────────────────────────────────────────────────────────────
    // BACKORDER — HQ อนุมัติ / ปฏิเสธ
    // ─────────────────────────────────────────────────────────────

    public function approveBackorder(Request $request, DeliveryOrder $deliveryOrder)
    {
        if (!$deliveryOrder->is_backorder) {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ Backorder'], 422);
        }

        if ($deliveryOrder->sub_status !== 'bo_pending') {
            return response()->json(['message' => 'Backorder นี้ไม่ได้อยู่ในสถานะรอการอนุมัติ'], 422);
        }

        $request->validate([
            'driver_id'           => 'required|exists:users,id',
            'items'               => 'nullable|array',
            'items.*.id'          => 'required|exists:delivery_items,id',
            'items.*.qty_ordered' => 'required|numeric|min:0.01',
            'scheduled_date'      => 'nullable|date',
        ]);

        $driver = User::where('id', $request->driver_id)->where('role', 'driver')->firstOrFail();

        if ($request->filled('items')) {
            foreach ($request->items as $itemData) {
                $deliveryOrder->items()->where('id', $itemData['id'])->update([
                    'qty_ordered' => $itemData['qty_ordered'],
                ]);
            }
        }

        $updateData = [
            'sub_status'  => 'bo_approved',
            'status'      => 'pending',
            'driver_id'   => $driver->id,
            'approved_by' => $request->user()->id,
        ];
        if ($request->filled('scheduled_date')) {
            $updateData['scheduled_date'] = $request->scheduled_date;
        }
        $deliveryOrder->update($updateData);

        $freshDO       = $deliveryOrder->fresh();
        $scheduledDate = $freshDO->scheduled_date?->format('d/m/Y') ?? '-';

        $boMsg = "คุณได้รับ Backorder ใหม่: {$deliveryOrder->do_number} (รอบ {$deliveryOrder->bo_round}) คาดว่าถึงวันที่ {$scheduledDate}";
        Notification::create([
            'user_id'    => $driver->id,
            'type'       => 'bo_assigned',
            'do_id'      => $deliveryOrder->id,
            'message'    => $boMsg,
            'is_read'    => false,
            'created_at' => now(),
        ]);
        app(PushNotificationService::class)->sendToUsers([$driver->id], 'BH-Logistic', $boMsg);

        $branchUsers = User::where('branch_id', $deliveryOrder->branch_id)
            ->where('role', 'branch')->get();
        $branchMsg = "DO ใหม่กำลังมา: {$deliveryOrder->do_number} คาดว่าถึงวันที่ {$scheduledDate}";
        foreach ($branchUsers as $bu) {
            Notification::create([
                'user_id'    => $bu->id,
                'type'       => 'bo_approved',
                'do_id'      => $deliveryOrder->id,
                'message'    => $branchMsg,
                'is_read'    => false,
                'created_at' => now(),
            ]);
        }
        if ($branchUsers->isNotEmpty()) {
            app(PushNotificationService::class)->sendToUsers($branchUsers->pluck('id')->toArray(), 'BH-Logistic', $branchMsg);
        }

        return response()->json(
            $freshDO->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items', 'parentOrder', 'approvedBy'])
        );
    }

    public function rejectBackorder(Request $request, DeliveryOrder $deliveryOrder)
    {
        if (!$deliveryOrder->is_backorder) {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ Backorder'], 422);
        }

        if ($deliveryOrder->sub_status !== 'bo_pending') {
            return response()->json(['message' => 'Backorder นี้ไม่ได้อยู่ในสถานะรอการอนุมัติ'], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $deliveryOrder->update([
            'sub_status'       => 'bo_rejected',
            'rejection_reason' => $request->rejection_reason,
            'status'           => 'cancelled',
        ]);

        $fresh = $deliveryOrder->fresh()->load(['branch', 'driver', 'items', 'parentOrder']);
        $reason = $request->rejection_reason;

        $rejectMsg = "Backorder {$fresh->do_number} ถูกปฏิเสธ: {$reason}";
        if ($fresh->driver_id) {
            Notification::create([
                'user_id'    => $fresh->driver_id,
                'type'       => 'rejection_alert',
                'do_id'      => $fresh->id,
                'message'    => $rejectMsg,
                'is_read'    => false,
                'created_at' => now(),
            ]);
            app(PushNotificationService::class)->sendToUsers([$fresh->driver_id], 'BH-Logistic', $rejectMsg);
        }

        $branchUsers = User::where('branch_id', $fresh->branch_id)
            ->where('role', 'branch')->pluck('id');
        if ($branchUsers->isNotEmpty()) {
            Notification::insert($branchUsers->map(fn($uid) => [
                'user_id'    => $uid,
                'type'       => 'rejection_alert',
                'do_id'      => $fresh->id,
                'message'    => $rejectMsg,
                'is_read'    => false,
                'created_at' => now(),
            ])->all());
            app(PushNotificationService::class)->sendToUsers($branchUsers->toArray(), 'BH-Logistic', $rejectMsg);
        }

        return response()->json($fresh);
    }

    // ─────────────────────────────────────────────────────────────
    // DRIVER — รายการ DO ของฉัน
    // ─────────────────────────────────────────────────────────────

    public function driverOrders(Request $request)
    {
        $orders = DeliveryOrder::with([
                'branch:id,name,lat,lng',
                'driver:id,name,vehicle_plate',
                'items:id,delivery_order_id,stop_id,item_id,product_name,qty_ordered,unit',
                'stops:id,delivery_order_id,branch_id,sequence,status,delivered_at,arrive_photo_path,signature',
                'stops.branch:id,name,lat,lng',
                'stops.items:id,stop_id,delivery_order_id,product_name,qty_ordered,unit',
                'receiveRecord:id,delivery_order_id,signature,received_at',
                'receiveRecord.photos:id,receive_record_id,photo_path',
            ])
            ->where('driver_id', $request->user()->id)
            ->where(function ($q) {
                $q->whereIn('status', ['pending', 'in_transit'])
                  ->orWhere(function ($q2) {
                      $q2->whereIn('status', ['delivered', 'completed'])
                         ->where('updated_at', '>=', now()->subDays(7));
                  });
            })
            ->where(function ($q) {
                $q->where('is_backorder', false)
                  ->orWhere(function ($q2) {
                      $q2->where('is_backorder', true)
                         ->where('sub_status', 'bo_approved');
                  });
            })
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // ─────────────────────────────────────────────────────────────
    // DRIVER — Available orders (no driver assigned yet)
    // ─────────────────────────────────────────────────────────────

    public function availableOrders(Request $request)
    {
        $orders = DeliveryOrder::with([
                'branch:id,name,lat,lng',
                'items:id,delivery_order_id,stop_id,item_id,product_name,qty_ordered,unit',
                'stops:id,delivery_order_id,branch_id,sequence,status',
                'stops.branch:id,name,lat,lng',
                'stops.items:id,stop_id,delivery_order_id,product_name,qty_ordered,unit',
            ])
            ->whereNull('driver_id')
            ->where('status', 'pending')
            ->where('is_backorder', false)
            ->latest()
            ->get();
        return response()->json($orders);
    }

    public function acceptOrder(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->driver_id) {
            return response()->json(['message' => 'ออเดอร์นี้มีคนรับแล้ว'], 422);
        }
        if ($deliveryOrder->status !== 'pending') {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ได้อยู่ในสถานะรอส่ง'], 422);
        }
        $deliveryOrder->update(['driver_id' => $request->user()->id]);
        return response()->json($deliveryOrder->load(['branch', 'items', 'driver', 'stops.branch', 'stops.items']));
    }

    public function departPhoto(Request $request, DeliveryOrder $deliveryOrder)
    {
        $request->validate([
            'photos'   => 'required|array|min:1|max:10',
            'photos.*' => 'required|string',
        ]);
        if ($deliveryOrder->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
        }
        $filenames = [];
        foreach ($request->photos as $b64) {
            if (str_contains($b64, ',')) $b64 = explode(',', $b64)[1];
            $filename = 'depart_' . $deliveryOrder->id . '_' . time() . '_' . uniqid() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put('depart-photos/' . $filename, base64_decode($b64));
            $filenames[] = $filename;
        }
        $deliveryOrder->update(['depart_photo_path' => json_encode($filenames)]);
        return response()->json(['message' => 'บันทึกรูปสำเร็จ', 'filenames' => $filenames]);
    }

    public function notifyArrival(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
        }
        if ($deliveryOrder->status !== 'in_transit') {
            return response()->json(['message' => 'ออเดอร์ไม่ได้อยู่ในสถานะกำลังส่ง'], 422);
        }
        if (!$deliveryOrder->arrived_at) {
            $deliveryOrder->update(['arrived_at' => now()]);
        }

        $deliveryOrder->load(['branch', 'stops.branch']);
        $branchIds = $deliveryOrder->is_multi_stop
            ? $deliveryOrder->stops->pluck('branch_id')->unique()
            : collect([$deliveryOrder->branch_id])->filter();

        $arrivalMsg = "คนขับถึงแล้ว — {$deliveryOrder->do_number} ออกมารับของได้เลย";
        foreach ($branchIds as $branchId) {
            $branchUserIds = User::where('branch_id', $branchId)
                ->where('role', 'branch')->pluck('id');
            if ($branchUserIds->isNotEmpty()) {
                Notification::insert($branchUserIds->map(fn($uid) => [
                    'user_id'    => $uid,
                    'type'       => 'driver_arrival',
                    'do_id'      => $deliveryOrder->id,
                    'message'    => $arrivalMsg,
                    'is_read'    => false,
                    'created_at' => now(),
                ])->all());
                app(PushNotificationService::class)->sendToUsers($branchUserIds->toArray(), 'BH-Logistic', $arrivalMsg);
            }
        }

        return response()->json(['message' => 'แจ้งสาขาเรียบร้อย']);
    }

    public function confirmDelivery(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
        }
        if (!in_array($deliveryOrder->status, ['in_transit', 'delivered'])) {
            return response()->json(['message' => 'ออเดอร์ไม่อยู่ในสถานะที่ยืนยันได้'], 422);
        }
        $stopId = $request->stop_id;
        $isMultiStopDelivery = $stopId && $deliveryOrder->is_multi_stop;

        $request->validate([
            'arrive_photos'   => 'required|array|min:2|max:10',
            'arrive_photos.*' => 'string',
            'signature'       => $isMultiStopDelivery ? 'nullable|string' : 'required|string',
            'stop_id'         => 'nullable|exists:delivery_stops,id',
        ]);

        DB::transaction(function () use ($request, $deliveryOrder, $stopId) {
            if ($stopId && $deliveryOrder->is_multi_stop) {
                // ─── Multi-stop: บันทึก photo/signature ต่อ stop ───
                $deliveryOrder->load('stops');
                $stop = $deliveryOrder->stops->find($stopId);
                if ($stop) {
                    $stopUpdate = ['status' => 'delivered', 'delivered_at' => now()];

                    if ($request->arrive_photos && count($request->arrive_photos) > 0) {
                        $filenames = [];
                        foreach ($request->arrive_photos as $b64) {
                            if (str_contains($b64, ',')) $b64 = explode(',', $b64)[1];
                            $filename = 'arrive_' . $deliveryOrder->id . '_stop' . $stop->id . '_' . time() . '_' . uniqid() . '.jpg';
                            \Illuminate\Support\Facades\Storage::disk('public')->put('arrive-photos/' . $filename, base64_decode($b64));
                            $filenames[] = $filename;
                        }
                        $stopUpdate['arrive_photo_path'] = json_encode($filenames);
                    }
                    if ($request->signature) {
                        $stopUpdate['signature'] = $request->signature;
                    }

                    $stop->update($stopUpdate);
                }

                $allDelivered = $deliveryOrder->fresh()->stops->every(fn($s) => $s->status === 'delivered');
                if ($allDelivered) {
                    $deliveryOrder->update([
                        'status'       => 'completed',
                        'sub_status'   => null,
                        'arrived_at'   => $deliveryOrder->arrived_at ?? now(),
                        'received_at'  => now(),
                        'delivered_at' => $deliveryOrder->delivered_at ?? now(),
                    ]);
                    ReceiveRecord::create([
                        'delivery_order_id' => $deliveryOrder->id,
                        'received_by'       => $request->user()->id,
                        'status'            => 'received_all',
                        'received_at'       => now(),
                    ]);
                }
            } else {
                // ─── Single-stop: บันทึก photo ในออเดอร์, signature ใน ReceiveRecord ───
                if ($request->arrive_photos && count($request->arrive_photos) > 0) {
                    $filenames = [];
                    foreach ($request->arrive_photos as $b64) {
                        if (str_contains($b64, ',')) $b64 = explode(',', $b64)[1];
                        $filename = 'arrive_' . $deliveryOrder->id . '_' . time() . '_' . uniqid() . '.jpg';
                        \Illuminate\Support\Facades\Storage::disk('public')->put('arrive-photos/' . $filename, base64_decode($b64));
                        $filenames[] = $filename;
                    }
                    $deliveryOrder->update(['arrive_photo_path' => json_encode($filenames)]);
                }
                $deliveryOrder->stops()->whereNotIn('status', ['delivered', 'completed'])
                    ->update(['status' => 'delivered', 'delivered_at' => now()]);
                $deliveryOrder->update([
                    'status'       => 'completed',
                    'sub_status'   => null,
                    'arrived_at'   => $deliveryOrder->arrived_at ?? now(),
                    'received_at'  => now(),
                    'delivered_at' => $deliveryOrder->delivered_at ?? now(),
                ]);
                ReceiveRecord::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'received_by'       => $request->user()->id,
                    'status'            => 'received_all',
                    'signature'         => $request->signature ?: null,
                    'received_at'       => now(),
                ]);
            }
        });

        return response()->json($deliveryOrder->fresh()->load(['branch', 'driver', 'items', 'stops.branch', 'stops.items']));
    }

    public function createReorder(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->sub_status !== 'rejected') {
            return response()->json(['message' => 'สร้างออเดอร์ใหม่ได้เฉพาะออเดอร์ที่ถูกปฏิเสธ'], 422);
        }
        $deliveryOrder->load(['branch', 'items']);
        $bo = DeliveryOrder::create([
            'branch_id'      => $deliveryOrder->branch_id,
            'driver_id'      => null,
            'created_by'     => $request->user()->id,
            'scheduled_date' => now()->addDay()->toDateString(),
            'parent_do_id'   => $deliveryOrder->id,
            'is_backorder'   => true,
            'bo_round'       => $deliveryOrder->bo_round + 1,
            'sub_status'     => 'bo_pending',
        ]);
        foreach ($deliveryOrder->items as $item) {
            $qty = ($item->shortage_qty > 0) ? $item->shortage_qty : $item->qty_ordered;
            $bo->items()->create([
                'product_name' => $item->product_name,
                'qty_ordered'  => $qty,
                'unit'         => $item->unit,
                'item_note'    => 'ตีกลับจาก ' . $deliveryOrder->do_number,
            ]);
        }
        $this->notifyHqAdmin('bo_created', $bo->id,
            "สร้าง BO {$bo->do_number} จาก {$deliveryOrder->do_number} ที่ถูกตีกลับ");
        return response()->json($bo->load(['branch', 'items']), 201);
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────────────────────

    private function notifyHqAdmin(string $type, int $doId, string $message): void
    {
        $recipients = User::whereIn('role', ['admin', 'hq'])->pluck('id');

        Notification::insert($recipients->map(fn($uid) => [
            'user_id'    => $uid,
            'type'       => $type,
            'do_id'      => $doId,
            'message'    => $message,
            'is_read'    => false,
            'created_at' => now(),
        ])->all());

        app(PushNotificationService::class)->sendToUsers($recipients->toArray(), 'BH-Logistic', $message);
    }
}
