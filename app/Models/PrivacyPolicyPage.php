<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PrivacyPolicyPage extends Model
{
    use SoftDeletes;

    protected $table = 'privacy_policy_pages';

    protected $fillable = [
        'privacyPolicy_page_content',
    ];


}
