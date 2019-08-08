<?php
include './customerSpecificFunction.php';
//-- FUNCTIONS --------------------------------------------------------------------------

/**This Metod will be used to call API with POST method  */
function callAPIPOST($url, $payload = "{}", $headers = array(), $logIdentifier = '') {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    debugLog(" callAPIPOST : ".$logIdentifier);
    debugLog("Request URL : ".$url);
    debugLog(var_dump($headers));
    debugLog(gettype($headers));
    debugLog("Headers :".json_encode($headers));
    debugLog("Payload :".json_encode($payload));
    debugLog("Resposne : ".$response);
    curl_close($curl);
    //curl_close($curl);

    if ($err) {
        debugLog($logIdentifier." : cURL Error # : " . $err);
    } else {
        return $response;
    }
}


/** Delete Function for callback */
function callAPIDELETE($url, $payload = "{}", $headers = array(), $logIdentifier = '') {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    debugLog(" callAPIDELETE : ".$logIdentifier);
    debugLog("Request URL : ".$url);
    debugLog("Headers :".json_encode($headers));
    debugLog("Payload :".json_encode($payload));
    debugLog("Resposne : ".$response);
    curl_close($curl);

    if ($err) {
        debugLog($logIdentifier." : cURL Error # : " . $err);
    } else {
        return $response;
    }
}



/**This Metod will be used to call API with GET method  */
function callAPIGET($url, $headers = array(), $logIdentifier = '') {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYPEER => false,
        // CURLOPT_HTTPHEADER => array(
        // "cache-control: no-cache",
        // "content-type: application/json"
        // ),
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    debugLog("callAPIGET : ".$logIdentifier);
    debugLog("Request URL : ".$url);
    debugLog("Headers :".json_encode($headers));
    debugLog("Resposne : ".$response);
    curl_close($curl);

    if ($err) {
        debugLog($logIdentifier." : cURL Error # : " . $err);
    } else {
        return $response;
    }
}


/**This Metod will be used for logging purpose  */
function debugLog($message) {
    $logfile = './logs';

        if (!file_exists($logfile)) {
            mkdir($logfile, 0777, true);
        }

        $log_file_data = $logfile.'/logs_'.date('d-M-Y').'.log';
    $now     = "\n[" . date("Y-M-d H:i:s") . "] ";
    $message = $now . $message;
    error_log($message, 3, $log_file_data);
}

/**This Fuction can be utilized to query the database....*/
function ameyoDBQuery($query) {
    debugLog("host=".AMEYO_DATABASE_HOST." user=".AMEYO_DATABASE_USER." dbname=".AMEYO_DATABASE_NAME);
    $conn = pg_connect("host=".AMEYO_DATABASE_HOST." user=".AMEYO_DATABASE_USER." dbname=".AMEYO_DATABASE_NAME);
    if (!$conn) {
        debugLog("--> An error occured while making connection to the Ameyo DB.\n");
        exit;
        }

    $res = pg_query($query);

    if (!$res) {
        debugLog("--> An error occured while quering the Ameyo DB.\n");
        exit;
        }

    $results = array();
    while($row = pg_fetch_assoc($res))
    {
        $result = array();
        $result = $row;
        array_push($results, $result);
    }
    pg_close($conn);
    return json_encode($results);
}


/**This Fuction can be utilized to query the reports database....*/
function reportsDBQuery($query) {
    debugLog("host=".REPORTS_DATABASE_HOST." user=".REPORTS_DATABASE_USER." dbname=".REPORTS_DATABASE_NAME);
    $conn = pg_connect("host=".REPORTS_DATABASE_HOST." user=".REPORTS_DATABASE_USER." dbname=".REPORTS_DATABASE_NAME);
    if (!$conn) {
        debugLog("--> An error occured while making connection to the Reports DB.\n");
        exit;
        }

    $res = pg_query($query);

    if (!$res) {
        debugLog("--> An error occured while quering the Reports DB.\n");
        exit;
        }

    $results = array();
    while($row = pg_fetch_assoc($res))
    {
        $result = array();
        $result = $row;
        array_push($results, $result);
    }
    pg_close($conn);
    return json_encode($results);
}



/** 
* Converts bytes into human readable file size. 
* 
* @param string $bytes 
* @return string human readable file size (2,87 Мб)
* @author Mogilev Arseny 
*/ 
function FileSizeConvert($bytes)
{
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}






//---------------------------------------------------------------------------------------------

