<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'impact_report_id',
        'name',
        'latitude',
        'longitude',
        'description',
        'beneficiaries',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'beneficiaries' => 'integer',
    ];

    public function impactReport()
    {
        return $this->belongsTo(ImpactReport::class);
    }
}
