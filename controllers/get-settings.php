<?php
session_start();
include("db_connect.php");

if (isset($_POST['portalAccess'])) {
    $portalAccess = $_POST["portalAccess"];
    $qrUrlAddress = $_POST["qrUrlAddress"];
    $electionStatus = $_POST["electionStatus"];
    $systemEnvironment = $_POST["systemEnvironment"];
    $electionDeadline = date("d-m-Y, h:i a", strtotime($_POST["electionDeadline"]));
    $sn = 1;

    // Prepare SQL to check for changes made 
    $stmt = $conn->prepare("SELECT COUNT(*) FROM election_settings WHERE portalAccess=? AND qr_ip_address=? AND system_status=? AND deadline=? AND environment=? AND sn=?");
    $stmt->bind_param("ssssss", $portalAccess, $qrUrlAddress, $electionStatus, $electionDeadline, $systemEnvironment, $sn);
    $stmt->execute();
    $stmt->bind_result($duplicateCount);
    $stmt->fetch();
    $stmt->close();

    if ($duplicateCount > 0) {
        $status = false;
        $header = 'No Changes!';
        $message = 'There is nothing to update';
        $responseStatus = 'warning';
        $btnText = "Save System Settings";
        $emailStatus = false;
        $emailMessage = "";
    } else {
        // Get system settings
        $stmt = $conn->prepare("SELECT * FROM election_settings WHERE sn=?");
        $stmt->bind_param("s", $sn);
        $stmt->execute() or die(mysqli_error($conn));
        $result = $stmt->get_result();
        $getSystemSettings = $result->fetch_array();
        $stmt->close();

        // If the election status has just been activated, send a general email
        if ($electionStatus == 1 && $getSystemSettings['system_status'] != "1") {
            $emailStatus = true;
            include("sendElectionGeneralEmail.php"); // Send General Email
        } else {
            $emailStatus = false;
            $emailMessage = "";
        }

        // Update system settings
        $stmt = $conn->prepare("UPDATE election_settings SET portalAccess=?, qr_ip_address=?, system_status=?, deadline=?, environment=? WHERE sn=?");
        $stmt->bind_param("ssssss", $portalAccess, $qrUrlAddress, $electionStatus, $electionDeadline, $systemEnvironment, $sn);
        $stmt->execute() or die(mysqli_error($conn));
        $stmt->close();

        $status = false;
        $header = 'Saved';
        $message = 'System Settings have been updated!';
        $responseStatus = 'success';
        $btnText = "Save System Settings";
    }
    $response = array(
        'status' => $status,
        'header' => $header,
        'message' => $message,
        'responseStatus' => $responseStatus,
        'btnText' => $btnText,
        'emailStatus' => $emailStatus,
        'emailMessage' => $emailMessage
    );

    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
}
