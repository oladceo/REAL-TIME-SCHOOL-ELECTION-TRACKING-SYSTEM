<?php
session_start();

include("db_connect.php");


//VALIDATION OF VOTERS IDENTIFICATION NUMBER (VIN)...... STARTS
if (!empty($_POST["validateVoterVin"]) && isset($_POST["voterVin"], $_POST["email"])) {
    $vin = $_POST["voterVin"];
    $email = $_POST["email"];


    $stmt = $conn->prepare("SELECT * FROM election_voters WHERE vin=? AND email=?");
    $stmt->bind_param("ss", $vin, $email);
    $stmt->execute() or die(mysqli_error($conn));
    $result = $stmt->get_result();
    $get_data = $result->fetch_array();
    $accreditationID = $get_data["accreditationID"];
    $stmt->close();


    // Check if the election is activated and deadline has not exceeded
    $stmt = $conn->prepare("SELECT * FROM election_settings");
    $stmt->execute() or die($stmt->error);
    $result = $stmt->get_result();
    $getSettings = $result->fetch_array();
    $stmt->close();

    // Calculate deadline from today
    $today = new DateTime();
    $deadline = DateTime::createFromFormat('d-m-Y, h:i a', $getSettings["deadline"]);

    if ($deadline !== false) {
        // Check if the deadline is in the future
        if ($today < $deadline) {
            // Calculate the time difference
            $dateDiff = $today->diff($deadline);
            $totalHoursRemaining = $dateDiff->h + ($dateDiff->days * 24);
        } else {
            // The deadline has passed
            $totalHoursRemaining = 0;
        }
    } else {
        // Handle invalid deadline format
        echo "Invalid deadline format!";
        exit(); // Exit the script
    }


    if ($totalHoursRemaining == 0) { // Check election status and time remaining
        $message = "Election is Currently Closed And Accreditation Has Been Suspended!";
        $header = "Accreditation Suspended!";
        $responseStatus = 'warning';
        $emailStatus = false;
        $emailMessage = "";
    } elseif ($get_data && empty($accreditationID)) {

        //Generate a new Accreditation ID and Update in the voters table.

        ///////////////////////////////////////////////////////////////////////////
        $characters = '1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $accreditationID = mysqli_real_escape_string($conn, $randomString);
        ///////////////////////////////////////////////////////////////////////////
        date_default_timezone_set("Africa/Lagos");
        $date = date("j-m-Y, g:i a");


        //UPDATE VOTERS ACCREDITATION ID
        $sql_u_online_s = mysqli_query($conn, "UPDATE election_voters SET accreditationID='$accreditationID', vote_status='active', modified_date='$date' WHERE vin='$vin' AND email='$email' ") or die(mysqli_error($conn));

        include("sendAccreditationEmail.php"); //Send voter an accreditation email

        $header = 'Accredited!';
        $message = 'Your accreditation was successful and your accreditation ID has been sent to your email with other information';
        $responseStatus = 'success';
        $emailStatus = true;
    } elseif ($get_data && !empty($accreditationID)) {
        //Send the voter the existing Accreditation ID and activate status if not activated

        if ($get_data['vote_status'] != "active") {
            //UPDATE VOTERS ACCREDITATION ID
            $sql_u_online_s = mysqli_query($conn, "UPDATE election_voters SET accreditationID='$accreditationID', vote_status='active', modified_date='$date' WHERE vin='$vin' AND email='$email' ") or die(mysqli_error($conn));
        }

        include("sendAccreditationEmail.php"); //Send voter an accreditation email

        $header = 'Accreditation Verified!';
        $message = 'You have been accredited and your accreditation ID has been sent to your email with other information';
        $responseStatus = 'success';
        $emailStatus = true;
    } else {
        $header = 'Accreditation Failed!';
        $message = 'Invalid Voter Identification Number, Please check and try again later!';
        $responseStatus = 'error';
        $emailStatus = false;
        $emailMessage = "";
    }
    $response = array(
        'header' => $header,
        'message' => $message,
        'responseStatus' => $responseStatus,
        'emailStatus' => $emailStatus,
        'emailMessage' => $emailMessage
    );

    header("Content-Type: application/json");

    echo json_encode($response);

    exit();
}
//VALIDATION OF VOTERS IDENTIFICATION NUMBER (VIN)...... ENDS