function pingSession($sessionId) {
    $headers = array();
    $headers['Content-Type'] = 'application/x-www-form-urlencoded';
    
    //data={"sessionId":"d707-5cdbdeaa-ses-CustomerManager-lx9Ow4X1-5","sessionPushSeqNo":"0"}
    $payload = array();
    $payload['sessionId']=$sessionId;
    $payload['sessionPushSeqNo']="0";
    $payload = json_encode($payload);

    $url = SERVER_URL.":".SERVER_PORT."/ameyowebaccess/command/?command=ping-session";
    $result  = callAPIPOST($url, "data=".$payload, $headers, $logIdentifier = 'PingSession');
    $result = json_decode($result);
    //{"alive":true,"serverTime":1562150869167}
    //{"status":"error","reason":"Invalid session Id","details":"Invalid session Id"}

    $returnData = 'false';
    try {
        if($result->alive == true || $result->alive == 'true'  )
        $returnData = 'true';
    } catch(Exception $e) {
        $returnData = 'false';
    }
    return $returnData;
}

function doLogin($userId, $password, $force="true") {
    $headers = array();
    $headers['Content-Type'] = 'application/x-www-form-urlencoded';

    //data={"password": "CustomerManager","terminal": "10.10.2.104","userId": "CustomerManager"}
    $payload = array();
    $payload['password']=$password;
    $payload['userId']=$userId;
    $payload['terminal']="10.10.2.104";
    $payload = json_encode($payload);

    if($force == "false")
        $url = SERVER_URL.":".SERVER_PORT."/ameyowebaccess/command?command=login";
    else {
        $url = SERVER_URL.":".SERVER_PORT."/ameyowebaccess/command?command=force-login";       
    }

    $result  = callAPIPOST($url, "data=".$payload, $headers, $logIdentifier = 'doLogin');
    $result = json_decode($result);

    $returnData = 'false';

    try {
        $returnData = $result->sessionId;
    } catch(Exception $e) {
        $returnData = 'false';
    }
    return $returnData;
}


function getSessionId($userId, $password, $force="true") {
// check the db for live sessionId of CustomerManager; 
$isAlreadyLoggedIn = false;
   $query = "select ush.session_id, ush.login_time, users.user_id, users.user_type from  (select * from user_session_history where logout_time is null ) as ush join users on users.user_id = ush.user_id where users.user_type = 'CustomerManager'";
/*    debugLog("Making Query For to Check Live CustomerManager User Session");
    debugLog("Query : ".$query);
    echo $query.'\n';
    $result = ameyoDBQuery($query);
    debugLog("Result : ".$result).'\n';
    //echo "Result without decoding: ".$result.'\n';
    $result = json_decode($result);
    echo "Result after decodigng--not yet";
    print_r($result);
    echo "Number OF Records :".count($result).'\n';
*/

        debugLog($query);
        $result = ameyoDBQuery($query);
        debugLog("Response : ".$result);
        $result = json_decode($result);


if(count($result) > 0) {
$isAlreadyLoggedIn = true;
$result = $result[0];
//$returnData = $result->lead_id;
$cmSessionID = $result->session_id;
}

//if Customer Manager is logged in just use the live session id :
echo "IsAlreadyLoggedIn = ".$isAlreadyLoggedIn.'\n';

// else if the customer manager is not loggedin login using doLogin()
return doLogin($userId, $password, $force);


}



function doLogout($sessionId) {
    $headers = array();
    $headers['Content-Type'] = 'application/x-www-form-urlencoded';

    //data={ "sessionId":"d707-5cdbdeaa-ses-CustomerManager-ZwqhNWIv-9" } 
    $payload = array();
    $payload['sessionId']=$sessionId;
    $payload = json_encode($payload);

    $url = SERVER_URL.":".SERVER_PORT."/ameyowebaccess/command?command=logout";
    $result  = callAPIPOST($url, "data=".$payload, $headers, $logIdentifier = 'doLogout');
    $result = json_decode($result);

    $returnData = 'true';
    return $returnData;
}

function uploadContact($cmSessionId, $campaignId, $leadId, $customerRecords ) {
    $headers = array();
    $headers['Content-Type'] = 'application/x-www-form-urlencoded';


    $payload = '{"campaignId":"'.$campaignId.'","leadId":'.$leadId.',"sessionId":"'.$cmSessionId.'","properties":{"update.customer":true,"migrate.customer":true},"numAttempts":"X","Status":"NOT_TRIED","customerRecords":'.$customerRecords.'}';

    $url = SERVER_URL.":".SERVER_PORT."/ameyowebaccess/command/?command=uploadContacts";
    $result  = callAPIPOST($url, "data=".$payload, $headers, $logIdentifier = 'uploadContact');
    $result = json_decode($result);

    $returnData = 'false';

    try {
        $returnData = $result;

    } catch(Exception $e) {
        $returnData = 'false';
    }
    return $returnData;
}

