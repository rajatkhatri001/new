<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ManufacturingPlantList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manufacturing_plant_lists';

    protected $fillable = [
        'image',
        'title',
        'description',
        'status',
    ];

}
