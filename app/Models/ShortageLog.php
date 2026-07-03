<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortageLog extends Model
{
    protected $fillable = [
        'do_id',
        'item_id',
        'branch_id',
        'shortage_qty',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'shortage_qty' => 'float',
        ];
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'do_id');
    }

    public function item()
    {
        return $this->belongsTo(DeliveryItem::class, 'item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
