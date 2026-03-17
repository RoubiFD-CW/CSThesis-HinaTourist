<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'visitor_type',
        'group_size',
        'male_count',
        'female_count',
        'origin',
        'visit_reason',
        'visit_reason_other',
        'dedicated_area',
        'attendant_id',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function attendant()
    {
        return $this->belongsTo(User::class, 'attendant_id');
    }
}
