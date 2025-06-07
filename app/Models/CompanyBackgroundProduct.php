<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBackgroundProduct extends Model
{
    use SoftDeletes;

    protected $table = 'company_background_products';

    protected $fillable = [
        'company_background_description',
        'company_background_button_title',
        'company_background_button_link',
        'products_description',
        'products_button_title',
        'products_button_link',
        'products_id',
        'company_background_image'
    ];
}
