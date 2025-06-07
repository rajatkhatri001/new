<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageAwards extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'manage_awards';

    protected $fillable = [
        'image',
        'title',
        'description',
        'status',
    ];
}
