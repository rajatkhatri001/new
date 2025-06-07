<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogDetailsForm extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'blog_details_form';

    protected $fillable = [
        'blog_id',
        'name',
        'comment',
        
    ];
}
