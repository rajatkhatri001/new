<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PCHPLTeam extends Model
{
    use HasFactory;use SoftDeletes;

    protected $table = 'p_c_h_p_l_teams';

    protected $fillable = [
        'image',
        'name',
        'designation', 'status',
    ];
}
