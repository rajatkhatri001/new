<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurProductsCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'our_products_category';

    protected $fillable = [
        'category_name', 'division', 'status'
    ];

    public function divisions()
    {
        return $this->belongsTo(OurDivision::class, 'division','id');
    }

    public function products()
    {
        return $this->hasMany(OurProductImages::class,'category');
    }
}
