<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidencePhoto extends Model
{
    protected $fillable = [
        'receive_record_id',
        'photo_path',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function receiveRecord()
    {
        return $this->belongsTo(ReceiveRecord::class);
    }
}
