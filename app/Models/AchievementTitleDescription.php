<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AchievementTitleDescription extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'achievement_title_description';

    protected $fillable = [
        'title',
        'description',
    ];
}
