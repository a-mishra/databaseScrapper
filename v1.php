<?php
/** Database Scrapper : Zanaco */
//--------------------------------------------------------
/* This file will scrap the given mySql database for provided tables and columns and will hash each 16 digit numbers:
    conditions : only continious 16 digit would be considered ass 16 digit number; */
//--------------------------------------------------------

header("Access-Control-Allow-Origin: *");

include "config/constants.php";
include "library/functions.php";


//--- PHASE : 1 -----------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

    // Create connection
    $conn = new mysqli(SUGAR_DB_HOST, SUGAR_DB_USER, '', SUGAR_DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    debugLog("--------------------- Connected successfully --------------------------------------");

//--- PHASE : 2 ------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

$customTableNames = array("ds_customers_cstm","cases","cases_cstm");
$customTableColumns = array("account_number_c, nrc_c, physical_address_c", "name, description", "summary_c");


    for($i = 0 ; $i < count($customTableNames); $i++ ) {
        $tableName = $customTableNames[$i];
        $columns = $customTableColumns[$i]; 

        
        $keyField = '';
        if(substr($tableName, -4) == 'cstm') {
            $keyField = 'id_c';
        } else {
            $keyField = 'id';
        }

        debugLog("------------ Connected To Table : $tableName ; keyValue = $keyValue -------------------------------");
        
        $sql = "SELECT $keyField, $columns from $tableName";

        $result = $conn->query($sql);
        $numberOfUpdatesMade = 0;

        if ($result->num_rows > 0) {
            
            debugLog(" Total Number of rows found : ".$result->num_rows);

            while($row = $result->fetch_assoc()) {
                $shouldUpdate = false;
                foreach ($row as $key => $value) {
                    if($key == $keyField) {
                        continue;
                    }
                    else {
                        $tempArr = array();
                        $tempArr = checkStringFor16DigitNumberAndLetMeKnowToUpdate($value);
                        if ($tempArr['shouldUpdate'] == true) {
                            $shouldUpdate = true;
                        }
                        $row[$key] =  $tempArr['str'];
                    }
                }
                if($shouldUpdate == true) {
                    // update the row in table for given id_c / id
                    debugLog("Updating Following Record : ". json_encode($row));
                    $numberOfUpdatesMade++;

                    // creting update query-------
                    $setString = ' ';
                    foreach ($row as $key => $value) {
                        if($key == $keyField) {
                            continue;
                        }
                        else {
                            $setString = $setString. "$key = '$row[$key]',";
                        }
                    }
                    $setString = substr($setString, 0, strlen($setString)-1 );

                    $updateQuery = "UPDATE ".$tableName." SET ".$setString." WHERE ".$keyField." = '".$row[$keyField]."'";
                    debugLog("Update Query : ".$updateQuery);

                }
            }
        } else {
            echo "0 results";
        }

        debugLog("----------- Number of Updates Made : ".$numberOfUpdatesMade."-------------------------------");
        debugLog("");

    }

//--- WRAP UP --------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

    $conn->close();

    echo "\n";



//--- Specific Functions for Database Scrapper --------------------------------------------------------------------

function checkStringFor16DigitNumberAndLetMeKnowToUpdate($str) {
    
    /*$str = "this is a 1234542398676545 16digit number 987645672345098700
    thissis line  2 of thew same string 87873388837336t36766272828828783288728278278282
    linr 3865884467444847644746474657 85785856474647 47477575757674  47474747474657564575657485";
    */

    $shouldUpdate = false;
    $matches = array();
    preg_match_all('/[0-9]{16}+/', $str, $matches);

    if ( count($matches) ) {        
        $matchedValues = $matches[0];
        $replacementValues = array();

        if(count($matchedValues)>0){
            $shouldUpdate = true;
        }

        for ($i =0 ; $i<count($matchedValues); $i++) {
            $replacementValues[$i] = hash16digit($matchedValues[$i]);
        }
        //print_r($matchedValues);
        //print_r($replacementValues);

        $str = str_replace($matchedValues, $replacementValues, $str);
    }

    $tempArr =  array();
    $tempArr['shouldUpdate'] = $shouldUpdate;
    $tempArr['str'] = $str;

    /* output : this is a 1234********6545 16digit number 9876********098700
    thissis line  2 of thew same string 87873388837336t3676********28783288********8282
    linr 3865********4847644746474657 85785856474647 47477575757674  4747********57564575657485
    */
return $tempArr;
}




/** This function will hash the 16 digit number */
function hash16digit($input16DigitNumber) {
$returnString = $input16DigitNumber;

    //cross check length of string
    if(strlen($input16DigitNumber) == 16) {
        $returnString = substr($input16DigitNumber, 0, 4)."********".substr($input16DigitNumber, -4);
    }

    return $returnString;
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
    echo("\n".$message);
}

?>
