<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\BroadcastController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\LoginHistoryController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\DeliveryOrderController;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\ReceiveController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReturnRequestController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Web Push VAPID public key — no auth needed
Route::get('/push/vapid-public-key', [PushSubscriptionController::class, 'vapidPublicKey']);

// Public logo — no auth needed (for apple-touch-icon and PWA manifest)
Route::get('/public/logo', function () {
    $b64 = \App\Models\Setting::get('logo_base64', '');
    if (!$b64 || !str_starts_with($b64, 'data:image/')) {
        return response('', 204);
    }
    [$meta, $data] = explode(',', $b64, 2);
    preg_match('/data:(image\/[^;]+)/', $meta, $m);
    $mime = $m[1] ?? 'image/png';
    $bin  = base64_decode($data);
    $v    = \App\Models\Setting::get('logo_base64') ? crc32($bin) : 0;
    return response($bin, 200, [
        'Content-Type'  => $mime,
        'Cache-Control' => 'public, max-age=86400',
        'ETag'          => '"' . $v . '"',
    ]);
});

// Evidence photos — public (filenames are UUID-based)
Route::get('/evidence/{filename}', [ReceiveController::class, 'serveEvidence'])
    ->where('filename', '[^/]+');
Route::get('/returns/photo/{filename}', [ReturnRequestController::class, 'servePhoto'])
    ->where('filename', '[^/]+');

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/employee-login', [AuthController::class, 'employeeLogin']);
    Route::post('/quick-access', [AuthController::class, 'quickAccess']);
    Route::post('/pin-login', [AuthController::class, 'pinLogin']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {

    // Delivery Orders — HQ, Admin
    Route::get('/delivery-orders', [DeliveryOrderController::class, 'index'])
        ->middleware('role:admin,hq');
    Route::get('/delivery-orders/{deliveryOrder}', [DeliveryOrderController::class, 'show'])
        ->middleware('role:admin,hq,driver,branch');
    Route::post('/delivery-orders', [DeliveryOrderController::class, 'store'])
        ->middleware('role:admin,hq');
    Route::put('/delivery-orders/{deliveryOrder}', [DeliveryOrderController::class, 'update'])
        ->middleware('role:admin,hq');
    Route::patch('/delivery-orders/{deliveryOrder}/cancel', [DeliveryOrderController::class, 'cancel'])
        ->middleware('role:admin,hq');
    Route::patch('/delivery-orders/{deliveryOrder}/dispatch', [DeliveryOrderController::class, 'dispatch'])
        ->middleware('role:admin,hq,driver');
    Route::post('/delivery-orders/{deliveryOrder}/receive-items', [DeliveryOrderController::class, 'receiveItems'])
        ->middleware('role:admin,branch');
    Route::post('/delivery-orders/{deliveryOrder}/approve-backorder', [DeliveryOrderController::class, 'approveBackorder'])
        ->middleware('role:admin,hq');
    Route::post('/delivery-orders/{deliveryOrder}/reject-backorder', [DeliveryOrderController::class, 'rejectBackorder'])
        ->middleware('role:admin,hq');
    Route::get('/driver/orders', [DeliveryOrderController::class, 'driverOrders'])
        ->middleware('role:admin,driver');
    Route::get('/driver/available-orders', [DeliveryOrderController::class, 'availableOrders'])
        ->middleware('role:admin,driver');
    Route::post('/delivery-orders/{deliveryOrder}/accept-order', [DeliveryOrderController::class, 'acceptOrder'])
        ->middleware('role:driver');
    Route::post('/delivery-orders/{deliveryOrder}/depart-photo', [DeliveryOrderController::class, 'departPhoto'])
        ->middleware('role:driver');
    Route::post('/delivery-orders/{deliveryOrder}/confirm-delivery', [DeliveryOrderController::class, 'confirmDelivery'])
        ->middleware('role:driver');
    Route::post('/delivery-orders/{deliveryOrder}/notify-arrival', [DeliveryOrderController::class, 'notifyArrival'])
        ->middleware('role:driver');
    Route::post('/delivery-orders/{deliveryOrder}/create-reorder', [DeliveryOrderController::class, 'createReorder'])
        ->middleware('role:admin,hq');

    // Notifications — all authenticated users (drivers get rejection alerts)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);

    // Web Push subscriptions
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
    Route::post('/push/test', [PushSubscriptionController::class, 'testPush']);

    // Receive — Branch
    Route::get('/scan/{qrCode}', [ReceiveController::class, 'scan'])
        ->middleware('role:admin,branch');
    Route::post('/receive/{deliveryOrder}', [ReceiveController::class, 'receive'])
        ->middleware('role:admin,branch');
    Route::post('/receive/{deliveryOrder}/signature', [ReceiveController::class, 'signature'])
        ->middleware('role:admin,branch');
    Route::post('/receive/{deliveryOrder}/photos', [ReceiveController::class, 'photos'])
        ->middleware('role:admin,branch');
    Route::get('/branch/delivery-orders', [ReceiveController::class, 'branchOrders']);

    // GPS — Driver
    Route::post('/gps/update', [GpsController::class, 'update'])
        ->middleware('role:admin,driver');
    Route::get('/gps/distance/{driverId}', [GpsController::class, 'getDistance'])
        ->middleware('role:admin,hq,branch,driver')
        ->where('driverId', '[0-9]+');
    Route::get('/gps/driver/{driverId}', [GpsController::class, 'history'])
        ->middleware('role:admin,hq,driver')
        ->where('driverId', '[0-9]+');

    // Reports — Admin, HQ
    Route::middleware('role:admin,hq')->group(function () {
        Route::get('/reports/monthly', [ReportController::class, 'monthly']);
        Route::get('/reports/delivery-summary', [ReportController::class, 'deliverySummary']);
        Route::get('/reports/delivery-stats', [ReportController::class, 'deliveryStats']);
    });

    // Branches — Admin, HQ
    Route::middleware('role:admin,hq')->group(function () {
        Route::get('/branches', [BranchController::class, 'index']);
        Route::post('/branches', [BranchController::class, 'store']);
        Route::put('/branches/{branch}', [BranchController::class, 'update']);
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy']);
    });


    // Calendar — all roles
    Route::get('/calendar', [CalendarController::class, 'index']);

    // Login History — Admin only
    Route::get('/admin/login-history', [LoginHistoryController::class, 'index'])
        ->middleware('role:admin');

    // Users — Admin only
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:admin,hq');
    Route::middleware('role:admin')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/users/{user}/reset-pin', [UserController::class, 'resetPin']);
    });

    // Return Requests — Branch creates, Admin/HQ views and updates
    Route::get('/returns', [ReturnRequestController::class, 'index'])
        ->middleware('role:admin,hq,branch');
    Route::post('/returns', [ReturnRequestController::class, 'store'])
        ->middleware('role:branch');
    Route::put('/returns/{returnRequest}', [ReturnRequestController::class, 'update'])
        ->middleware('role:admin,hq');

    // Settings — read all authenticated, write admin only
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update'])
        ->middleware('role:admin');

    // Admin-only: broadcast, export, login history
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/broadcast', [BroadcastController::class, 'send']);
        Route::get('/admin/broadcast/history', [BroadcastController::class, 'history']);
        Route::get('/admin/export/csv', [ExportController::class, 'exportCsv']);
        Route::get('/admin/export/excel', [ExportController::class, 'exportExcel']);
    });

    // Items — read for all, write for admin
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/delivery-items/products', [ItemController::class, 'productNames'])
        ->middleware('role:admin,hq,branch');
    Route::middleware('role:admin')->group(function () {
        Route::post('/items', [ItemController::class, 'store']);
        Route::put('/items/{item}', [ItemController::class, 'update']);
        Route::patch('/items/{item}/toggle', [ItemController::class, 'toggle']);
    });
});
