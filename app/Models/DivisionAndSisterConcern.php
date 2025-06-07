<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionAndSisterConcern extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'division_and_sister_concerns';

    protected $fillable = [
        'image',
        'title',
        'description', 'status',
    ];
}
