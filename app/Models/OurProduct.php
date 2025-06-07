<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurProduct extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'our_product';

    protected $fillable = [
        'title', 'image', 'description'
    ];
}
