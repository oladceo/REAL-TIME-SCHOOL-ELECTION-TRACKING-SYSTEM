<?php

// Allow for demo mode testing of emails
$demo = ($getSystemSettings['environment'] == "production") ? false : true; // Setting to TRUE will stop the email from sending.

// Set the location of the template file to be loaded
$template_file = "generalElectionEmailTemplate.php";

// Get Voters Record and prepare email
$vin = " ";
$stmt = $conn->prepare("SELECT * FROM election_voters WHERE vin != ?");
$stmt->bind_param("s", $vin);
$stmt->execute() or die($stmt->error);
$result = $stmt->get_result();
$stmt->close(); // Close the prepared statement to free up resources


while ($getVotersInfo = $result->fetch_array()) {

    // Fetch voter information
    $sname = $getVotersInfo["sname"];
    $fname = $getVotersInfo["fname"];
    $email = $getVotersInfo["email"];
    $voterVin = $getVotersInfo["vin"];
    $url = $getSystemSettings['qr_ip_address'] . "/accreditation?id=" . $email;

    // Generate the QR code URL
    $qrCodeURL = $getSystemSettings['qr_ip_address'] . "/accreditation?id=" . $email;

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
        $email_message = str_replace("{EMAIL_TITLE}", "Live Election Notification", $email_message);
        $email_message = str_replace("{CUSTOM_URL}", $url, $email_message);
        $email_message = str_replace("{FNAME}", $fname, $email_message);
        $email_message = str_replace("{SNAME}", $sname, $email_message);
        $email_message = str_replace("{VIN}", $voterVin, $email_message);
        $email_message = str_replace("{TO_EMAIL}", $email, $email_message);
        $email_message = str_replace("{DEADLINE}", $electionDeadline, $email_message);
        $email_message = str_replace("{QRCODE_URL}", $qrCodeURL, $email_message);
    } else {
        die("Unable to locate the template file");
    }

    // Send the email only if not in demo mode
    if (!$demo) {
        if (mail($email, "Important: Your Participation Required in Live Election Now!", $email_message, $email_headers)) {
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
}

// Optionally, you can return a response indicating the email sending status
// You can include this in the response sent back to the client in your main PHP file
// For example:
$response['emailStatus'] = $emailStatus;
$response['emailMessage'] = $emailMessage;
