<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'stop_id',
        'item_id',
        'product_name',
        'qty_ordered',
        'qty_received',
        'shortage_qty',
        'shortage_reason',
        'unit',
        'remark',
        'item_note',
    ];

    protected function casts(): array
    {
        return [
            'qty_ordered'  => 'float',
            'qty_received' => 'float',
            'shortage_qty' => 'float',
            'stop_id'      => 'integer',
        ];
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function stop()
    {
        return $this->belongsTo(DeliveryStop::class);
    }

    public function shortageLogs()
    {
        return $this->hasMany(ShortageLog::class, 'item_id');
    }
}
