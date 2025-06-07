<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FranchiseOpportunity extends Model
{
    use SoftDeletes;

    protected $table = 'franchise_opportunities';

    protected $fillable = ['title', 'button_title', 'button_link'];

    static public function frenchiseOpp() {
        return FranchiseOpportunity::first();
    }
}
