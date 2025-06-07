<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PcdPharmaAdvantage extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'pcd_pharma_advantages';

    protected $fillable = ['title', 'description', 'status','image'];
}
