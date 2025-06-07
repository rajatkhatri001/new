<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectForm extends Model
{
    use HasFactory;

    protected $table = 'connect_forms';

    protected $fillable = [
        'image',
        'name',
        'email',
        'module',
    ];
}
