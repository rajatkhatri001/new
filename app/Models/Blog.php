<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'blogs';

    protected $fillable = ['title', 'description', 'status','image','is_this_news'];
}
