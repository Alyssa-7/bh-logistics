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

        $completed  = $orders->where('status', 'completed')->count();
        $problem    = $orders->where('status', 'problem')->count();
        $incomplete = $orders->where('sub_status', 'incomplete')->count();
        $rejected   = $orders->where('sub_status', 'rejected')->count();
        $total      = $orders->count();
        $successRate = $total > 0 ? round($completed / $total * 100, 1) : 0;

        $boCount = DeliveryOrder::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('is_backorder', true)
            ->whereIn('status', ['in_transit', 'delivered', 'completed'])
            ->count();

        // ─── TREND (12 months of selected year) ───────────────────
        $trendRaw = DB::table('delivery_orders')
            ->select(
                DB::raw('MONTH(created_at) as m'),
                DB::raw("SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status='problem'   THEN 1 ELSE 0 END) as problem")
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
            'problem'   => (int) ($trendRaw[$m]->problem   ?? 0),
            'highlight' => in_array($m, $highlightMonths),
        ])->values();

        // ─── TOP SHORTAGE ITEMS (incomplete DOs) ──────────────────
        $shortageItems = DB::table('delivery_items as di')
            ->join('delivery_orders as d', 'di.delivery_order_id', '=', 'd.id')
            ->whereBetween('d.created_at', [$dateFrom, $dateTo])
            ->where('di.shortage_qty', '>', 0)
            ->where('d.sub_status', 'incomplete')
            ->select(
                'di.product_name',
                DB::raw('COUNT(DISTINCT di.delivery_order_id) as do_count'),
                DB::raw('GROUP_CONCAT(di.shortage_reason ORDER BY di.shortage_reason SEPARATOR "|||") as reasons')
            )
            ->groupBy('di.product_name')
            ->orderByDesc('do_count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'product_name' => $r->product_name,
                'count'        => (int) $r->do_count,
                'top_reason'   => $this->topReason($r->reasons),
            ]);

        // ─── TOP REJECTED ITEMS (rejected DOs) ────────────────────
        $rejectedItems = DB::table('delivery_items as di')
            ->join('delivery_orders as d', 'di.delivery_order_id', '=', 'd.id')
            ->whereBetween('d.created_at', [$dateFrom, $dateTo])
            ->where('d.sub_status', 'rejected')
            ->select(
                'di.product_name',
                DB::raw('COUNT(DISTINCT di.delivery_order_id) as do_count'),
                DB::raw('GROUP_CONCAT(di.shortage_reason ORDER BY di.shortage_reason SEPARATOR "|||") as reasons')
            )
            ->groupBy('di.product_name')
            ->orderByDesc('do_count')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'product_name' => $r->product_name,
                'count'        => (int) $r->do_count,
                'top_reason'   => $this->topReason($r->reasons),
            ]);

        // ─── TIMING ───────────────────────────────────────────────
        $timing = DB::table('delivery_orders')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->whereNotNull('dispatched_at')
            ->whereNotNull('arrived_at')
            ->whereNotNull('received_at')
            ->selectRaw('
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, created_at, dispatched_at)), 0) as avg_prep,
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, dispatched_at, arrived_at)),  0) as avg_drive,
                ROUND(AVG(TIMESTAMPDIFF(MINUTE, arrived_at,   received_at)),  0) as avg_receive
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
                'problem'            => $problem,
                'problem_incomplete' => $incomplete,
                'problem_rejected'   => $rejected,
                'bo_count'           => $boCount,
                'success_rate'       => $successRate,
                'problem_rate'       => $total > 0 ? round(100 - $successRate, 1) : 0,
            ],
            'trend'              => $trend,
            'top_shortage_items' => $shortageItems,
            'top_rejected_items' => $rejectedItems,
            'timing' => [
                'avg_prep_min'    => $timing?->avg_prep    ? (int) $timing->avg_prep    : null,
                'avg_drive_min'   => $timing?->avg_drive   ? (int) $timing->avg_drive   : null,
                'avg_receive_min' => $timing?->avg_receive ? (int) $timing->avg_receive : null,
                'fastest_hour'    => $fastestHour?->hr,
            ],
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
        $from = $request->from ?? '';
        $to   = $request->to   ?? '';
        $cacheKey = "delivery_summary_{$from}_{$to}";

        $result = cache()->remember($cacheKey, 30, function () use ($request) {
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
                ->with(['branch:id,name,lat,lng', 'vehicle:id,license_plate'])
                ->where('status', 'in_transit')
                ->latest()
                ->get();

            return [
                'total'      => (int) $counts->total,
                'delivered'  => (int) $counts->delivered,
                'in_transit' => (int) $counts->in_transit,
                'pending'    => (int) $counts->pending,
                'cancelled'  => (int) $counts->cancelled,
                'orders'     => $transitOrders->values()->toArray(),
            ];
        });

        return response()->json($result);
    }
}
