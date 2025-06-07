<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurDivision extends Model
{
    use SoftDeletes;

    protected $table = 'our_divisions';

    protected $fillable = [
        'title',
        'image',
        'description',
        'division_link',
        'status',
    ];

    public function productCategories()
    {
        return $this->hasMany(OurProductsCategory::class, 'division');
    }
}
