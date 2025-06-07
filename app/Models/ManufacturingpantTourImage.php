<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturingpantTourImage extends Model
{
    use SoftDeletes;

    protected $table = 'manufacturingpant_tour_images';

    protected $fillable = [
        'image',
        'category',
        'status',
    ];
}
