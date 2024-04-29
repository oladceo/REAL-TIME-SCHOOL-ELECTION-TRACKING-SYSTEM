<?php

$sn = 1;
//get system settings
$stmt = $conn->prepare("SELECT * FROM election_settings WHERE sn=?");
$stmt->bind_param("s", $sn);
$stmt->execute() or die(mysqli_error($conn));
$result = $stmt->get_result();
$getSystemSettings = $result->fetch_array();
$stmt->close();


// Allow for demo mode testing of emails
$demo = ($getSystemSettings['environment'] == "production") ? false : true; // Setting to TRUE will stop the email from sending.

// Set the location of the template file to be loaded
$template_file = "accreditationEmailTemplate.php";

// Get Voters Record and prepare email
$sname = $get_data["sname"];
$fname = $get_data["fname"];
$email = $get_data["email"];
$voterVin = $get_data["vin"];
$electionDeadline = date("d-m-Y, h:i a", strtotime($getSystemSettings["deadline"]));
$url = $getSystemSettings['qr_ip_address'] . "/index?id=" . $email;

// Generate the QR code URL
$qrCodeURL = $getSystemSettings['qr_ip_address'] . "/index?id=" . $email;

// Set the email 'from' information
$email_from = "Real Time Election <Zooomnaija@gmail.com>";

// Create the email headers to begin the email
$email_headers = "From: " . $email_from . "\r\nReply-To: " . $email_from . "\r\n";
$email_headers .= "MIME-Version: 1.0\r\n";
$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

// Load in the template file for processing (after we make sure it exists)
if (file_exists($template_file)) {
    $email_message = file_get_contents($template_file);

    // Replace placeholders with actual data
    $email_message = str_replace("{SITE_ADDR}", $getSystemSettings['qr_ip_address'], $email_message);
    $email_message = str_replace("{EMAIL_TITLE}", "Successful Accreditation", $email_message);
    $email_message = str_replace("{CUSTOM_URL}", $url, $email_message);
    $email_message = str_replace("{FNAME}", $fname, $email_message);
    $email_message = str_replace("{SNAME}", $sname, $email_message);
    $email_message = str_replace("{VIN}", $voterVin, $email_message);
    $email_message = str_replace("{TO_EMAIL}", $email, $email_message);
    $email_message = str_replace("{ACCREDITATION_ID}", $accreditationID, $email_message);
    $email_message = str_replace("{DEADLINE}", $electionDeadline, $email_message);
    $email_message = str_replace("{QRCODE_URL}", $qrCodeURL, $email_message);
} else {
    die("Unable to locate the template file");
}

// Send the email only if not in demo mode
if (!$demo) {
    if (mail($email, "Successful Accreditation - Start Voting Now!", $email_message, $email_headers)) {
        // Email sent successfully
        $emailStatus = true;
        $emailMessage = "Email sent successfully to " . $email;
        // You can handle logging or any other action here
    } else {
        // Email sending failed
        $emailStatus = false;
        $emailMessage = "Failed to send email to " . $email;
        // You can handle error logging or any other action here
    }
} else {
    // Demo mode, so no emails will be sent
    $emailStatus = false;
    $emailMessage = "Demo mode is enabled. No emails were sent.";
}

// Optionally, you can return a response indicating the email sending status
// You can include this in the response sent back to the client in your main PHP file
// For example:
$response['emailStatus'] = $emailStatus;
$response['emailMessage'] = $emailMessage;
