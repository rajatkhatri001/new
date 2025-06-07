<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyProfile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_profiles';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
        'about_pchpl_title',
        'about_pchpl_description',
        'video_url',
        'mission_image',
        'mission_title',
        'mission_button_title',
        'mission_button_url',
        'mission_description',
        'vision_image',
        'vision_title',
        'vision_button_title',
        'vision_button_url',
        'vision_description',
        'we_believe_title',
        'we_believe_image',
        'we_believe_description',
        'achievements_title',
        'achievements_button_title',
        'achievements_button_url',
        'directors_title',
        'trusted_partners_title',
        'trusted_partners_description',
        'division_sister_concerns_title',
        'products_title',
        'pchpl_team_title',
    ];

}
