<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateOfficeTourImage extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'corporate_office_tour_images';

    protected $fillable = [
       'image','category','status'
    ];
}
