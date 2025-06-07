<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturingPlant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manufacturing_plants';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
        'objective_image',
        'objective_title',
        'objective_description',
        'content_title',
    ];

}
