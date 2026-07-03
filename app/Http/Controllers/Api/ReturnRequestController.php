<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ReturnRequest::with(['branch', 'creator'])->latest();

        if ($user->role === 'branch') {
            $query->where('branch_id', $user->branch_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity'     => 'required|numeric|min:0.01',
            'reason'       => 'required|in:damaged,expired,wrong_item,other',
            'remark'       => 'nullable|string|max:500',
            'photo'        => 'required|file|image|max:5120',
        ]);

        $user = $request->user();
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('returns', 'public');
        }

        $returnReq = ReturnRequest::create([
            'branch_id'    => $user->branch_id,
            'created_by'   => $user->id,
            'product_name' => $request->product_name,
            'quantity'     => $request->quantity,
            'reason'       => $request->reason,
            'remark'       => $request->remark,
            'photo_path'   => $photoPath,
        ]);

        $returnReq->load(['branch', 'creator']);
        $branchName = $returnReq->branch->name ?? 'สาขา';
        $returnMsg  = "สาขา{$branchName} แจ้งตีกลับสินค้า: {$request->product_name} ({$request->quantity} ชิ้น)";
        $recipients = User::whereIn('role', ['admin', 'hq'])->pluck('id');
        Notification::insert($recipients->map(fn($uid) => [
            'user_id'    => $uid,
            'type'       => 'return_request',
            'do_id'      => null,
            'message'    => $returnMsg,
            'is_read'    => false,
            'created_at' => now(),
        ])->all());
        app(PushNotificationService::class)->sendToUsers($recipients->toArray(), 'BH-Logistic', $returnMsg);

        return response()->json($returnReq, 201);
    }

    public function update(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $returnRequest->update(['status' => $request->status]);
        $returnRequest->load(['branch', 'creator']);

        $notifType   = $request->status === 'approved' ? 'return_approved' : 'return_rejected';
        $statusTH    = ['approved' => 'อนุมัติ', 'rejected' => 'ปฏิเสธ'];
        $label       = $statusTH[$request->status] ?? $request->status;
        $updateMsg   = "คำร้องตีกลับสินค้า \"{$returnRequest->product_name}\" ถูก{$label}แล้ว";
        $branchUsers = User::where('branch_id', $returnRequest->branch_id)->pluck('id');
        if ($branchUsers->isNotEmpty()) {
            Notification::insert($branchUsers->map(fn($uid) => [
                'user_id'    => $uid,
                'type'       => $notifType,
                'do_id'      => null,
                'message'    => $updateMsg,
                'is_read'    => false,
                'created_at' => now(),
            ])->all());
            app(PushNotificationService::class)->sendToUsers($branchUsers->toArray(), 'BH-Logistic', $updateMsg);
        }

        return response()->json($returnRequest);
    }

    public function servePhoto(string $filename)
    {
        $path = storage_path('app/public/returns/' . $filename);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