//VALIDATION OF VOTERS EMAIL...... STARTS
if (!empty($_POST['validateVoterEmail'])) {

    $email = strtolower($_POST["email"]);

    $stmt = $conn->prepare("SELECT * FROM election_voters WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute() or die(mysqli_error($conn));
    $result = $stmt->get_result();
    $getVoterInformation = $result->fetch_array();

    if ($getVoterInformation && $email == strtolower($getVoterInformation["email"])) {
        $votersPassport = "resources/" . $getVoterInformation['passport'];
        $defaultPassport = "images/no-preview.jpeg";
        $_SESSION["vin"] = $getVoterInformation["vin"]; //set VIN into session 
?>

        <script>
            $("#validateMessage").html("<span class='fa fa-spin fa-spinner'></span> Processing please wait...").css("color", "#818181").show();

            $("#accreditationIDPanel").hide(); //hide the Accreditation ID verification fields
            setTimeout(function() {
                $("#emailValidationForm").hide(); //hide email field when processing is showing
                $("#validateMessage").hide();
                $("#accreditationIDPanel").show(); //show the Accreditation ID verification fields
            }, 2000);
        </script>

        <!-- Automatic element centering -->
        <div class="lockscreen-wrapper" id="accreditationIDPanel">
            <!-- Voters name -->
            <div class="lockscreen-name" style="margin-left:30px;color:white;font-family:arial;font-weight:200;text-transform:capitalize">
                <?php echo ucfirst($getVoterInformation['sname'] . " " . $getVoterInformation['fname']); ?>
            </div>

            <div class="lockscreen-item">
                <!-- voters image -->
                <div class="lockscreen-image">
                    <img src="<?php echo ((file_exists("../" . $votersPassport)) ? $votersPassport : $defaultPassport); ?>" alt="Voters passport">
                </div>
                <!-- /.voters-image -->

                <!-- voters vin authentication credentials field (contains the form) -->
                <form class="lockscreen-credentials" id="accreditationAuthForm">
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="Enter Accreditation ID" id="votersAccreditationID" name="votersAccreditationID" style="background-color:#fff" autofocus maxlength="10" <?php echo (($getVoterInformation['vote_status'] != 'active') ? "disabled" : ""); ?>>
                        <input type="hidden" name="email" id="email" value="<?php echo $getVoterInformation['email']; ?>" />
                        <div class="input-group-btn" id="accreditationAuthBtn">
                            <button class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                        </div>
                    </div>
                </form>
                <!-- /.voters vin authentication credentials field -->
            </div>
            <!--qr code display-->
            <center>
                <img style="width:80px;height:80px;margin-top:-25px" id="evoteqr">
            </center>
            <!-- /.Voters help message-item -->
            <div class="help-block text-center" style="color:#ffffff">
                <?php
                function generateRandomString($length)
                {
                    return bin2hex(random_bytes($length / 2)); // More secure random string generation
                }


                //Get ip address from settings for QR    
                $sql_settings = mysqli_query($conn, "SELECT * FROM election_settings WHERE sn='1'") or die(mysqli_error($conn));
                $get_setting = mysqli_fetch_array($sql_settings);

                $randomString = generateRandomString(100); // Generate a random string of 6 characters
                $accreditationLink = $get_setting['qr_ip_address'] . "/accreditation?" . $randomString . "&id=" . $getVoterInformation['email'];

                ?>
                Scan QR code or <a href="<?php echo $accreditationLink; ?>" style="color:#d0ce9f;text-decoration:none">click here </a>for accreditation!
            </div>
            <div class="text-center">
                <div id="accreditationValidationMsg"></div>
                <!--Show VIN Validation Messages here!-->
                <div>&nbsp;</div>
                <a href="index" style="color:#00bc5e;text-decoration:none">Click here to sign in as a different voter</a><br />
                <div>&nbsp;</div>

                <div id="accreditationContent"> </div>
                <!--SHOW CONTENT FOR THE VALIDATION-->
            </div>
        </div>
        <script>
            $(document).ready(function() {
                // Restricts input for the given textbox to the given inputFilter, starts.
                function setInputFilter(textbox, inputFilter) {
                    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
                        textbox.oldValue = "";
                        textbox.addEventListener(event, function() {
                            if (inputFilter(this.value)) {
                                this.oldValue = this.value;
                                this.oldSelectionStart = this.selectionStart;
                                this.oldSelectionEnd = this.selectionEnd;
                            } else if (this.hasOwnProperty("oldValue")) {
                                this.value = this.oldValue;
                                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                            }
                        });
                    });
                }
                // Restrict input to digits and '.' by using a regular expression filter.
                setInputFilter(document.getElementById("votersAccreditationID"), function(value) {
                    return /^\d*$/.test(value);
                });
            });
            //SECTION FOR QR STARTS****************************>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 
            (function() { //FOR QR CODE DISPLAY

                var qr = window.qr = new QRious({
                    element: document.getElementById('evoteqr'),
                    size: 250,
                    value: '<?php echo $accreditationLink; ?>' //url where QR code navigates to
                });
            })();
            //SECTION FOR QR ENDS HERE****************************>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 

            //Validation of voters vin starts
            //alert("hallelujah");
            $('#accreditationAuthForm').submit(function(e) {
                e.preventDefault();
                var accreditationID = $("#votersAccreditationID").val();
                var email = $("#email").val();
                if (accreditationID == "" || email == "") {
                    $("#accreditationValidationMsg").html("<i style='color:red;font-size:13px'>Please enter your Accreditation ID to continue or scan QR code to get accredited </i>").show(); //show validation message onload
                    setTimeout(function() {
                        $("#accreditationValidationMsg").hide();
                    }, 3000);
                } else {
                    $("#accreditationValidationMsg").html("<i style='font-size:13px'> <span class='fa fa-spin fa-spinner'></span> Please wait... </i>").css("color", "#818181").show(); //show validation message onload
                    $.ajax({
                        type: 'POST',
                        url: 'controllers/login',
                        async: false,
                        data: {
                            validateAccreditationID: 1,
                            accreditationID: accreditationID,
                            email: email
                        },
                        success: function(vai) {
                            $("#accreditationContent").html(vai).show();
                        }
                    });
                }
            });
            //Validation of voters vin ends
        </script>
        <!-- /.center -->

    <?php } else { ?>
        <script>
            $("#validateMessage").html("<span class='fa fa-spin fa-spinner'></span> Processing please wait...").css("color", "#ffffff").show();

            setTimeout(function() {
                $("#votersEmail").prop("disabled", false);
                $("#validateMessage").html("<span class='alert alert-danger'>invalid email address, please check and try again!</span>").show();
            }, 2000);
        </script>
    <?php
        unset($_SESSION["vin"]); //unset voters VIN from Session 
    }
    $stmt->close();
    exit();
}
//VALIDATION OF VOTERS EMAIL...... ENDS

