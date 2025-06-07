<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturingplantTour extends Model
{
    use SoftDeletes;

    protected $table = 'manufacturingplant_tour';

    protected $fillable = [
        'image',
        'title',
        'description',
        'address_iframe',
        'address',
        'phone_no',
        'website',
        'email',
    ];
}
