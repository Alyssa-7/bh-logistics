<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) $request->input('year',  now()->year);
        $month = (int) $request->input('month', now()->month);

        $from = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $to   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $user = $request->user();

        $query = DB::table('delivery_orders as d')
            ->join('branches as b', 'b.id', '=', 'd.branch_id')
            ->leftJoin('users as u', 'u.id', '=', 'd.driver_id')
            ->whereBetween('d.scheduled_date', [$from, $to])
            ->where('d.is_backorder', false);

        // Role-based filtering
        if ($user->role === 'branch') {
            $query->where('d.branch_id', $user->branch_id);
        } elseif ($user->role === 'driver') {
            $query->where('d.driver_id', $user->id);
        } elseif ($user->role === 'hq' && $request->filled('branch_id')) {
            $query->where('d.branch_id', (int) $request->branch_id);
        }

        $orders = $query->select(
            'd.id', 'd.do_number', 'd.status', 'd.scheduled_date',
            'd.branch_id', 'b.name as branch_name',
            'd.driver_id', 'u.name as driver_name',
            DB::raw('(SELECT GROUP_CONCAT(CONCAT(product_name, " (", CAST(qty_ordered AS UNSIGNED), " ชิ้น)") ORDER BY id SEPARATOR ", ") FROM delivery_items WHERE delivery_order_id = d.id) as product_summary'),
            DB::raw('(SELECT COUNT(*) FROM delivery_items WHERE delivery_order_id = d.id) as item_count')
        )->orderBy('d.scheduled_date')->get();

        // Group by date
        $byDate = $orders->groupBy(fn ($o) => $o->scheduled_date);

        $days = $byDate->map(fn ($dayOrders, $date) => [
            'date'       => $date,
            'total'      => $dayOrders->count(),
            'completed'  => $dayOrders->whereIn('status', ['completed', 'delivered'])->count(),
            'in_transit' => $dayOrders->where('status', 'in_transit')->count(),
            'pending'    => $dayOrders->where('status', 'pending')->count(),
            'cancelled'  => $dayOrders->where('status', 'cancelled')->count(),
            'orders'     => $dayOrders->map(fn ($o) => [
                'id'              => $o->id,
                'do_number'       => $o->do_number,
                'status'          => $o->status,
                'branch_name'     => $o->branch_name,
                'driver_name'     => $o->driver_name ?? 'ยังไม่กำหนด',
                'product_summary' => $o->product_summary ?? '',
                'item_count'      => (int) ($o->item_count ?? 0),
            ])->values(),
        ])->values();

        $total     = $orders->count();
        $completed = $orders->whereIn('status', ['completed', 'delivered'])->count();

        return response()->json([
            'year'    => $year,
            'month'   => $month,
            'summary' => [
                'total'     => $total,
                'completed' => $completed,
                'days'      => $byDate->count(),
            ],
            'days' => $days,
        ]);
    }
}
