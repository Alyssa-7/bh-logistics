<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsLog extends Model
{
    protected $fillable = [
        'driver_id',
        'delivery_order_id',
        'lat',
        'lng',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }
}
