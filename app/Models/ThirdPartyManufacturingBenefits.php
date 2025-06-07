<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThirdPartyManufacturingBenefits extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'third_party_manufacturing_benefits';

    protected $fillable = [
        'image',
        'title',
        'description',
        'status',
        
    ];
}
