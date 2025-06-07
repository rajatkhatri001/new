<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ContactUs extends Model
{
    use SoftDeletes;

    protected $table = 'contact_us';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
    ];
}
