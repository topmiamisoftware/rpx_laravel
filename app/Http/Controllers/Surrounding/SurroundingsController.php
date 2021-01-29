<?php

namespace App\Http\Controllers\Surrounding;

use App\Http\Controllers\Controller;

use App\Services\SurroundingsApi;

use Illuminate\Http\Request;

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

}
