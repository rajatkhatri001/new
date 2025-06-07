<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogisticPartner extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'logistic_partners';

    protected $fillable = [
       'image','status'
    ];
}
