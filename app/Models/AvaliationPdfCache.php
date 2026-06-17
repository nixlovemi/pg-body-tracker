<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvaliationPdfCache extends Model
{
    protected $fillable = [
        'avaliation_id',
        'snapshot_hash',
        'storage_path',
        'status',
        'file_size',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function avaliation(): BelongsTo
    {
        return $this->belongsTo(Avaliation::class);
    }
}
