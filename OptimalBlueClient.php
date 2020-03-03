<?php

class OptimalBlueClient
{

    const consumerUrl = "https://marketplace.optimalblue.com/consumer/api/";
    const fullUrl = "https://marketplace.optimalblue.com/full/api/";
    const loginUrl = "https://login.microsoftonline.com/marketplaceauth.optimalblue.com/oauth2/token";
	const supportUrl = "https://marketplace.optimalblue.com/support/api/";



    private $businessChannelId;
    private $originatorId;
    private $token;
    private $tokenExp;
    public  $searchId;
    public  $productId;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $grantType
     * @param string $resource
     * @param integer $businessChannelId
     * @param integer $originatorId
     * @param array $config
     * @param boolean $debug
     */
    public function __construct($clientId, $clientSecret, $grantType, $resource, $businessChannelId, $originatorId, $config = array(), $debug = false)
    {
    	LogAction::obclog("OBC: construct");
        if($debug) {
            $config['debug'] = true;
        }

        $this->businessChannelId = $businessChannelId;
        $this->originatorId = $originatorId;
        $this->authenticate($clientId, $clientSecret, $grantType, $resource);
    }

    /**
     * Authenticate
     * @param string $clientId
     * @param string $clientSecret
     * @param string $grantType
     * @param string $resource
     * @return mixed
     */
    public function authenticate($clientId, $clientSecret, $grantType, $resource) {

    	LogAction::obclog("OBC: authenticate");
        $curl = curl_init();

        $data = sprintf("client_id=%s&client_secret=%s&grant_type=%s&resource=%s",urlencode($clientId),urlencode($clientSecret),urlencode($grantType),urlencode($resource));

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::loginUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));

        $response = json_decode(curl_exec($curl));

        if(!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        } else {

            if (property_exists($response,'access_token')) {
                $this->token = $response->access_token;
                $this->tokenExp = $response->expires_on;
            }
            else return false;
        }

        curl_close($curl);

        return $response;
    }

    public function getToken()
    {
       return $this->token; 
    }

    public function getTokenExpiration() {
        return $this->tokenExp;
    }

    /*
     * BEST EXECUTION SEARCH - "post"
     * @param $data
     * @return mixed
     * url - https://marketplace.optimalblue.com/consumer/api/businesschannels/{businessChannelId}/originators/{originatorId}/bestexsearch
     */
    public function bestExecutionSearch($data)
    {
    	LogAction::obclog("OBC: bestExecutionSearch");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/bestexsearch",self::consumerUrl,$this->businessChannelId,$this->originatorId),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                'Content-type: application/json',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = curl_exec($curl);
        if (!$response) 
           $this->handleError(curl_error($curl),curl_errno($curl));
        curl_close($curl);
        return $response;
    }


    /**
     * BEST EXECUTION PRODUCT DEATILS - "get"
     * @param  $searchId - from BES
     * @param  $productId - from BES
     * @return $response
     * Obtain $searchId, $productId from bestExecutionSearch return result for each product 
     // * url - https://marketplace.optimalblue.com/consumer/api/businesschannels/{businessChannelId}/originators/{originatorId}/bestexsearch/{searchId}/products/{productId}
     */

    public function bESDetails($searchId, $productId)
    {
        
    	LogAction::obclog("OBC: bESDetails");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/bestexsearch/%s/products/%s",self::consumerUrl,$this->businessChannelId,$this->originatorId,$searchId,$productId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)

            )

        ));
        $response = curl_exec($curl);

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }



    /*
     * AMORTIZATION SCHEDULE BES "post"
     * @param  $searchId - from BES
     * @param  $productId - from BES
     * @return $response
     * url - https://marketplace.optimalblue.com/consumer/api/businesschannels/64170/originators/749463/bestexsearch/1439838597E1537558649/products/{productId}/amortizationSchedule
     */
    public function bESAmortizationSchedule($searchId,$productId)
    {
        LogAction::obclog("OBC: bESAmortizationSchedule");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/bestexsearch/%s/products/%s/amortizationSchedule",self::consumerUrl,$this->businessChannelId,$this->originatorId,$searchId,$productId),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                'Content-type: application/json',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = curl_exec($curl);
        if (!$response) 
           $this->handleError(curl_error($curl),curl_errno($curl));
        curl_close($curl);
        return $response;
    }




