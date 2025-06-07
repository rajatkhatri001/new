<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JoinUs extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'join_us';

    protected $fillable = [
        'image1',
        'image2',
        'title_1',
        'description_1',
        'title_2',
        'description_2',
        'title_3',
        'description_3',
        'title_4',
        'description_4',
    ];
}
