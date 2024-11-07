<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoterBonus extends Model
{
    use HasFactory;

    protected $table = 'promoter_bonus';

    protected $fillable = ['business_id', 'lp_amount', 'redeemed', 'user_id', 'device_ip', 'device_id', 'expires_at', 'day', 'time_range_1', 'time_range_2'];
}
