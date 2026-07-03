<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'do_number',
        'branch_id',
        'driver_id',
        'created_by',
        'parent_do_id',
        'is_backorder',
        'is_multi_stop',
        'bo_round',
        'sub_status',
        'approved_by',
        'rejection_reason',
        'cancel_reason',
        'status',
        'qr_code',
        'scheduled_date',
        'dispatched_at',
        'delivered_at',
        'arrived_at',
        'received_at',
        'depart_photo_path',
        'arrive_photo_path',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'dispatched_at'  => 'datetime',
            'delivered_at'   => 'datetime',
            'arrived_at'     => 'datetime',
            'received_at'    => 'datetime',
            'is_backorder'   => 'boolean',
            'is_multi_stop'  => 'boolean',
            'bo_round'       => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->do_number = $model->do_number ?? static::generateDoNumber();
            $model->qr_code   = $model->qr_code   ?? Str::uuid()->toString();
        });
    }

    public static function generateDoNumber(): string
    {
        $date  = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return 'DO-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function branch()      { return $this->belongsTo(Branch::class); }
    public function driver()      { return $this->belongsTo(User::class, 'driver_id'); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }

    public function items()        { return $this->hasMany(DeliveryItem::class); }
    public function stops()        { return $this->hasMany(DeliveryStop::class)->orderBy('sequence'); }
    public function receiveRecord(){ return $this->hasOne(ReceiveRecord::class); }
    public function gpsLogs()      { return $this->hasMany(GpsLog::class); }
    public function shortageLogs() { return $this->hasMany(ShortageLog::class, 'do_id'); }

    public function parentOrder()  { return $this->belongsTo(DeliveryOrder::class, 'parent_do_id'); }
    public function backorders()   { return $this->hasMany(DeliveryOrder::class, 'parent_do_id'); }
}
