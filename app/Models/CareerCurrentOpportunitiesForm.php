<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerCurrentOpportunitiesForm extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'career_current_opportunities_form';

    protected $fillable = [
        'opportunities_id',
        'name',
        'email',
        'subject',
        'resume',
        'message',
    ];
}
