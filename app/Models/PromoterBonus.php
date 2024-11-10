<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PromoterBonus extends Model
{
    use HasFactory;

    protected $table = 'promoter_bonus';

    protected $fillable = ['business_id', 'lp_amount', 'redeemed', 'user_id', 'promoter_id', 'device_ip', 'device_id', 'expires_at', 'day', 'time_range_1', 'time_range_2', 'time_range_3'];

    /**
     * Scope a query to only include popular users.
     */
    public function scopeIsNotExpired(Builder $query)
    {
        // Scope function to check if expiration_date has not transpired
        return $query->where('expires_at', '>', Carbon::now());
    }

    public function scopeIsNotRedeemed(Builder $query)
    {
        return $query->where('redeemed', '=', 0);
    }

    /**
     * Let's create something out of this.
     */
    public function scopeWithInTimeRange(Builder $query): void
    {
        $now = Carbon::now();
        $nowHour = intval($now->format('g'));
        $nowDay = intval($now->format('N'));
        $amOrPm = $now->format('A');

        Log::info('Day ' .  $nowDay . ' Hour ' . $nowHour . ' amorpm: ' . $amOrPm);

        $query->where('day', '=', $nowDay)
            ->where('time_range_1', '<=', $nowHour)
            ->where('time_range_2', '>=', $nowHour)
            ->where('time_range_3', '=', $amOrPm);
    }

}
