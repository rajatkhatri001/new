<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetstalkForm extends Model
{
    use SoftDeletes;

    protected $table = 'letstalk_forms';

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
}