//VALIDATION OF VOTERS ACCREDITATION ID...... STARTS
if (!empty($_POST["validateAccreditationID"]) && isset($_POST["accreditationID"])) {
    $vin = $_SESSION["vin"];
    $accreditationID = $_POST["accreditationID"];
    $email = $_POST["email"];
    $vote_status = "active";

    $stmt = $conn->prepare("SELECT * FROM election_voters WHERE vin=? AND email=? AND accreditationID=? AND vote_status=?");
    $stmt->bind_param("ssss", $vin, $email, $accreditationID, $vote_status);
    $stmt->execute() or die(mysqli_error($conn));
    $result = $stmt->get_result();
    $get_data = $result->fetch_array();

    if ($get_data) {

        $_SESSION["accreditationID"] = $get_data["accreditationID"];

        //UPDATE ONLINE STATUS
        $sql_u_online_s = mysqli_query($conn, "UPDATE election_voters SET online_status='online' WHERE vin='$vin' AND email='$email'") or die(mysqli_error($conn));
    ?>
        <script>
            //setting the login process message
            $("#accreditationValidationMsg").html("<span><span class='fa fa-spinner fa-spin'></span> Processing please wait...</span>").css("color", "#818181").show();

            setTimeout(function() { //setting the login message for verification success
                $("#accreditationValidationMsg").html("<span> Confirmed! <i class='fa fa-check'></i></span> || <span class='fa fa-spin fa-spinner'></span> Redirecting...").css("color", "#6edb6e").show();
            }, 2000);

            setTimeout(function() { //go to dashboard once its successful
                window.location.href = "./v-dashboard";
            }, 4000);
        </script>
    <?php
    } else { ?>
        <script>
            $("#accreditationValidationMsg").html("<span class='fa fa-spin fa-spinner'></span> Processing please wait...").css("color", "#818181").show();

            setTimeout(function() {
                $("#accreditationValidationMsg").html("<p class='alert alert-warning' style='font-size:13px'>invalid Accreditation ID, check your email inbox/spam @ <b>" + "<?php echo $email; ?>" + "</b> for a valid accreditation ID!</p>").show();
            }, 2000);
        </script>
        <?php
    }
    exit();
}
//VALIDATION OF VOTERS ACCREDITATION ID...... ENDS

