<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemLedger extends Model
{
    
    use HasFactory, SoftDeletes;

    public $table = 'loyalty_point_ledger';

}
