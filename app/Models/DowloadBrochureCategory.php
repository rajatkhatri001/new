<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DowloadBrochureCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'dowload_brochure_category';

    protected $fillable = [
        'category',
        'pdf',
        'status',
    ];
    static public function brochurecategory() {
        return DowloadBrochureCategory::where('status',1)->get();
    }
}
