<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealWithRange extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'deal_with_range';

    protected $fillable = [
        'title',
        'description',
    ];

}
