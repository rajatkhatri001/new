<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PcdPharmaFranchise extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'pcd_pharma_franchises';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
        'pharma_franchise_image',
        'pharma_franchise_title',
        'pharma_franchise_button_title',
        'pharma_franchise_button_url',
        'pharma_franchise_description',
        'pcd_image',
        'pcd_title',
        'pcd_description',
        'pcd_visit',
        'pcd_call',
        'pcd_journey_title',
        'pcd_journey_customers',
        'pcd_journey_manufacturing_facilities',
        'pcd_journey_sku',
        'pcd_journey_employees',
        'pcd_journey_button_title',
        'pcd_journey_button_url',
        'psychocare_image',
        'psychocare_title',
        'psychocare_description',
    ];
}
