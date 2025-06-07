<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsPage extends Model
{
    use SoftDeletes;

    protected $table = 'terms_pages';

    protected $fillable = [
        'title_1',
        'title_1_description',
        'title_2',
        'title_2_description',
        'terms_file_title',
        'terms_file_name',
    ];
}
