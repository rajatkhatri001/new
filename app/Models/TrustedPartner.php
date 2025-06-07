<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrustedPartner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'trusted_partners';

    protected $fillable = [
       'image','status'
    ];
}
