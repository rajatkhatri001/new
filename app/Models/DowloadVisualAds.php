<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DowloadVisualAds extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'dowload_visual_ads';

    protected $fillable = [
        'category',
        'name',
        'email',
    ];
}
