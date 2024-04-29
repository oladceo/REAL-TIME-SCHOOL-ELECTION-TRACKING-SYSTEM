<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
    header("location:./");
}

$page = "settings";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
    <meta charset="UTF-8">
    <title>Administrative Portal :: Election Settings</title>
    <link rel="icon" href="images/roe.png" type="icon/png">
    <meta name="viewport" content="width=device-width,initial-scale=1">


    <!-- START: Template CSS-->
    <link rel="stylesheet" href="dist/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="dist/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="dist/vendors/flags-icon/css/flag-icon.min.css">
    <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">
    <!-- END Template CSS-->

    <!-- START: Page CSS-->
    <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
    <!-- END: Page CSS-->

    <!-- START: Page CSS-->
    <link rel="stylesheet" href="dist/vendors/morris/morris.css">
    <link rel="stylesheet" href="dist/vendors/weather-icons/css/pe-icon-set-weather.min.css">
    <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
    <link rel="stylesheet" href="dist/vendors/starrr/starrr.css">
    <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="dist/vendors/jquery-jvectormap/jquery-jvectormap-2.0.3.css">
    <!-- END: Page CSS-->

    <!-- START: Custom CSS-->
    <link rel="stylesheet" href="dist/css/main.css">
    <!-- END: Custom CSS-->
</head>
<!-- END Head-->

<!-- START: Body-->