//command,campaignIdForInboundCampaign,callbackTime,userId,customerId,callbackPhone
function addCallback($cmSessionId, $campaignId, $callbackTime, $phone, $userId, $customerId='' ) {
    $headers = array();
    $headers['Content-Type'] = 'application/x-www-form-urlencoded';


    if($customerId == '' || $customerId == '-1' || $customerId == null || $customerId == 'null') {
        $payload = '{"sessionId":"'.$cmSessionId.'","campaignId":'.$campaignId.',"callBackTime":"'.$callbackTime.'","isSelfCallBack":"true","userId":"'.$userId.'","callBackHandlerType":"voice.campaign.callback.handler","callBackProperties":{"phone":"'.$phone.'"}}';
    } else {
        $payload = '{"sessionId":"'.$cmSessionId.'","campaignId":'.$campaignId.',"callBackTime":"'.$callbackTime.'","isSelfCallBack":"true","userId":"'.$userId.'","callBackHandlerType":"voice.campaign.callback.handler","callBackProperties":{"customerId":"'.$customerId.'","phone":"'.$phone.'"}}';
    }

    echo $payload;
    debugLog($payload);
    $url = SERVER_URL.":".SERVER_PORT."/ameyowebaccess/command/?command=addCallback";
    $result  = callAPIPOST($url, "data=".$payload, $headers, $logIdentifier = 'addCallback');
    echo $result;
    debugLog($result);

    $result = json_decode($result);
    $returnData = 'false';

    try {
        $returnData = $result;

    } catch(Exception $e) {
        $returnData = 'false';
    }
    return $returnData;
}



//command,campaignIdForInboundCampaign,callbackTime,userId,customerId,callbackPhone
function removeCallback($cmSessionId, $campaignId, $phone, $customerId='' ) {

	if($campaignId != null && $campaignId != '') {
		if($customerId != null && $customerId != '' && $customerId != '-1') {
			$query = "select id from campaign_customer_callback where campaign_id=".$campaignId." and customer_id=".$customerId;
		} else if($phone != null && $phone != '' ) {
			$query = "select id from campaign_customer_callback where campaign_id=".$campaignId." and phone ='".$phone."'";
		} else {
			return "{'status':'ERROR', 'reason':'No customerId or phone found'}";
		}
	} else {
		return "{'status':'ERROR', 'reason':'No campaignId found'}";
	}

	$result = ameyoDBQuery($query);
echo "DB Query fro callbackId Result :".$result;
	$result = json_decode($result);

//	$sessionId = getSessionID();
	$returnData = '';
    foreach ($result as $key => $value){
echo "Key : ".$key;        
print_r($value);
$callbackId = $value->id;
echo $callbackId;
        $payload = '{"customerCallbackId":"'.$callbackId.'"}';
echo "payload : key : ".$key."---".$payload;        
   //$headers = array();
            //$headers['Content-Type'] = 'application/x-www-form-urlencoded';

        $headers= array('Content-Type: application/json','sessionId:'.$cmSessionId);
        $url = SERVER_URL.":".SERVER_PORT."/ameyorestapi/voice/customerCallbacks/".$callbackId;
        $returnData = callAPIDELETE($url, $payload, $headers, 'Removing Callback : Curl');
	echo $returnData;
    }
    return $returnData;
}



function dataTableName($campaignId) {
	$query = "select process_id from campaign_context where id ='".$campaignId."'";
	$result = ameyoDBQuery($query);
	$result = json_decode($result);
echo "gettin process Id: query".$query;	
	$result = $result[0];
	$processId = $result->process_id;
	$query = "select name from process where id =".$processId;
	
	$result = ameyoDBQuery($query);
	$result = json_decode($result);
	$result = $result[0];
	$processName = $result->name;
	
	//$dataTableName = "dt_".strtolower($processName)."_".$processId."_".$processId;
	$dataTableName = "dt_".strtolower($processName)."_".$processId;
echo " datatable name =".$dataTableName;
	return $dataTableName;
}



function validatePsqlTimeStamp($psqltime) {
    // valid format 2019-06-01 00:00:00
    $regex = "....-..-.. ..:..:..";
    
        if(preg_match("/^$regex$/", $psqltime)) { 
            return true; 
        } else 
            return false;
    }



?>
