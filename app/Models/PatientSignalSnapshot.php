<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientSignalSnapshot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'snapshot_date',
        'status',
        'risk_percent',
        'signal_count',
        'summary_json',
        'reasons_json',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'snapshot_date' => 'date',
        'risk_percent' => 'float',
        'signal_count' => 'integer',
        'summary_json' => 'array',
        'reasons_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
