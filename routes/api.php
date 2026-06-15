<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DeliveryOrderController;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReceiveController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

// Evidence photos — public (filenames are UUID-based)
Route::get('/evidence/{filename}', [ReceiveController::class, 'serveEvidence'])
    ->where('filename', '[^/]+');

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/quick-login', [AuthController::class, 'quickLogin']);
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
        ->middleware('role:admin,hq,driver');
    Route::post('/delivery-orders', [DeliveryOrderController::class, 'store'])
        ->middleware('role:admin,hq');
    Route::put('/delivery-orders/{deliveryOrder}', [DeliveryOrderController::class, 'update'])
        ->middleware('role:admin,hq');
    Route::patch('/delivery-orders/{deliveryOrder}/cancel', [DeliveryOrderController::class, 'cancel'])
        ->middleware('role:admin,hq');
    Route::patch('/delivery-orders/{deliveryOrder}/dispatch', [DeliveryOrderController::class, 'dispatch'])
        ->middleware('role:admin,hq,driver');
    Route::patch('/delivery-orders/{deliveryOrder}/arrive', [DeliveryOrderController::class, 'arrive'])
        ->middleware('role:admin,hq,driver');
    Route::post('/delivery-orders/{deliveryOrder}/receive-items', [DeliveryOrderController::class, 'receiveItems'])
        ->middleware('role:admin,branch');
    Route::post('/delivery-orders/{deliveryOrder}/approve-backorder', [DeliveryOrderController::class, 'approveBackorder'])
        ->middleware('role:admin,hq');
    Route::post('/delivery-orders/{deliveryOrder}/reject-backorder', [DeliveryOrderController::class, 'rejectBackorder'])
        ->middleware('role:admin,hq');
    Route::get('/driver/orders', [DeliveryOrderController::class, 'driverOrders'])
        ->middleware('role:admin,driver');

    // Notifications — all authenticated users (drivers get rejection alerts)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);

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
    Route::get('/gps/distance/{vehicleId}', [GpsController::class, 'getDistance'])
        ->middleware('role:admin,hq,branch,driver')
        ->where('vehicleId', '[0-9]+');
    Route::get('/gps/{vehicle}', [GpsController::class, 'history'])
        ->middleware('role:admin,hq,driver');

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

    // Vehicles — Admin, HQ, Driver
    Route::get('/vehicles', [VehicleController::class, 'index'])
        ->middleware('role:admin,hq,driver');
    Route::middleware('role:admin,hq')->group(function () {
        Route::post('/vehicles', [VehicleController::class, 'store']);
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update']);
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);
    });

    // Users — Admin only
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:admin,hq');
    Route::middleware('role:admin')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    // Items — read for all, write for admin
    Route::get('/items', [ItemController::class, 'index']);
    Route::middleware('role:admin')->group(function () {
        Route::post('/items', [ItemController::class, 'store']);
        Route::put('/items/{item}', [ItemController::class, 'update']);
        Route::patch('/items/{item}/toggle', [ItemController::class, 'toggle']);
    });
});
