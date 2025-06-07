<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientReview extends Model
{
    use SoftDeletes;

    protected $table = 'client_reviews';

    protected $fillable = ['image', 'description', 'name', 'status'];
}
