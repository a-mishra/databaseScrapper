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

    echo "\n--------------------- Connected successfully --------------------------------------\n";

//--- PHASE : 2 ------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

$customTableNames = CUSTOM_TABLE_NAMES;
$customTableColumns = CUSTOM_TABLE_COLUMNS;

    for($i = 0 ; $i < count($customTableNames); $i++ ) {
        $tableName = $customTableNames[$i];
        $columns = $customTableColumns[$i]; 
        
        if(substr($tableName, -4) == 'cstm'){
            $keyField = 'id_c';
        } else {
        $keyField = 'id';
        }

        $sql = "SELECT $keyField, $columns from $tableName";

        $result = $conn->query($sql);
        $numberOfUpdatesMade = 0;

        if ($result->num_rows > 0) {
            
            echo "\n Total Number of rows found : ".$result->num_rows."\n";

            while($row = $result->fetch_assoc()) {
                $shouldUpdate = false;
                foreach ($row as $key => $value) {
                    //print "$key => $value\n";
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
                    echo ("\n\nUpdating Following Record : ");
                    echo (json_encode($row));
                    $numberOfUpdatesMade++;

                    // creting update query-------
                    $setString = '';
                    foreach ($row as $key => $value) {
                        if($key == $keyField) {
                            continue;
                        }
                        else {
                            $setString = $setString + "$key = '$row[$key]',";
                        }
                    }
                    $setString = substr($setString, 0, strlen($setString)-1 );

                    $updateQuery = "UPDATE ".$tableName." SET ".$setString." WHERE ".$keyField." = '".$row[$keyField]."'";
                    echo "\n\n Update Query : ".$updateQuery;
                    
                }
            }
        } else {
            echo "0 results";
        }

        echo ("\n\n----------- Number of Updates Made : ".$numberOfUpdatesMade."-------------------------------");
    }

//--- WRAP UP --------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

    $conn->close();

    echo "\n";

?>
