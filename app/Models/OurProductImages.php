<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurProductImages extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'our_product_images';

    protected $fillable = [
        'product_label',
        'image',
        'product_title',
        'packing_type',
        'packing_size',
        'status',
        'category',
        'division_id',
        'mrp',
        'ptr',
        'pts',
        'composition',
        'label_2',
        'product_description',
        'product_side_effect',
        'product_indication',
        'is_new_product'
    ];

    public function productCategory()
    {
        return $this->belongsTo(OurProductsCategory::class,'category','id');
    }
}
