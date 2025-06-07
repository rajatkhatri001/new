<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Eloquent implements Authenticatable
{
    use SoftDeletes;
	use AuthenticableTrait;

    protected $table = 'admin';

    protected $hidden = [
		'password',
	];

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_no',
        'password',
        'email',
        'status',
        'remember_token',
        'verify_key',
    ];
}
