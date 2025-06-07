<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'blog_pages';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
        'blog_list_banner_image',
        'blog_list_banner_title',
        'blog_list_banner_description',
    ];
}
