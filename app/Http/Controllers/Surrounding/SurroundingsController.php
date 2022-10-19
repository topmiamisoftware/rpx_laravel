<?php

namespace App\Http\Controllers\Surrounding;

use App\Http\Controllers\Controller;
use App\Services\SurroundingsApi;
use Illuminate\Http\Request;
use App\Models\Business;

class SurroundingsController extends Controller
{
    private $apiService;

    function __construct(){
        $this->apiService = new SurroundingsApi();
    }

    public function pullInfoObject(Request $request){
        $response = array(
            'success' => true,
            'data' => $this->apiService->pullInfoObject($request),
        );

        return response($response);
    }

    public function searchBusinesses(Request $request){
        $response = array(
            'success' => true,
            'data' => $this->apiService->searchBusinesses($request),
        );

        return response($response);
    }

    public function searchEvents(Request $request){
        $response = array(
            'success' => true,
            'data' => $this->apiService->searchEvents($request),
        );

        return response($response);
    }

    public function getEvent(Request $request){
        $response = array(
            'success' => true,
            'data' => $this->apiService->getEvent($request),
        );

        return response($response);
    }

    public function getClassifications(Request $request){
        $response = array(
            'success' => true,
            'data' => $this->apiService->getClassifications($request),
        );

        return response($response);
    }

    public function getSbCommunityMembers(Request $request){
        $response = array(
            'success' => true,
            'data' => $this->nearByBusinessList($request)
        );

        return response($response);
    }

    public function nearByBusinessList(Request $request){
        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string|numeric'
        ]);

        $categories = json_decode($validatedData['categories']);

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];

        $data = Business::select(
            'business.qr_code_link', 'business.name', 'business.categories', 'business.description',
            'business.photo', 'business.qr_code_link', 'business.loc_x', 'business.loc_y',
            'spotbie_users.user_type',
            'loyalty_point_balances.balance', 'loyalty_point_balances.loyalty_point_dollar_percent_value',
        )
        ->join('spotbie_users', 'business.id', '=', 'spotbie_users.id')
        ->join('loyalty_point_balances', function ($join){
            $join->on('business.id', '=', 'loyalty_point_balances.business_id')
            ->where('loyalty_point_balances.balance', '>', 0)
            ->where('loyalty_point_balances.loyalty_point_dollar_percent_value', '>', 0);
        })
        ->where('business.is_verified', 1)
        ->whereJsonContains('business.categories', $categories)
        ->whereRaw("(
            (business.loc_x = $loc_x AND business.loc_y = $loc_y)
            OR (
                    ABS (
                            SQRT    (
                                        (POWER ( (business.loc_x - $loc_x), 2) ) +
                                        (POWER ( (business.loc_y - $loc_y), 2) )
                                    )
                        )
                    <= 0.1
                )
        )")
        ->has("rewards")
        ->inRandomOrder()
        ->limit(8)
        ->get();

        return $data;
    }
}
