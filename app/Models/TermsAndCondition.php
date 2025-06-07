<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TermsAndCondition extends Model
{
    use SoftDeletes;

    protected $table = 'terms_and_conditions';

    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_description',
    ];
}
