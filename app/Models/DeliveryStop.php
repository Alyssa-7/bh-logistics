<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryStop extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'branch_id',
        'sequence',
        'status',
        'delivered_at',
        'arrive_photo_path',
        'signature',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'sequence'     => 'integer',
        ];
    }

    public function deliveryOrder() { return $this->belongsTo(DeliveryOrder::class); }
    public function branch()        { return $this->belongsTo(Branch::class); }
    public function items()         { return $this->hasMany(DeliveryItem::class, 'stop_id'); }
}
