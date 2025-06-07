<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'event_pages';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
    ];
}
