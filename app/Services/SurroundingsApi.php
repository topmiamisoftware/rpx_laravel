<?php

namespace App\Services;

use Illuminate\Http\Request;

class SurroundingsApi
{

    private $currentSearchType;

    private $yelpApiKey = "Bearer GsqTZZ0BaqQgYeJmQ692lddjO7A1EbCERqED66KcOoWpqDmly3N_dW-_yxJkTDIQbQEGlL3Mwfhwyvf0Ww0E7-KBuIrld0-VJ_8pIw7agmyj9RbZJbAMsvGQj8MkXnYx";
        
    private $ticketMasterDiscovery = "https://app.ticketmaster.com/discovery/v2/events.json?apikey=7g94WBVHVpPZfTD7ANdBQdC438WXCHOj";

    private $ticketMasterDiscoveryClassifications = "https://app.ticketmaster.com/discovery/v2/classifications.json?apikey=7g94WBVHVpPZfTD7ANdBQdC438WXCHOj";

    function __construct(){}

    public function pullInfoObject(Request $request){
        
        $serviceUrl = $request->config_url;

        $curl = curl_init($serviceUrl);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $this->yelpApiKey
        ));
        
        $curlResponse = curl_exec($curl);

        if ($curlResponse === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('curl error');
        }

        curl_close($curl);

        if (isset($decoded1->response->status) && $decoded1->response->status == 'ERROR')
            die('error occured: ' . $decoded1->response->errormessage);         

        return json_decode($curlResponse);

    }

    public function searchBusinesses(Request $request){
        
        $serviceUrl = $request->config_url;

        $curl = curl_init($serviceUrl);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $this->yelpApiKey
        ));

        $curlResponse = curl_exec($curl);

        if ($curlResponse === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('curl error');
        }

        curl_close($curl);

        if (isset($decoded1->response->status) && $decoded1->response->status == 'ERROR')
            die('error occured: ' . $decoded1->response->errormessage);

        return json_decode($curlResponse);

    }
    
    public function searchEvents(Request $request){
        
        $serviceUrl = $this->ticketMasterDiscovery . "&" . $request->config_url;

        $curl = curl_init($serviceUrl);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curlResponse = curl_exec($curl);

        if ($curlResponse === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('curl error');
        }

        curl_close($curl);

        if (isset($decoded1->response->status) && $decoded1->response->status == 'ERROR')
            die('error occured: ' . $decoded1->response->errormessage);          

        return json_decode($curlResponse);

    }
    
    public function getEvent(Request $request){

        $serviceUrl = $this->ticketMasterDiscovery . "&" . $request->config_url;

        $curl = curl_init($serviceUrl);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curlResponse = curl_exec($curl);

        if ($curlResponse === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('curl error');
        }

        curl_close($curl);

        if (isset($decoded1->response->status) && $decoded1->response->status == 'ERROR')
            die('error occured: ' . $decoded1->response->errormessage);          

        return json_decode($curlResponse);

    }

    public function getClassifications(Request $request){
        
        $serviceUrl = $this->ticketMasterDiscoveryClassifications;

        try{

            $curl = curl_init($serviceUrl);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $curlResponse = curl_exec($curl);

            // Check the return value of curl_exec(), too
            if ($curlResponse === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }

            curl_close($curl);

        } catch(Exception $e){
            
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }

        if (isset($decoded1->response->status) && $decoded1->response->status == 'ERROR')
            die('error occured: ' . $decoded1->response->errormessage);           

        return json_decode($curlResponse);

    }    

}