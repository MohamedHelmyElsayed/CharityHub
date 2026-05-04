<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'impact_report_id',
        'path',
        'caption',
        'sort_order',
    ];

    public function impactReport()
    {
        return $this->belongsTo(ImpactReport::class);
    }

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::url($this->path);
    }
}
