<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventImage extends Model
{
    use HasFactory; use SoftDeletes;

    protected $table = 'event_images';

    protected $fillable = ['image','status'];

    // public function event()
    // {
    //     return $this->belongsTo(Event::class);
    // }
}
