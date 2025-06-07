<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurDivisionBanner extends Model
{
    use SoftDeletes;

    protected $table = 'our_division_banner';

    protected $fillable = [
        'image', 'title', 'description'
    ];
}
