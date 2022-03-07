<?php

function is_queueit_active($db) {
    $query = $db->prepare("SELECT valor FROM ioc_configuracio WHERE variable = ?");
    $query->execute(array('queueit'));
    $res = $query->fetch(\PDO::FETCH_ASSOC);
    if (intval($res['valor']) === 1) {
        return true;
    }
    return false;
}

function iocqueue($data_dir) {

require_once( __DIR__ .'/Models.php');
require_once( __DIR__ .'/KnownUser.php');

$configText = file_get_contents($data_dir.'integrationconfig.json');
$customerID = "ctti"; //Your Queue-it customer ID
$secretKey = "b9ed1c99-3af6-4f68-a1b2-e7abed577aa5b49dd72d-5f06-4322-a59b-f3606f07c4ec"; //Your 72 char secret key as specified in Go Queue-it self-service platform

$queueittoken = isset( $_GET["queueittoken"] )? $_GET["queueittoken"] :'';

try {
    $fullUrl = getFullRequestUri();
    $currentUrlWithoutQueueitToken = preg_replace("/([\\?&])("."queueittoken"."=[^&]*)/i", "", $fullUrl);

    //Verify if the user has been through the queue
    $result = QueueIT\KnownUserV3\SDK\KnownUser::validateRequestByIntegrationConfig(
       $currentUrlWithoutQueueitToken, $queueittoken, $configText, $customerID, $secretKey);
		
    if($result->doRedirect()) {
        //Adding no cache headers to prevent browsers to cache requests
        header("Expires:Fri, 01 Jan 1990 00:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        //end
    
        if(!$result->isAjaxResult)
        {
            //Send the user to the queue - either because hash was missing or because is was invalid
            header('Location: ' . $result->redirectUrl);		            
        }
        else
        {
            header('HTTP/1.0: 200');
            header($result->getAjaxQueueRedirectHeaderKey() . ': '. $result->getAjaxRedirectUrl());            
        }
		
        die();
    }
    if(!empty($queueittoken) && $result->actionType == "Queue") {        
	//Request can continue - we remove queueittoken form querystring parameter to avoid sharing of user specific token
        header('Location: ' . $currentUrlWithoutQueueitToken);
	die();
    }
} catch(\Exception $e) {
    // There was an error validating the request
    // Use your own logging framework to log the error
    // This was a configuration error, so we let the user continue
    
	print_r($e);
	die('here');

}

}

function getFullRequestUri() {
     // Get HTTP/HTTPS (the possible values for this vary from server to server)
    $myUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http';
    // Get domain portion
    $myUrl .= '://'.$_SERVER['HTTP_HOST'];
    // Get path to script
    $myUrl .= $_SERVER['REQUEST_URI'];
    // Add path info, if any
    if (!empty($_SERVER['PATH_INFO'])) $myUrl .= $_SERVER['PATH_INFO'];

    return $myUrl; 
}
