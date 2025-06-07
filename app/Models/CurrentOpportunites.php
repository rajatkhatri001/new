<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CurrentOpportunites extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'current_opportunites';

    protected $fillable = [
        'name',
        'location',
        'description',
        'status',
    ];
}
