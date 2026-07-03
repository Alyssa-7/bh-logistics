<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\ReceiveRecord;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReceiveController extends Controller
{
    public function scan(string $qrCode)
    {
        $order = DeliveryOrder::with([
                'branch', 'driver', 'items', 'receiveRecord',
                'stops.branch', 'stops.items',
            ])
            ->where('qr_code', $qrCode)
            ->firstOrFail();

        $order->items->transform(function ($item) {
            $shortage = $item->qty_ordered - $item->qty_received;
            $item->qty_shortage    = $shortage > 0 ? $shortage : 0;
            $item->shortage_status = $shortage > 0 ? 'ขาด' : 'ครบ';
            return $item;
        });

        return response()->json($order);
    }

    public function receive(Request $request, DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->receiveRecord) {
            return response()->json(['message' => 'This order has already been received.'], 422);
        }

        if ($deliveryOrder->status !== 'in_transit') {
            return response()->json(['message' => 'Order is not in transit.'], 422);
        }

        $request->validate([
            'items'                    => 'required|array|min:1',
            'items.*.id'               => ['required', Rule::exists('delivery_items', 'id')->where('delivery_order_id', $deliveryOrder->id)],
            'items.*.qty_received'     => 'required|numeric|min:0',
            'items.*.shortage_reason'  => 'nullable|string|max:500',
            'signature'                => 'nullable|string',
        ]);

        $deliveryOrder->load('items');

        $record = DB::transaction(function () use ($request, $deliveryOrder) {
            $record = ReceiveRecord::create([
                'delivery_order_id' => $deliveryOrder->id,
                'received_by'       => $request->user()->id,
                'status'            => 'received_all',
                'signature'         => $request->signature ?: null,
                'received_at'       => now(),
            ]);

            foreach ($request->items as $item) {
                $ordered = $deliveryOrder->items->find($item['id'])?->qty_ordered ?? 0;
                $deliveryOrder->items()->where('id', $item['id'])->update([
                    'qty_received' => $item['qty_received'] ?? $ordered,
                    'shortage_qty' => 0,
                ]);
            }

            $deliveryOrder->update([
                'status'       => 'completed',
                'sub_status'   => null,
                'arrived_at'   => now(),
                'received_at'  => now(),
                'delivered_at' => $deliveryOrder->delivered_at ?? now(),
            ]);

            return $record;
        });

        return response()->json($record->load('photos'), 201);
    }

    public function signature(Request $request, DeliveryOrder $deliveryOrder)
    {
        $record = $deliveryOrder->receiveRecord;

        if (! $record) {
            return response()->json(['message' => 'No receive record found.'], 404);
        }

        $request->validate([
            'signature' => 'required|string',
        ]);

        $record->update(['signature' => $request->signature]);

        return response()->json($record);
    }

    public function photos(Request $request, DeliveryOrder $deliveryOrder)
    {
        $record = $deliveryOrder->receiveRecord;

        if (! $record) {
            return response()->json(['message' => 'No receive record found.'], 404);
        }

        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|file|image|max:5120',
        ]);

        $uploaded = [];
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('evidence', 'public');
            $uploaded[] = $record->photos()->create([
                'photo_path' => $path,
                'uploaded_at' => now(),
            ]);
        }

        return response()->json($uploaded, 201);
    }

    public function serveEvidence(string $filename)
    {
        $path = storage_path('app/public/evidence/' . $filename);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function branchOrders(Request $request)
    {
        $user = $request->user();

        $query = DeliveryOrder::with([
                'items', 'driver', 'branch', 'receiveRecord',
                'stops.branch', 'stops.items',
            ])
            ->orderBy('created_at', 'desc');

        if (!in_array($user->role, ['admin', 'hq'])) {
            // Branch sees all DOs where it is either the main branch or one of the stops
            $branchId = $user->branch_id;
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereHas('stops', fn($sq) => $sq->where('branch_id', $branchId));
            })->whereNotIn('status', ['cancelled']);
        } else {
            $query->whereIn('status', ['in_transit', 'delivered']);
        }

        $orders = $query->get();

        $branchId = in_array($user->role, ['admin', 'hq']) ? null : $user->branch_id;

        $orders->each(function ($order) use ($branchId) {
            // For multi-stop orders, expose which stop belongs to this branch
            if ($branchId && $order->is_multi_stop) {
                $myStop = $order->stops->firstWhere('branch_id', $branchId);
                $order->my_stop = $myStop;
            }

            $order->items->transform(function ($item) {
                $shortage              = $item->qty_ordered - $item->qty_received;
                $item->qty_shortage    = $shortage > 0 ? $shortage : 0;
                $item->shortage_status = $shortage > 0 ? 'ขาด' : 'ครบ';
                return $item;
            });
        });

        return response()->json($orders);
    }
}
