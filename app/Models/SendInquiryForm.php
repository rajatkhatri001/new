<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendInquiryForm extends Model
{
    use SoftDeletes;

    protected $table = 'send_inquiry_forms';

    protected $fillable = [
        'name',
        'email',
    ];
}
