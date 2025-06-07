<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ContactUsPage extends Model
{
    use SoftDeletes;

    protected $table = 'contact_us_pages';

    protected $fillable = [
        'address',
        'mobile',
        'email',
        'map_iframe',
    ];

    static public function contact(){
        $contact = ContactUsPage::first();

        return $contact;
    }
}
