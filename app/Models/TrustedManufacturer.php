<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrustedManufacturer extends Model
{
    use SoftDeletes;

    protected $table = 'trusted_manufacturers';

    protected $fillable = [
        'image', 'name', 'description', 'status',
    ];
}
