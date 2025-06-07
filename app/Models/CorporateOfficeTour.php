<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateOfficeTour extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'corporate_office_tours';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
        'welcome_video_url',
        'welcome_title',
        'welcome_description',
        'office_map_iframe',
        'office_location',
        'office_phone',
        'office_email',
        'office_website',
    ];
}
