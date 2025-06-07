<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealWithRangeimage extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'deal_with_range_image';

    protected $fillable = [
        'image',
        'title',
        'status',
    ];
}
