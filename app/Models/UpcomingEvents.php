<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UpcomingEvents extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'upcoming_events';

    protected $fillable = [
       'image','title','status','start_date'
    ];
}
