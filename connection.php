<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zanaco_suitecrm";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "\nConnected successfully";
echo "\n------------------------------------------------------------------------";


$sql = "SELECT id_c, account_number_c, nrc_c, physical_address_c from ds_customers_cstm";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            print "$key => $value\n";
            if($key == 'id_c'){
                continue;
            }
            else {

            }
        }
    }
} else {
    echo "0 results";
}




$conn->close();

echo "\n";

//-- Inline Functions -----------------------------------------------------------------------

/** replace16Digit numbers */
function replace16digitNumbers() {


}




?>