/*
     * AMORTIZATION SCHEDULE FPS "get"
     * @param  $searchId - from FPS
     * @param  $productId - from FPS
     * @return $response
     * url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productsearch/{searchId}/products/{productId}/amortizationSchedule

     */
    public function fPSAmortizationSchedule($searchId,$productId)
    {
        LogAction::obclog("OBC: fPSAmortizationSchedule");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productsearch/%s/products/%s/amortizationSchedule",self::fullUrl,$this->businessChannelId,$this->originatorId,$searchId,$productId),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                'Content-type: application/json',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = curl_exec($curl);
        if (!$response) 
           $this->handleError(curl_error($curl),curl_errno($curl));
        curl_close($curl);
        return $response;
    }


     /**
     * FULL PRODUCT SEARCH - "post"
     * @param $data
     * @return $response
     // * url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productsearch
    */
    public function fullProductSearch($data)
    {
        LogAction::obclog("OBC: fullProductSearch");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productsearch",self::fullUrl,$this->businessChannelId,$this->originatorId),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                'Content-type: application/json',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = curl_exec($curl);

        if (!$response) {
           $this->handleError(curl_error($curl),curl_errno($curl));
        }
        else
            if (property_exists($response,'searchId')) {
                $this->searchId = $response->searchId;
                $this->productId = $response->productId;
            }

        curl_close($curl);

        return $response;
    }


    /**
     * FULL PRODUCT SEARCH  WITH QM TESTING - "post"
     * @param $data
     * @return $response
     // * url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productsearchwithqm
     */
    public function fPSWithQM($data)
    {
        LogAction::obclog("OBC: fPSWithQM");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productsearchwithqm",self::fullUrl,$this->businessChannelId,$this->originatorId),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                'Content-type: application/json',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
           $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * FULL PRODUCT SEARCH - PRODUCTGROUPS - "post"
     * @param $data
     * @return $response
     // * url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productgroupsearch/{searchId}/productgroups/{productGroupId}/BestPrice

     */
    public function fPSProductGroups($data)
    {
    	LogAction::obclog("OBC: fPSProductGroups");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productgroupsearch",self::fullUrl,$this->businessChannelId,$this->originatorId),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                'Content-type: application/json',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
           $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }


    /**
     *  FULL PRODUCT SEARCH - PRODUCT DETAILS - "get".
     *  @param $searchId
     *  @param $productId
     *  @return $response
     // * url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productsearch/{searchId}/products/{productId}
    */
    public function fPSProductDetails($searchId, $productId)
    {
        LogAction::obclog("OBC: fPSProductDetails");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productsearch/%d/products/%d",self::fullUrl,$this->businessChannelId,$this->originatorId,$searchId,$productId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

   

    

    /**
     *  FULL PRODUCT SEARCH - PRODUCT GROUP DEATILS FOR BEST PRICE - "get"
     * @param  $searchId
     * @param  $productGroupId
     * @return $response
     // * URL - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productgroupsearch/{searchId}/productgroups/{productGroupId}/BestPrice
    */
    public function fPSProductGroupDetailsBestPrice($searchId,$productGroupId)
    {
    	LogAction::obclog("OBC: fPSProductGroupDetailsBestPrice");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productgroupsearch/%d/productgroups/%d/BestPrice",self::fullUrl,$this->businessChannelId,$this->originatorId,$searchId,$productGroupId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * FULL PRODUCT SEARCH - GUIDELINE DOCUMENTS - "get".
     * @param $isIndex
     * @param $value
     * @return $response
     // * url - https://marketplace.optimalblue.com/full/api/guideline?isIndex={isIndex}&value={value}
     */
    public function fPSGuidelineDocument($isIndex, $value)
    {
    	LogAction::obclog("OBC: fPSGuidelineDocument");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sguideline?isIndex=%b&value=%s", self::fullUrl, $isIndex, $value),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * FULL PRODUCT SEARCH - INELIGIBLE PRODUCTS - "get"
     * @param $searchId
     * @return $response
     // * Url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productsearch/{searchId}/ineligible
    */
    public function fPSIneligibleProducts($searchId)
    {
    	LogAction::obclog("OBC: fPSIneligibleProducts");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productsearch/%s/ineligible",self::fullUrl,$this->businessChannelId,$this->originatorId,$searchId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));
  

        $response = curl_exec($curl);

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * FULL PRODUCT SEARCH -  LENDER FEES - "get"
     * @param $searchId
     * @param $productId
     * @return $response
     // * Url - https://marketplace.optimalblue.com/full/api/businesschannels/{businessChannelId}/originators/{originatorId}/productsearch/{searchId}/products/{productId}/lenderfees
    */
    public function fPSLenderFees($searchId,$productId)
    {
    	LogAction::obclog("OBC: fPSLenderFees");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/originators/%d/productsearch/%d/products/%d/lenderfees",self::fullUrl,$this->businessChannelId,$this->originatorId,$searchId,$productId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    

    /**
     * MARKET SUPPORT SERVICES AVALILABLE STARTEGIES - "get"
     * @return $response
     // * url - https://marketplace.optimalblue.com/support/api/strategies
     */
    public function mSSAvailableStrategies()
    {
    	LogAction::obclog("OBC: mSSAvailableStrategies");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sstrategies",self::supportUrl),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));
        $response = json_decode(curl_exec($curl));
        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * MARKET SUPPORT SERVICES AVALILABLE STARTEGIES - CURRENT SERVICER - "get" 
     * @return $response
     // * URL - https://marketplace.optimalblue.com/support/api/currentservicers
     */
    public function mSSCurrentServicer()
    {
    	LogAction::obclog("OBC: mSSCurrentServicer");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%scurrentservicers",self::supportUrl),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * MARKET SUPPORT SERVICES AVALILABLE STARTEGIES - CUSTOM FIELDS - "get"
     * @return $response
     // * https://marketplace.optimalblue.com/support/api/businesschannels/{businessChannelId}/customquestions
     */
    public function mSSCustomFields()
    {
    	LogAction::obclog("OBC: mSSCustomFields");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/customquestions",self::supportUrl, $this->businessChannelId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * MARKET SUPPORT SERVICES AVALILABLE STARTEGIES - EXTERNAL STATUS - "get"
     * @return $response
     // * https://marketplace.optimalblue.com/support/api/businesschannels/{businessChannelId}/externalstatuses
     */
    public function mSSExternalStatus()
    {
    	LogAction::obclog("OBC: mSSExternalStatus");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/externalstatuses",self::supportUrl, $this->businessChannelId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * MARKET SUPPORT SERVICES AVALILABLE STARTEGIES - LEAD SOURCE TYPE - "get" 
     * @return $response
     // * url - https://marketplace.optimalblue.com/support/api/businesschannels/{businessChannelId}/leadsources
     */
    public function mSSLeadSource()
    {
    	LogAction::obclog("OBC: mSSLeadSource");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sbusinesschannels/%d/leadsources",self::supportUrl, $this->businessChannelId),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * MARKET SUPPORT SERVICES AVALILABLE STARTEGIES - STATE AND COUNTY - "get"
     * @return $response
     // * url - https://marketplace.optimalblue.com/support/api/stateswithcounties
     */
    public function mSSStateAndCounty()
    {
    	LogAction::obclog("OBC: mSSStateAndCounty");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf("%sstateswithcounties",self::supportUrl),
            CURLOPT_FAILONERROR => 0,
            CURLOPT_HTTPHEADER => array(
                'Api-version: 3',
                sprintf('Authorization: Bearer %s',$this->token)
            )
        ));

        $response = json_decode(curl_exec($curl));

        if (!$response) {
            $this->handleError(curl_error($curl),curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    private function handleError($error, $errorNo) {
    	LogAction::obclog("OBC: handleError");
        die('Error: "' . $error . '" - Code: ' . $errorNo);
    }

}
