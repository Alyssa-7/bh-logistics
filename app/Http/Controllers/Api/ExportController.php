<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    private function getOrders(Request $request)
    {
        $query = DB::table('delivery_orders as d')
            ->join('branches as b', 'b.id', '=', 'd.branch_id')
            ->leftJoin('users as u', 'u.id', '=', 'd.driver_id')
            ->leftJoin('receive_records as rr', 'rr.delivery_order_id', '=', 'd.id')
            ->leftJoin('users as ru', 'ru.id', '=', 'rr.received_by')
            ->select(
                'd.do_number',
                'd.status',
                'd.scheduled_date',
                'b.name as branch',
                'u.name as driver',
                'd.created_at',
                'd.dispatched_at',
                'd.arrived_at',
                'd.received_at',
                'd.cancel_reason',
                'ru.name as recipient_name',
                DB::raw('(SELECT product_name FROM delivery_items WHERE delivery_order_id = d.id ORDER BY id LIMIT 1) as first_product'),
                DB::raw('(SELECT CAST(SUM(qty_ordered) AS DECIMAL(10,2)) FROM delivery_items WHERE delivery_order_id = d.id) as total_qty'),
                DB::raw('(SELECT COUNT(*) FROM delivery_items WHERE delivery_order_id = d.id) as item_count')
            )
            ->where('d.is_backorder', false);

        if ($request->filled('date_from')) {
            $query->whereDate('d.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('d.created_at', '<=', $request->date_to);
        }
        if ($request->filled('branch_id')) {
            $query->where('d.branch_id', $request->branch_id);
        }
        if ($request->filled('driver_id')) {
            $query->where('d.driver_id', $request->driver_id);
        }
        if ($request->filled('status')) {
            $query->where('d.status', $request->status);
        }

        return $query->orderBy('d.created_at', 'desc')->get();
    }

    private function statusTh(string $status): string
    {
        return match($status) {
            'pending'    => 'รอดำเนินการ',
            'in_transit' => 'กำลังส่ง',
            'delivered'  => 'ส่งแล้ว',
            'completed'  => 'สำเร็จ',
            'cancelled'  => 'ยกเลิก',
            default      => $status,
        };
    }

    private function formatDt(?string $dt): string
    {
        if (!$dt) return '-';
        return \Carbon\Carbon::parse($dt)->format('d/m/Y H:i');
    }

    private function headers(): array
    {
        return [
            'DO Number', 'สถานะ', 'วันนัดส่ง', 'สาขา', 'คนขับ', 'วันที่สร้าง',
            'เวลาออกรถ', 'เวลาถึงสาขา', 'เวลารับสินค้าเสร็จ',
            'สินค้าหลัก', 'จำนวนรายการ', 'จำนวนรวม (หน่วย)',
            'ชื่อผู้รับ', 'หมายเหตุ',
        ];
    }

    private function toRow(object $o): array
    {
        $note = $o->cancel_reason ?? '';
        $totalQty = $o->total_qty !== null ? rtrim(rtrim(number_format((float)$o->total_qty, 2), '0'), '.') : '-';

        return [
            $o->do_number,
            $this->statusTh($o->status),
            $o->scheduled_date ?? '-',
            $o->branch,
            $o->driver ?? '-',
            $this->formatDt($o->created_at),
            $this->formatDt($o->dispatched_at),
            $this->formatDt($o->arrived_at),
            $this->formatDt($o->received_at),
            $o->first_product ?? '-',
            $o->item_count ?? 0,
            $totalQty,
            $o->recipient_name ?? '-',
            $note,
        ];
    }

    public function exportCsv(Request $request)
    {
        $orders = $this->getOrders($request);

        $lines = [implode(',', array_map(fn ($h) => '"' . $h . '"', $this->headers()))];
        foreach ($orders as $o) {
            $lines[] = implode(',', array_map(
                fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"',
                $this->toRow($o)
            ));
        }

        $csv      = "\xEF\xBB\xBF" . implode("\n", $lines);
        $filename = 'orders_' . now()->format('Ymd_His') . '.csv';

        Setting::set('last_export_csv', now()->toDateTimeString());

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportExcel(Request $request)
    {
        $orders = $this->getOrders($request);

        $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
        $html .= '<table border="1"><thead><tr>';
        foreach ($this->headers() as $h) {
            $html .= '<th style="background:#1e3a5f;color:#fff;font-weight:bold;">' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($orders as $o) {
            $html .= '<tr>' . implode('', array_map(
                fn ($v) => '<td>' . htmlspecialchars((string) $v) . '</td>',
                $this->toRow($o)
            )) . '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $filename = 'orders_' . now()->format('Ymd_His') . '.xls';

        Setting::set('last_export_excel', now()->toDateTimeString());

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
