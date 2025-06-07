<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeSlider extends Model
{

    use SoftDeletes;

    protected $table = 'home_sliders';

    protected $fillable = [
        'image', 'title', 'description', 'button_title', 'button_link', 'status',
    ];
}
