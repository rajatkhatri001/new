<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageCerificate extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'manage_cerificates';

    protected $fillable = [
        'image','name','status',
    ];

}
