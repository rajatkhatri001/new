<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NewLaunchBanner extends Model
{
    use SoftDeletes;

    protected $table = 'new_launch_banners';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
    ];
}
