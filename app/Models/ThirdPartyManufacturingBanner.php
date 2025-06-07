<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyManufacturingBanner extends Model
{
    use HasFactory;

    protected $table = 'third_party_manufacturing_banner';

    protected $fillable = [
        'title', 'image', 'description', 'manufacturing_image', 'website', 'contactno', 'manufacturing_title', 'manufacturing_description'
    ];
}