//ADMINISTRATOR VALIDATION FOR AUTHORIZED ENVIRONMENT...... STARTS
if (!empty($_POST["validateAdminLogin"])) {


    $username = $_POST["username"];
    $password = md5($_POST["password"]);
    $status = 'active';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=? AND password=? AND status=?");
    $stmt->bind_param("sss", $username, $password, $status);
    $stmt->execute() or die(mysqli_error($conn));
    $result = $stmt->get_result();
    $get_data = $result->fetch_array();


    //GET ACCESS CONTROL FROM PREFERENCE
    $sqlGetPreferenceData = mysqli_query($conn, "SELECT * FROM election_settings") or die(mysqli_error($conn));
    $getPreferenceData = mysqli_fetch_array($sqlGetPreferenceData);

    if ($get_data) {

        //UPDATE ONLINE STATUS
        $sql_u_online_s = mysqli_query($conn, "UPDATE admins SET onlineStatus='1' WHERE email='$username'") or die(mysqli_error($conn));


        //ASSIGN SESSION FOR ADMINISTRATORS
        $_SESSION['adminFname'] = $get_data["fname"];
        $_SESSION['adminLname'] = $get_data["lname"];
        $_SESSION['adminID'] = $get_data["adminID"];
        $_SESSION['adminEmail'] = $get_data["email"];
        $_SESSION['adminRole'] = $get_data["role"];
        $_SESSION['portalAccess'] = $getPreferenceData["portalAccess"];

        //check for portal access control
        if ($_SESSION['portalAccess'] != "enabled" || $_SESSION['adminRole'] != "superAdmin") {
            session_destroy();
        ?>
            <script>
                $("#validationAdminMsg").html("<span><span class='fa fa-spinner fa-spin'></span> Processing please wait...</span>").css("color", "#ffffff").show();
                setTimeout(function() {
                    $("#loginMsg").html("<span class='alert alert-danger '> Access Denied <i class='fa fa-exclamation-triangle'></i></span>").show();
                }, 2000);
            </script>
        <?php
        }
        ?>
        <script>
            //setting the login process message
            $("#validationAdminMsg").html("<span><span class='fa fa-spinner fa-spin'></span> Processing please wait...</span>").css("color", "#ffffff").show();

            setTimeout(function() { //setting the login message for verification success
                $("#validationAdminMsg").html("<span class='alert alert-success '> Credentials verified! <i class='fa fa-check'></i> || <span class='fa fa-spin fa-spinner'></span> Redirecting...</span>").show();
            }, 2000);

            setTimeout(function() { //go to dashboard once its successful
                window.location.href = "dashboard";
            }, 4000);
        </script>
    <?php
    } else { ?>
        <script>
            $("#validationAdminMsg").html("<span class='fa fa-spin fa-spinner'></span> Processing please wait...").css("color", "#818181").show();

            setTimeout(function() {
                $("#validationAdminMsg").html("<span class='alert alert-danger' style='font-size:13px'>invalid authorization! <i class='fa fa-times'></i></span>").show();

                //location.reload(); //reload this location
            }, 2000);
        </script>
<?php
    }
    exit();
}
//ADMINISTRATOR VALIDATION FOR AUTHORIZED ENVIRONMENT...... ENDS
