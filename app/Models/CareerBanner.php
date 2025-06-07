<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerBanner extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'career_banners';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
    ];
}
