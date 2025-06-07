<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Director extends Model
{
    use SoftDeletes;

    protected $table = 'directors';

    protected $fillable = [
        'image',
        'name',
        'designation',
        'status',
        'description', 'fb_link', 'insta_link', 'twitter_link','linkedin_link', 'youtube_link'
    ];
}
