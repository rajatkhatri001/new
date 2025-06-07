<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NewLaunchSlider extends Model
{
    use softDeletes;

    protected $table = 'new_launch_sliders';

    protected $fillable = [
        'image', 'thumb_image', 'title', 'description', 'button_title', 'file_name', 'is_banner','status',
    ];
}
