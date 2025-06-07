<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DowloadVisualAdsCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'dowload_visual_ads_category';

    protected $fillable = [
        'category',
        'pdf',
        'status',
    ];
    static public function visualadscategory() {
        return DowloadVisualAdsCategory::where('status',1)->get();
    }
}
