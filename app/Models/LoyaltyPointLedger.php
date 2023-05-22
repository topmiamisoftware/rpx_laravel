<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

/**
 * @property float|int|mixed $loyalty_amount
 * @property int             $user_id
 * @property string          $uuid
 * @property int             $business_id
 * @property string          $type
 */
class LoyaltyPointLedger extends Model
{
    use SoftDeletes;

    public $table = 'loyalty_point_ledger';

    public $fillable = ['uuid', 'business_id', 'loyalty_amount', 'user_id', 'type'];

    public function business()
    {
        return $this->hasOne('App\Models\Business', 'id', 'business_id');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->spotbieUser->user_type === 4)
        {
            $lpLedger = $user->loyaltyPointLedger()
                ->where(function ($query) {
                    $query->where('type', 'points')
                        ->orWhere('type', 'reward_expense');
                })
                ->with('business', function ($query) {
                    $query->with('spotbieUser');
                })
                ->orderBy('loyalty_point_ledger.created_at', 'desc')
                ->paginate(10);
        }
        else
        {
            $lpLedger = $user->business->loyaltyPointLedger()->paginate(10);
        }

        return response([
            'ledger' => $lpLedger,
        ]);
    }
}
