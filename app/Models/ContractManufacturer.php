<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractManufacturer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contract_manufacturers';

    protected $fillable = [
       'image','status'
    ];
}
