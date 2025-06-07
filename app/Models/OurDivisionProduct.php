<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurDivisionProduct extends Model
{
    use SoftDeletes;

    protected $table = 'our_division_products';

    protected $fillable = [
        'image', 'title', 'category', 'division', 'status',
    ];
}
