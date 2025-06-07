<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'achievements';

    protected $fillable = ['title', 'description', 'status','image'];
}
