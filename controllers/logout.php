<?php
session_start();
include("db_connect.php");

//UPDATE ONLINE STATUS
$sql_u_online_s = mysqli_query($conn, "UPDATE election_voters SET online_status='online' WHERE vin='" . $_SESSION['vin'] . "'") or die(mysqli_error($conn));

$selectedVariables = array(
    'accreditationID', 'vin', 'adminFname', 'adminLname', 'adminID', 'adminEmail', 'adminRole', 'portalAccess', 'voteSessionID'
);

foreach ($selectedVariables as $variable) {
    if (isset($_SESSION[$variable])) {
        unset($_SESSION[$variable]);
    }
}
header("location: .././");
