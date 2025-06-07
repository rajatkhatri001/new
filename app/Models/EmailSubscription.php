<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSubscription extends Model
{
    use SoftDeletes;

    protected $table = 'email_subscriptions';

    protected $fillable = ['email'];
}
