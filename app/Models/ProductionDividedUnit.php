<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionDividedUnit extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'production_divided_unit';

    protected $fillable = [
        'image', 'title', 'description','status',
    ];
}
