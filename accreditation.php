<?php
session_start();
// if (isset($_SESSION['vin'], $_SESSION['accreditationID'])) {
//     header("location:v-dashboard");
// }

// if (isset($_SESSION['adminID'])) {
//     header("location:dashboard");
// }

include("controllers/db_connect.php");

//get voters valid email address
$stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE email=?");
$stmt->bind_param("s", $_GET['id']);
$stmt->execute();
$stmt->bind_result($getCountNum);
$stmt->fetch();
$stmt->close();
(($getCountNum < 1) ? header("location:./index") : ""); //redirect to index if the email is not valid.
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Reophamton SU :: E-voting System</title>
    <!-- <title>ROEHAMPTON SU :: E-voting System</title>-->
    <link rel="shortcut icon" type="image/png" href="images/roe.png">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


    <!-- css files -->
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="../dist/vendors/sweetalert/sweetalert.css">
    <!-- /css files -->

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .footer {
            /* background-color: #13171a; */
            /* Change the background color as needed */
            padding: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper banner-layer">
        <div class="logo text-center">
            <h4 class="">
                <a href="index.php" style="color:white;text-decoration:none">
                    <img src="images/roe.png" style="width:90px; height:90px"><br>
                    <b style="font-size:40px;font-family:'Work Sans', sans-serif;font-weight:100">SU ELECTION</b>
                </a>
            </h4>
        </div>
        <div class="w3ls-container text-center" style="margin-top:10%">
            <div class="sub-form" id="vinValidationPanel">
                <form method="post" id="vinValidationForm">
                    <p>Enter your Voters Identification Number(VIN) for accreditation.</p>
                    <div class="form-group">
                        <input type="text" name="voterVin" id="voterVin" required="" maxlength="10" placeholder="Enter your valid VIN" />
                        <div>&nbsp;</div>
                        <button type="submit" class="btn1" id="vinValidationBtn">
                            <span class="fa fa-paper-plane" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div>&nbsp;</div>
                    <div id="validateVotersVinMsg"></div>

                </form>
            </div>
            <div class="sub-form" id="startVotePanel" style="display:none;">
                <p>Accreditation Was successful, click button below to start vote.</p>
                <a href="./index?id=<?php if (isset($_GET['id'])) {
                                        echo $_GET['id'];
                                    } ?>" class="btn btn-success btn-sm" id="vinValidationBtn">
                    Start Vote <span class="fa fa-paper-plane" aria-hidden="true"></span>
                </a>
            </div>
        </div>

    </div>
    <div class="footer">

        <p> Copyright <span style="cursor:pointer;" id="cPanel">&copy;</span> <?php echo date("Y"); ?> SU Election Tracker, Roehampton University All Rights Reserved. Presented by
            <a href="#" target="=_blank">ADEDEJI OLAWUNMI.</a>
        </p>
    </div>
</body>
<!-- JS scripts -->
<!--<script src="js/bootstrap.min.js"></script>-->
<script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/qrious.js"></script>

</html>
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
        setInputFilter(document.getElementById("voterVin"), function(value) {
            return /^\d*$/.test(value);
        });
    });

    //Validating voters VIN for accreditation >>>>  starts
    $('#vinValidationForm').submit(function(e) {
        e.preventDefault();
        var voterVin = $("#voterVin").val();
        var email = "<?php echo $_GET['id']; ?>";

        console.log("Welcome")
        $.ajax({
            type: 'POST',
            url: 'controllers/login',
            async: true,
            data: {
                validateVoterVin: 1,
                voterVin: voterVin,
                email: email
            },
            beforeSend: function() {
                $("#validateVotersVinMsg").html("<i style='font-size:13px'> <span class='fa fa-spin fa-spinner'></span> Please wait... </i>").css("color", "#ffffff").show(); //show validation message onload
            },
            success: function(vVinAccreditation) {
                var emailStatus = vVinAccreditation.emailStatus;
                var emailMessage = vVinAccreditation.emailMessage;
                var header = vVinAccreditation.header;
                var message = vVinAccreditation.message;
                var responseStatus = vVinAccreditation.responseStatus;

                console.log(emailStatus, emailMessage);

                swal(header, message, responseStatus);
                $("#validateVotersVinMsg").fadeOut(200);

                if (responseStatus === "success") {
                    $("#vinValidationPanel").fadeOut(200);
                    $("#startVotePanel").fadeIn(200);
                }
            },
            error: function(vVinAccreditation) {
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                $("#validateVotersVinMsg").fadeOut(200);

            }
        });
    });
    //Validating of voters VIN for accreditation >>>> ends  
</script>