<body id="main-container" class="default compact-menu">
    <!-- START: Pre Loader-->
    <div class="se-pre-con">
        <img class="loader" src="images/roe.png" alt="Imsu Portal">
    </div>
    <!-- END: Pre Loader-->

    <!-- START: Header-->
    <?php include("inc/header.php"); ?>
    <!-- END: Header-->

    <!-- START: Main Menu-->
    <?php include("inc/sidebar.php"); ?>
    <!-- END: Main Menu-->

    <!-- START: Main Content-->
    <main>
        <div class="container-fluid site-width">
            <!-- START: Breadcrumbs-->
            <div class="row ">
                <div class="col-12  align-self-center">
                    <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
                        <div class="w-sm-100 mr-auto ">
                            <!-- <h4 class="mb-0 ">Election Settings</h4> -->
                        </div>

                        <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
                            <li class="breadcrumb-item active"><a href="<?php echo $page; ?>"><?php echo ucfirst($page); ?></a></li>
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- END: Breadcrumbs-->

            <!-- START: Card Data-->
            <div class="row">
                <div class="card col-6" style="min-height:35rem;margin: 0 auto;">
                    <div class="card-header">
                        <h4 class="card-title">Portal Preferences</h4>
                    </div>
                    <div class=" card-body">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM election_settings");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $getSettings = $result->fetch_array();

                        ?>
                        <form id="settingsForm">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="portalAccess" class="col-12 mb-1">Portal Access
                                        <select class="form-control" name="portalAccess" id="portalAccess" required="">
                                            <option value="enabled" <?php echo (($getSettings['portalAccess'] == "enabled") ? "selected" : ""); ?>>Enable</option>
                                            <option value="disabled" <?php echo (($getSettings['portalAccess'] == "disabled") ? "selected" : ""); ?>>Disable</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="qrUrlAddress" class="col-12 mb-1">Domain/Website Address:
                                        <input class="form-control" type="url" name="qrUrlAddress" id="qrUrlAddress" placeholder="http://www.hostedwebsite.domain" required="" value="<?php echo $getSettings['qr_ip_address']; ?>" />
                                    </label>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="systemEnvironment" class="col-12 mb-1">System Environment
                                        <select class="form-control" name="systemEnvironment" id="systemEnvironment" required="">
                                            <option value="development" <?php echo (($getSettings['environment'] == "development") ? "selected" : ""); ?>>Development</option>
                                            <option value="production" <?php echo (($getSettings['environment'] == "production") ? "selected" : ""); ?>>Production</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="electionDeadline" class="control col-12 mb-1">Election Deadline
                                        <input type="datetime-local" class="form-control" name="electionDeadline" id="electionDeadline" required="" value="<?= date('Y-m-d\TH:i', strtotime($getSettings['deadline'])); ?>" />
                                    </label>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="electionStatus" class="col-12 mb-1">Election Status
                                        <select class="form-control" name="electionStatus" id="electionStatus" required="" onChange="electionStatusSwitch();">
                                            <option value="1" <?php echo (($getSettings['system_status'] == "1") ? "selected" : ""); ?>>Activate</option>
                                            <option value="0" <?php echo (($getSettings['system_status'] == "0") ? "selected" : ""); ?>>Deactivate</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <center>
                                <button class="btn btn-primary btn-sm mt-3" type="submit" id="saveSettingBtn">Save System Setting</button>
                            </center>
                        </form>
                        <div class="alert alert-warning mt-4" style="display:none" id="electionActivationStatusWarning">
                            <center>
                                <h3 class="fas fa-exclamation-circle "></h3>
                            </center>
                            <h6 class="text-center font-w-700 ">What you need to know when <b>Election Status</b> is Activated.</h6>
                            <ol type="1">
                                <li>An email containing the updated <b>Election Deadline</b> will be sent to all registered voters and candidates. This will allow them to proceed with the accreditation process and then cast their votes.</li>
                                <li>All actions, such as <b>Adding/Modifying</b> Candidates, Voters, and Offices data, will be temporarily suspended until the election status is <b>Deactivated</b>.</li>
                                <li>You won't be able to modify the Election deadline once the election is set to start. </li>

                                <p class=" mt-3"><i class="text-center"><b>NB:</b> This option should only be utilized when the Election is scheduled to begin and the election deadline has been confirmed.</i>
                                </p>
                            </ol>
                        </div>
                        <div class="alert alert-info mt-4" style="display:none" id="sentEmailStatus">
                            <center>
                                <h3 class="fas fa-envelope "></h3>
                            </center>
                            <p>An email containing the updated <b>Election Deadline</b> has just been sent to all registered voters and candidates. This will allow them to proceed with the accreditation process and then cast their votes.<br />
                                <!-- <center>
                                    <b>NB:</b> Please kindly ignore this message if system environment is set to <em>Development</em> state. Email will only be sent in <em>Production</em> state.
                                </center> -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Card DATA-->
        </div>
    </main>
    <!-- END: Content-->



    <!-- START: Footer-->
    <footer>
        <?php include("inc/footer.php"); ?>
    </footer>
    <!-- END: Footer-->


    <!-- START: Back to top-->
    <a href="#" class="scrollup text-center">
        <i class="icon-arrow-up"></i>
    </a>
    <!-- END: Back to top-->

    <!-- START: Template JS-->
    <script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
    <script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
    <script src="dist/vendors/moment/moment.js"></script>
    <script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
    <!-- END: Template JS-->

    <!-- START: APP JS-->
    <script src="dist/js/app.js"></script>
    <!-- END: APP JS-->
    <script>
        $(document).ready(function() {
            electionStatusSwitch();
        });

        // make sure selection date does not select from behind
        const now = new Date();
        const formattedNow = now.toISOString().slice(0, 16);
        document.getElementById("electionDeadline").min = formattedNow;
        // make sure selection date does not select from behind

        //ELECTION STATUS SWITCH ALERT >>>Starts 
        function electionStatusSwitch() {
            var electionStatus = $("#electionStatus").val();
            if (electionStatus === "1") {
                $("#electionActivationStatusWarning").fadeIn(500);
                $("#electionDeadline").prop("readonly", true);
                $("#sentEmailStatus").fadeOut(500);

            } else {
                $("#electionActivationStatusWarning").fadeOut(500);
                $("#electionDeadline").prop("readonly", false);
                $("#sentEmailStatus").fadeOut(500);
            }
        }
        //ELECTION STATUS SWITCH ALERT >>>Ends 

        //FUNCTION TO UPDATE SYSTEM SETTINGS >>>> Starts
        $("#settingsForm").submit(function(e) {
            e.preventDefault();
            var settingsForm = new FormData($("#settingsForm")[0]);
            swal({
                    title: "Are you sure to proceed with changes?",
                    text: "Clicking **continue** will modify the system settings across the system. ",
                    icon: 'question',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-success',
                    cancelButtonClass: 'btn-danger',
                    confirmButtonText: 'Continue!',
                    cancelButtonText: 'Cancel!',
                    closeOnConfirm: false,
                    //closeOnCancel: false
                },
                function() {
                    $.ajax({
                        url: "controllers/get-settings",
                        type: "POST",
                        async: true,
                        processData: false,
                        contentType: false,
                        data: settingsForm,
                        beforeSend: function(sSettings) {
                            $("#saveSettingBtn").prop("disabled", true);
                            $("#saveSettingBtn").html("<i class='fa fa-spin fa-spinner'></i>Please wait...").show();
                        },
                        success: function(sSettings) {
                            var status = sSettings.status;
                            var header = sSettings.header;
                            var message = sSettings.message;
                            var responseStatus = sSettings.responseStatus;
                            var btnText = sSettings.btnText;
                            var emailStatus = sSettings.emailStatus;
                            var emailMessage = sSettings.emailMessage;

                            console.log(emailMessage);

                            if (emailStatus === true) {
                                $("#sentEmailStatus").fadeIn(500);
                                $("#electionActivationStatusWarning").fadeOut(500);
                            }

                            swal(header, message, responseStatus);
                            $("#saveSettingBtn").prop("disabled", status);
                            $("#saveSettingBtn").html(btnText).show();


                        },
                        error: function(sSettings) {
                            $("#saveSettingBtn").prop("disabled", false);
                            $("#saveSettingBtn").html("Save System Settings").show();
                            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                        }

                    });
                });
        });
        //FUNCTION TO UPDATE SYSTEM SETTINGS >>>> Ends
    </script>
</body>
<!-- END: Body-->

</html>