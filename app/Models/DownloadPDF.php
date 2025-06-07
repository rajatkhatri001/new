<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DownloadPDF extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'download_pdf';

    protected $fillable = [
        'name',
        'email',
    ];
}
