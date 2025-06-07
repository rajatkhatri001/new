<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProudMember extends Model
{
    use SoftDeletes;

    protected $table = 'proud_members';

    protected $fillable = ['image', 'status'];
}
