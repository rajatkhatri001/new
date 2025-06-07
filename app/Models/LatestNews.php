<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LatestNews extends Model
{
    use HasFactory; use SoftDeletes;

    protected $table = 'latest_news';

    protected $fillable = [
        'title',
        'description',
        'image',
    ];
}
