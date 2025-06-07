<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Footer extends Model
{
    use SoftDeletes;

    protected $table = 'footer';

    protected $fillable = [
        'description',
    ];

    static public function footer(){
        $description = Footer::first();

        return $description;
    }
}
