<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'account_id',
        'action',
        'subject_type',
        'subject_id',
        'title',
        'desc',
        'data_record',
    ];

    protected $casts = [
        'data_record' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'account_id', 'id');
    }
}
