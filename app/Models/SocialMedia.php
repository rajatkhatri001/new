<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMedia extends Model
{

    use SoftDeletes;

    protected $table = 'social_media';

    protected $fillable = ['image', 'name', 'url', 'status'];

    static public function socialMedia(){


        $socialmedia = SocialMedia::where('status', 1)->get();


        return $socialmedia;
    }
}
