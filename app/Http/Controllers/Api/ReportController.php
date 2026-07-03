<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ─── DELIVERY STATS (new comprehensive report) ───────────────
    public function deliveryStats(Request $request)
    {
        $request->validate([
            'from'    => 'nullable|date',
            'to'      => 'nullable|date|after_or_equal:from',
            'year'    => 'nullable|integer|min:2000|max:2100',
            'month'   => 'nullable|integer|min:1|max:12',
            'quarter' => 'nullable|integer|min:1|max:4',
        ]);

        $year = (int) $request->input('year', now()->year);
        $highlightMonths = [];

        if ($request->filled('from') && $request->filled('to')) {
            $dateFrom = Carbon::parse($request->from)->startOfDay();
            $dateTo   = Carbon::parse($request->to)->endOfDay();
            $year     = $dateFrom->year;
        } elseif ($request->filled('month')) {
            $dateFrom        = Carbon::create($year, $request->month, 1)->startOfMonth();
            $dateTo          = $dateFrom->copy()->endOfMonth();
            $highlightMonths = [(int) $request->month];
        } elseif ($request->filled('quarter')) {
            $q          = (int) $request->quarter;
            $startMonth = ($q - 1) * 3 + 1;
            $dateFrom   = Carbon::create($year, $startMonth, 1)->startOfMonth();
            $dateTo     = Carbon::create($year, min($startMonth + 2, 12), 1)->endOfMonth();
            $highlightMonths = [$startMonth, $startMonth + 1, $startMonth + 2];
        } else {
            $dateFrom = Carbon::create($year, 1, 1)->startOfYear();
            $dateTo   = Carbon::create($year, 12, 31)->endOfYear();
        }

        // ─── CARDS ────────────────────────────────────────────────
        $orders = DeliveryOrder::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('is_backorder', false)
            ->get(['id', 'status', 'sub_status']);

        $completed   = $orders->where('status', 'completed')->count();
        $total       = $orders->count();
        $inTransit   = $orders->where('status', 'in_transit')->count();
        $pending     = $orders->where('status', 'pending')->count();
        $cancelled   = $orders->where('status', 'cancelled')->count();
        $delivered   = $orders->where('status', 'delivered')->count();
        $successRate = $total > 0 ? round($completed / $total * 100, 1) : 0;

        // ─── BY BRANCH (returns) ──────────────────────────────────
        $byBranch = DB::table('branches as b')
            ->leftJoin('return_requests as r', function ($join) use ($dateFrom, $dateTo) {
                $join->on('r.branch_id', '=', 'b.id')
                     ->whereBetween('r.created_at', [$dateFrom, $dateTo]);
            })
            ->select('b.id', 'b.name', DB::raw('COUNT(r.id) as return_count'))
            ->groupBy('b.id', 'b.name')
            ->orderByDesc(DB::raw('COUNT(r.id)'))
            ->get()
            ->map(fn ($r) => [
                'id'           => (int) $r->id,
                'name'         => $r->name,
                'return_count' => (int) $r->return_count,
            ])
            ->values();

        // ─── BRANCH RETURN DETAIL (สินค้า + สาเหตุ รายสาขา) ─────────
        $returnDetails = DB::table('return_requests as r')
            ->join('branches as b', 'b.id', '=', 'r.branch_id')
            ->whereBetween('r.created_at', [$dateFrom, $dateTo])
            ->select('r.branch_id', 'b.name as branch_name', 'r.product_name', 'r.reason',
                     DB::raw('COUNT(*) as cnt'))
            ->groupBy('r.branch_id', 'b.name', 'r.product_name', 'r.reason')
            ->orderBy('r.branch_id')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->get();

        $branchReturnDetail = $returnDetails->groupBy('branch_id')->map(function ($rows) {
            $products = $rows->groupBy('product_name')->map(fn ($g) => [
                'name'  => $g->first()->product_name,
                'count' => $g->sum('cnt'),
            ])->sortByDesc('count')->values();

            $reasons = $rows->groupBy('reason')->map(fn ($g) => [
                'reason' => $g->first()->reason,
                'count'  => $g->sum('cnt'),
            ])->sortByDesc('count')->values();

            return [
                'branch_id'   => (int) $rows->first()->branch_id,
                'branch_name' => $rows->first()->branch_name,
                'products'    => $products,
                'reasons'     => $reasons,
            ];
        })->values();

        // ─── TREND (12 months of selected year) ───────────────────
        $trendRaw = DB::table('delivery_orders')
            ->select(
                DB::raw('MONTH(created_at) as m'),
                DB::raw("SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled")
            )
            ->whereYear('created_at', $year)
            ->where('is_backorder', false)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('m');

        $thMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                         'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        $trend = collect(range(1, 12))->map(fn ($m) => [
            'month'     => $m,
            'label'     => $thMonths[$m],
            'completed' => (int) ($trendRaw[$m]->completed ?? 0),
            'cancelled' => (int) ($trendRaw[$m]->cancelled ?? 0),
            'highlight' => in_array($m, $highlightMonths),
        ])->values();

        // ─── RETURN REQUESTS (ตีกลับ) ─────────────────────────────
        $returnCount = DB::table('return_requests')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $returnByProduct = DB::table('return_requests')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('product_name', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'product_name' => $r->product_name,
                'count'        => (int) $r->cnt,
                'top_reason'   => null,
            ]);

        $returnByReason = DB::table('return_requests')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('reason', DB::raw('COUNT(*) as cnt'))
            ->groupBy('reason')
            ->orderByDesc('cnt')
            ->get()
            ->map(fn ($r) => [
                'product_name' => $r->reason,
                'count'        => (int) $r->cnt,
                'top_reason'   => null,
            ]);

        // ─── TIMING (SECOND precision) ────────────────────────────
        $timing = DB::table('delivery_orders')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->whereNotNull('dispatched_at')
            ->whereNotNull('arrived_at')
            ->whereNotNull('received_at')
            ->selectRaw('
                ROUND(AVG(TIMESTAMPDIFF(SECOND, created_at, dispatched_at)), 0) as avg_prep,
                ROUND(AVG(TIMESTAMPDIFF(SECOND, dispatched_at, arrived_at)),  0) as avg_drive,
                ROUND(AVG(TIMESTAMPDIFF(SECOND, arrived_at,   received_at)),  0) as avg_receive,
                COUNT(*) as completed_count
            ')
            ->first();

        $fastestHour = DB::table('delivery_orders')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->whereNotNull('received_at')
            ->selectRaw('HOUR(received_at) as hr, COUNT(*) as cnt')
            ->groupBy('hr')
            ->orderByDesc('cnt')
            ->first();

        // ─── DELIVERY FUNNEL ──────────────────────────────────────
        $funnelDispatched = DB::table('delivery_orders')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('is_backorder', false)
            ->whereNotNull('dispatched_at')
            ->count();

        $funnelArrived = DB::table('delivery_orders')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('is_backorder', false)
            ->whereNotNull('arrived_at')
            ->count();

        $funnelReceived = DB::table('delivery_orders')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('is_backorder', false)
            ->whereNotNull('received_at')
            ->count();

        // ─── PER-DRIVER TIMING ────────────────────────────────────
        $driverTiming = DB::table('delivery_orders as d')
            ->join('users as u', 'u.id', '=', 'd.driver_id')
            ->whereBetween('d.created_at', [$dateFrom, $dateTo])
            ->where('d.status', 'completed')
            ->whereNotNull('d.dispatched_at')
            ->whereNotNull('d.arrived_at')
            ->whereNotNull('d.received_at')
            ->select(
                'u.name',
                DB::raw('COUNT(d.id) as total'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(SECOND, d.dispatched_at, d.arrived_at)), 0) as avg_drive_sec'),
                DB::raw('ROUND(AVG(TIMESTAMPDIFF(SECOND, d.created_at,    d.received_at)), 0) as avg_total_sec')
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc(DB::raw('COUNT(d.id)'))
            ->get()
            ->map(fn ($r) => [
                'name'          => $r->name,
                'total'         => (int) $r->total,
                'avg_drive_sec' => $r->avg_drive_sec !== null ? (int) $r->avg_drive_sec : null,
                'avg_total_sec' => $r->avg_total_sec !== null ? (int) $r->avg_total_sec : null,
            ]);

        return response()->json([
            'filter' => [
                'from'             => $dateFrom->toDateString(),
                'to'               => $dateTo->toDateString(),
                'year'             => $year,
                'highlight_months' => $highlightMonths,
            ],
            'cards' => [
                'total'              => $total,
                'completed'          => $completed,
                'delivered'          => $delivered,
                'in_transit'         => $inTransit,
                'pending'            => $pending,
                'cancelled'          => $cancelled,
                'return_count'       => $returnCount,
                'success_rate'       => $successRate,
                'complete_rate'      => $total > 0 ? round(($completed + $delivered) / $total * 100, 1) : 0,
                'cancel_rate'        => $total > 0 ? round($cancelled / $total * 100, 1) : 0,
            ],
            'trend'              => $trend,
            'by_branch'          => $byBranch,
            'branch_return_detail' => $branchReturnDetail,
            'return_by_product'  => $returnByProduct,
            'return_by_reason'   => $returnByReason,
            'timing' => [
                'avg_prep_sec'    => $timing?->avg_prep    !== null ? (int) $timing->avg_prep    : null,
                'avg_drive_sec'   => $timing?->avg_drive   !== null ? (int) $timing->avg_drive   : null,
                'avg_receive_sec' => $timing?->avg_receive !== null ? (int) $timing->avg_receive : null,
                'fastest_hour'    => $fastestHour?->hr,
                'completed_count' => $timing?->completed_count ? (int) $timing->completed_count : 0,
            ],
            'funnel' => [
                'created'    => $total,
                'dispatched' => $funnelDispatched,
                'arrived'    => $funnelArrived,
                'received'   => $funnelReceived,
            ],
            'driver_timing' => $driverTiming,
        ]);
    }

    // ─── HELPER ───────────────────────────────────────────────────
    private function topReason(?string $reasons): ?string
    {
        if (!$reasons) return null;
        $parts = array_filter(explode('|||', $reasons));
        if (!$parts) return null;
        $counts = array_count_values($parts);
        arsort($counts);
        return array_key_first($counts);
    }

    // ─── LEGACY endpoints (kept for backward compat) ──────────────
    public function monthly(Request $request)
    {
        $year  = $request->input('year',  now()->year);
        $month = $request->input('month', now()->month);

        $orders = DeliveryOrder::with(['branch', 'items'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        return response()->json([
            'year'         => $year,
            'month'        => $month,
            'total_orders' => $orders->count(),
            'by_status'    => $orders->groupBy('status')->map->count(),
            'by_branch'    => $orders->groupBy('branch.name')->map->count(),
        ]);
    }

    public function deliverySummary(Request $request)
    {
        $base = DeliveryOrder::query();
        if ($request->from) $base->whereDate('created_at', '>=', $request->from);
        if ($request->to)   $base->whereDate('created_at', '<=', $request->to);

        // COUNT at DB level — no full table load
        $counts = (clone $base)->selectRaw("
            COUNT(*) as total,
            SUM(status = 'in_transit') as in_transit,
            SUM(status = 'delivered')  as delivered,
            SUM(status = 'pending')    as pending,
            SUM(status = 'cancelled')  as cancelled,
            SUM(status = 'completed')  as completed
        ")->first();

        // Only fetch in_transit rows (dashboard table only shows these)
        $transitOrders = (clone $base)
            ->with(['branch:id,name,lat,lng', 'driver:id,name', 'items:id,delivery_order_id,product_name,qty_ordered,unit'])
            ->where('status', 'in_transit')
            ->latest()
            ->get();

        return response()->json([
            'total'      => (int) $counts->total,
            'delivered'  => (int) $counts->delivered,
            'completed'  => (int) $counts->completed,
            'in_transit' => (int) $counts->in_transit,
            'pending'    => (int) $counts->pending,
            'cancelled'  => (int) $counts->cancelled,
            'orders'     => $transitOrders->values()->toArray(),
        ]);
    }
}
