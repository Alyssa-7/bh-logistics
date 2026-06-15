<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryOrder;
use App\Models\Item;
use App\Models\Notification;
use App\Models\ReceiveRecord;
use App\Models\ShortageLog;
use App\Models\User;
use App\Models\Vehicle;
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
        $query = DeliveryOrder::with(['branch', 'vehicle.driver', 'creator', 'items']);

        if ($request->has('status')) {
            if ($request->status === 'in_delivery') {
                $query->whereIn('status', ['in_transit', 'delivered']);
            } elseif ($request->status === 'problem') {
                $query->where(function ($q) {
                    $q->where('status', 'problem')
                      ->orWhereIn('sub_status', ['incomplete', 'rejected', 'bo_pending']);
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function show(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($request->user()->role === 'driver') {
            $vehicle = Vehicle::where('driver_id', $request->user()->id)->first();
            if (!$vehicle || $deliveryOrder->vehicle_id !== $vehicle->id) {
                return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง DO นี้'], 403);
            }
            if ($deliveryOrder->is_backorder && $deliveryOrder->sub_status !== 'bo_approved') {
                return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง DO นี้'], 403);
            }
        }

        return response()->json(
            $deliveryOrder->load([
                'branch', 'vehicle.driver', 'creator', 'items',
                'receiveRecord.photos', 'approvedBy',
                'backorders.items', 'shortageLogs',
            ])
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'vehicle_id'         => 'nullable|exists:vehicles,id',
            'scheduled_date'     => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'required|exists:items,id',
            'items.*.qty_ordered'=> 'required|numeric|min:0.01',
        ]);

        $itemMaster = Item::whereIn('id', collect($request->items)->pluck('item_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        if ($itemMaster->count() !== count($request->items)) {
            return response()->json(['message' => 'สินค้าบางรายการไม่พร้อมใช้งาน'], 422);
        }

        $order = DeliveryOrder::create([
            'branch_id'      => $request->branch_id,
            'vehicle_id'     => $request->vehicle_id,
            'created_by'     => $request->user()->id,
            'scheduled_date' => $request->scheduled_date,
        ]);

        foreach ($request->items as $row) {
            $master = $itemMaster[$row['item_id']];
            $order->items()->create([
                'item_id'      => $master->id,
                'product_name' => $master->name,
                'unit'         => $master->unit,
                'qty_ordered'  => $row['qty_ordered'],
            ]);
        }

        $order->load('branch');

        if ($request->vehicle_id) {
            $vehicle = Vehicle::find($request->vehicle_id);
            if ($vehicle && $vehicle->driver_id) {
                $scheduledDate = $order->scheduled_date?->format('d/m/Y') ?? '-';
                Notification::create([
                    'user_id'    => $vehicle->driver_id,
                    'type'       => 'do_assigned',
                    'do_id'      => $order->id,
                    'message'    => "มีออเดอร์ใหม่: {$order->do_number} ส่ง {$order->branch?->name} วันที่ {$scheduledDate}",
                    'is_read'    => false,
                    'created_at' => now(),
                ]);
            }
        }

        return response()->json($order->load(['branch', 'items']), 201);
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $request->validate([
            'branch_id'      => 'sometimes|exists:branches,id',
            'vehicle_id'     => 'sometimes|nullable|exists:vehicles,id',
            'scheduled_date' => 'sometimes|date',
            'status'         => 'sometimes|in:pending,in_transit,delivered,cancelled',
        ]);

        if (in_array($deliveryOrder->status, ['completed', 'cancelled'])) {
            return response()->json(['message' => 'ไม่สามารถแก้ไข DO ที่เสร็จสิ้นหรือยกเลิกแล้ว'], 422);
        }

        $data = $request->only(['branch_id', 'vehicle_id', 'scheduled_date', 'status']);

        if (isset($data['status']) && $data['status'] === 'in_transit' && !$deliveryOrder->dispatched_at) {
            $data['dispatched_at'] = now();
        }

        if (isset($data['status']) && $data['status'] === 'delivered') {
            $data['delivered_at'] = now();
        }

        $oldVehicleId = $deliveryOrder->vehicle_id;
        $deliveryOrder->update($data);

        if (!empty($data['vehicle_id']) && $data['vehicle_id'] != $oldVehicleId) {
            $vehicle = Vehicle::find($data['vehicle_id']);
            if ($vehicle && $vehicle->driver_id) {
                $deliveryOrder->load('branch');
                $scheduledDate = $deliveryOrder->scheduled_date?->format('d/m/Y') ?? '-';
                Notification::create([
                    'user_id'    => $vehicle->driver_id,
                    'type'       => 'do_assigned',
                    'do_id'      => $deliveryOrder->id,
                    'message'    => "มีออเดอร์ใหม่: {$deliveryOrder->do_number} ส่ง {$deliveryOrder->branch?->name} วันที่ {$scheduledDate}",
                    'is_read'    => false,
                    'created_at' => now(),
                ]);
            }
        }

        return response()->json($deliveryOrder->load(['branch', 'vehicle', 'items']));
    }

    public function dispatch(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($request->user()->role === 'driver') {
            $vehicle = Vehicle::where('driver_id', $request->user()->id)->first();
            if (!$vehicle || $deliveryOrder->vehicle_id !== $vehicle->id) {
                return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
            }
        }

        if ($deliveryOrder->status !== 'pending') {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ได้อยู่ในสถานะรอส่ง'], 422);
        }

        $deliveryOrder->update([
            'status'       => 'in_transit',
            'dispatched_at' => $deliveryOrder->dispatched_at ?? now(),
        ]);

        return response()->json($deliveryOrder->load(['branch', 'vehicle', 'items']));
    }

    public function arrive(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($request->user()->role === 'driver') {
            $vehicle = Vehicle::where('driver_id', $request->user()->id)->first();
            if (!$vehicle || $deliveryOrder->vehicle_id !== $vehicle->id) {
                return response()->json(['message' => 'ออเดอร์นี้ไม่ใช่ของคุณ'], 403);
            }
        }

        if ($deliveryOrder->status !== 'in_transit') {
            return response()->json(['message' => 'ออเดอร์นี้ไม่ได้อยู่ในสถานะกำลังส่ง'], 422);
        }

        $deliveryOrder->update([
            'status'       => 'delivered',
            'arrived_at'   => now(),
            'delivered_at' => now(),
        ]);

        return response()->json($deliveryOrder->load(['branch', 'vehicle', 'items']));
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

        return response()->json($deliveryOrder->load(['branch', 'vehicle', 'items']));
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

        $request->validate([
            'action'           => 'required|in:confirm,reject',
            'signature'        => 'nullable|string',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:1000',
            'items'            => 'required_if:action,confirm|array',
            'items.*.id'       => [
                'required_if:action,confirm',
                Rule::exists('delivery_items', 'id')
                    ->where('delivery_order_id', $deliveryOrder->id),
            ],
            'items.*.qty_received' => 'required_if:action,confirm|numeric|min:0',
            'items.*.item_note'    => 'nullable|string|max:500',
        ]);

        $deliveryOrder->load(['items', 'branch', 'vehicle']);

        DB::transaction(function () use ($request, $deliveryOrder) {
            $branchName = $deliveryOrder->branch->name ?? 'สาขา';

            // ── REJECT ────────────────────────────────────────────
            if ($request->action === 'reject') {
                $deliveryOrder->update([
                    'status'           => 'problem',
                    'sub_status'       => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'received_at'      => now(),
                ]);

                ReceiveRecord::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'received_by'       => $request->user()->id,
                    'status'            => 'rejected',
                    'signature'         => $request->signature ?: null,
                    'received_at'       => now(),
                ]);

                $msg = "Branch {$branchName} ปฏิเสธรับ {$deliveryOrder->do_number} เหตุผล: {$request->rejection_reason}";
                $this->notifyHqAdmin('rejection_alert', $deliveryOrder->id, $msg);

                $driverId = $deliveryOrder->vehicle?->driver_id;
                if ($driverId) {
                    Notification::create([
                        'user_id'    => $driverId,
                        'type'       => 'rejection_alert',
                        'do_id'      => $deliveryOrder->id,
                        'message'    => "Branch ปฏิเสธรับ {$deliveryOrder->do_number} เหตุผล: {$request->rejection_reason}",
                        'is_read'    => false,
                        'created_at' => now(),
                    ]);
                }

                if ($deliveryOrder->bo_round < 3) {
                    $bo = DeliveryOrder::create([
                        'branch_id'      => $deliveryOrder->branch_id,
                        'vehicle_id'     => null,
                        'created_by'     => $request->user()->id,
                        'scheduled_date' => $deliveryOrder->scheduled_date,
                        'parent_do_id'   => $deliveryOrder->id,
                        'is_backorder'   => true,
                        'bo_round'       => $deliveryOrder->bo_round + 1,
                        'sub_status'     => 'bo_pending',
                    ]);
                    foreach ($deliveryOrder->items as $item) {
                        $bo->items()->create([
                            'product_name' => $item->product_name,
                            'qty_ordered'  => $item->qty_ordered,
                            'unit'         => $item->unit,
                            'item_note'    => 'Backorder จาก ' . $deliveryOrder->do_number,
                        ]);
                    }
                    $this->notifyHqAdmin('bo_created', $bo->id,
                        "{$deliveryOrder->do_number} มีปัญหา ระบบสร้าง BO {$bo->do_number} รอยืนยัน");
                }
                return;
            }

            // ── CONFIRM ───────────────────────────────────────────
            $shortageItems = [];
            $noneReceived  = collect($request->items)->every(fn($d) => ($d['qty_received'] ?? 0) == 0);

            foreach ($request->items as $data) {
                $item     = $deliveryOrder->items->find($data['id']);
                $shortage = max(0, ($item->qty_ordered ?? 0) - $data['qty_received']);

                if ($shortage > 0) {
                    $shortageItems[] = ['item' => $item, 'shortage' => $shortage];
                }

                $item->update([
                    'qty_received' => $data['qty_received'],
                    'shortage_qty' => $shortage,
                    'item_note'    => $data['item_note'] ?? null,
                ]);
            }

            $hasShortage = count($shortageItems) > 0;

            // qty = 0 ทุกรายการ → ปฏิเสธรับทั้งหมด
            if ($noneReceived) {
                $deliveryOrder->update([
                    'status'      => 'problem',
                    'sub_status'  => 'rejected',
                    'received_at' => now(),
                ]);
                ReceiveRecord::create([
                    'delivery_order_id' => $deliveryOrder->id,
                    'received_by'       => $request->user()->id,
                    'status'            => 'rejected',
                    'signature'         => $request->signature ?: null,
                    'received_at'       => now(),
                ]);
                $this->notifyHqAdmin('rejection_alert', $deliveryOrder->id,
                    "Branch {$branchName} ปฏิเสธรับสินค้าทั้งหมด {$deliveryOrder->do_number}");

                if ($deliveryOrder->bo_round < 3) {
                    $bo = DeliveryOrder::create([
                        'branch_id'      => $deliveryOrder->branch_id,
                        'vehicle_id'     => null,
                        'created_by'     => $request->user()->id,
                        'scheduled_date' => $deliveryOrder->scheduled_date,
                        'parent_do_id'   => $deliveryOrder->id,
                        'is_backorder'   => true,
                        'bo_round'       => $deliveryOrder->bo_round + 1,
                        'sub_status'     => 'bo_pending',
                    ]);
                    foreach ($shortageItems as ['item' => $item, 'shortage' => $shortage]) {
                        $bo->items()->create([
                            'product_name' => $item->product_name,
                            'qty_ordered'  => $shortage,
                            'unit'         => $item->unit,
                            'item_note'    => 'Backorder จาก ' . $deliveryOrder->do_number,
                        ]);
                    }
                    $this->notifyHqAdmin('bo_created', $bo->id,
                        "{$deliveryOrder->do_number} มีปัญหา ระบบสร้าง BO {$bo->do_number} รอยืนยัน");
                }
                return;
            }

            // รับครบ → completed
            if (!$hasShortage) {
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
                return;
            }

            // รับบางส่วน → problem / incomplete
            $deliveryOrder->update([
                'status'      => 'problem',
                'sub_status'  => 'incomplete',
                'received_at' => now(),
            ]);

            ReceiveRecord::create([
                'delivery_order_id' => $deliveryOrder->id,
                'received_by'       => $request->user()->id,
                'status'            => 'partial',
                'signature'         => $request->signature ?: null,
                'received_at'       => now(),
            ]);

            foreach ($shortageItems as ['item' => $item, 'shortage' => $shortage]) {
                ShortageLog::create([
                    'do_id'        => $deliveryOrder->id,
                    'item_id'      => $item->id,
                    'branch_id'    => $deliveryOrder->branch_id,
                    'shortage_qty' => $shortage,
                ]);
            }

            if ($deliveryOrder->bo_round < 3) {
                $bo = DeliveryOrder::create([
                    'branch_id'      => $deliveryOrder->branch_id,
                    'vehicle_id'     => null,
                    'created_by'     => $request->user()->id,
                    'scheduled_date' => $deliveryOrder->scheduled_date,
                    'parent_do_id'   => $deliveryOrder->id,
                    'is_backorder'   => true,
                    'bo_round'       => $deliveryOrder->bo_round + 1,
                    'sub_status'     => 'bo_pending',
                ]);
                foreach ($shortageItems as ['item' => $item, 'shortage' => $shortage]) {
                    $bo->items()->create([
                        'product_name' => $item->product_name,
                        'qty_ordered'  => $shortage,
                        'unit'         => $item->unit,
                        'item_note'    => 'Backorder จาก ' . $deliveryOrder->do_number,
                    ]);
                }
                $total = array_sum(array_column($shortageItems, 'shortage'));
                $this->notifyHqAdmin('bo_created', $bo->id,
                    "{$deliveryOrder->do_number} มีปัญหา (ขาด {$total} หน่วย) ระบบสร้าง BO {$bo->do_number} รอยืนยัน");
            }
        });

        return response()->json(
            $deliveryOrder->fresh()->load(['branch', 'vehicle', 'items', 'backorders'])
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

        $driver  = User::where('id', $request->driver_id)->where('role', 'driver')->firstOrFail();
        $vehicle = Vehicle::where('driver_id', $driver->id)->first();

        if (!$vehicle) {
            return response()->json(['message' => 'คนขับนี้ยังไม่มียานพาหนะที่ผูกไว้ในระบบ'], 422);
        }

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
            'vehicle_id'  => $vehicle->id,
            'approved_by' => $request->user()->id,
        ];
        if ($request->filled('scheduled_date')) {
            $updateData['scheduled_date'] = $request->scheduled_date;
        }
        $deliveryOrder->update($updateData);

        $freshDO       = $deliveryOrder->fresh();
        $scheduledDate = $freshDO->scheduled_date?->format('d/m/Y') ?? '-';

        Notification::create([
            'user_id'    => $driver->id,
            'type'       => 'bo_assigned',
            'do_id'      => $deliveryOrder->id,
            'message'    => "คุณได้รับ Backorder ใหม่: {$deliveryOrder->do_number} (รอบ {$deliveryOrder->bo_round}) คาดว่าถึงวันที่ {$scheduledDate}",
            'is_read'    => false,
            'created_at' => now(),
        ]);

        $branchUsers = User::where('branch_id', $deliveryOrder->branch_id)
            ->where('role', 'branch')->get();
        foreach ($branchUsers as $bu) {
            Notification::create([
                'user_id'    => $bu->id,
                'type'       => 'bo_approved',
                'do_id'      => $deliveryOrder->id,
                'message'    => "DO ใหม่กำลังมา: {$deliveryOrder->do_number} คาดว่าถึงวันที่ {$scheduledDate}",
                'is_read'    => false,
                'created_at' => now(),
            ]);
        }

        return response()->json(
            $freshDO->load(['branch', 'vehicle.driver', 'items', 'parentOrder', 'approvedBy'])
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

        return response()->json(
            $deliveryOrder->fresh()->load(['branch', 'vehicle', 'items', 'parentOrder'])
        );
    }

    // ─────────────────────────────────────────────────────────────
    // DRIVER — รายการ DO ของฉัน
    // ─────────────────────────────────────────────────────────────

    public function driverOrders(Request $request)
    {
        $vehicleId = Vehicle::where('driver_id', $request->user()->id)->value('id');
        if (!$vehicleId) {
            return response()->json([]);
        }

        $orders = DeliveryOrder::with([
                'branch:id,name',
                'vehicle:id,license_plate',
                'items:id,delivery_order_id,item_id,product_name,qty_ordered,unit',
            ])
            ->where('vehicle_id', $vehicleId)
            ->where(function ($q) {
                // Active orders always visible
                $q->whereIn('status', ['pending', 'in_transit'])
                  // Completed/delivered within last 7 days
                  ->orWhere(function ($q2) {
                      $q2->whereIn('status', ['delivered', 'completed', 'problem'])
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
    // HELPER
    // ─────────────────────────────────────────────────────────────

    private function notifyHqAdmin(string $type, int $doId, string $message): void
    {
        $recipients    = User::whereIn('role', ['admin', 'hq'])->pluck('id');
        $notifications = $recipients->map(fn($uid) => [
            'user_id'    => $uid,
            'type'       => $type,
            'do_id'      => $doId,
            'message'    => $message,
            'is_read'    => false,
            'created_at' => now(),
        ])->all();

        Notification::insert($notifications);
    }
}
