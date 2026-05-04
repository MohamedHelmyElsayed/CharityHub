<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'title',
        'outcomes_narrative',
        'beneficiary_count',
        'funds_used',
        'report_period',
        'status',
        'pdf_path',
        'pdf_generated_at',
    ];

    protected $casts = [
        'funds_used' => 'decimal:2',
        'pdf_generated_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function locations()
    {
        return $this->hasMany(BeneficiaryLocation::class);
    }

    public function photos()
    {
        return $this->hasMany(ImpactPhoto::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
