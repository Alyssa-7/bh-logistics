<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveRecord extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'received_by',
        'status',
        'remark',
        'signature',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
        ];
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function photos()
    {
        return $this->hasMany(EvidencePhoto::class);
    }
}
