<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityAssurance extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'quality_assurances';

    protected $fillable = [
       'image','status'
    ];
}
