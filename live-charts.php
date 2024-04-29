<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
    header("location:./");
}

$page = "live-charts";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
    <meta charset="UTF-8">
    <title>Administrative Portal :: Election-live-charts</title>
    <link rel="icon" href="images/roe.png" type="icon/png">
    <meta name="viewport" content="width=device-width,initial-scale=1">


    <!-- START: Template CSS-->
    <link rel="stylesheet" href="dist/vendors/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="dist/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="dist/vendors/flags-icon/css/flag-icon.min.css">
    <!-- END Template CSS-->

    <!-- START: Page CSS-->
    <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
    <link href="dist/vendors/lineprogressbar/jquery.lineProgressbar.min.css" rel="stylesheet">
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
        <img class="loader" src="images/roe.png" alt="">
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
                        <div class="w-sm-100 mr-auto">
                            <h4 class="mb-0">Election live Reports</h4>
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
            <div class="card card-body col-12">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <!-- Load Data Table -->
                                    <div class="card-body" id="displayLiveStatisticsData"> </div>
                                    <!-- Load Data Table -->

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <div class="card">

                            <div class="card overflow-hidden">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 h5 font-w-600">Leading Candidates By Office</h6>
                                </div>
                                <!-- Load Data Table -->
                                <div class="card-body" id="displayLeadingCandidates" style="height:30rem; overflow:auto"> </div>
                                <!-- Load Data Table -->

                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                        <!-- Offices Selection Tabs -->
                        <div class="profile-menu mt-4 theme-background border z-index-1 p-2">
                            <div class="d-sm-flex">
                                <div class="align-self-center">
                                    <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                                        <?php
                                        $id = " ";
                                        $status = "active";

                                        $stmt = $conn->prepare("SELECT eo.*, COUNT(ec.id) as candidateCount
                                        FROM election_offices eo
                                        LEFT JOIN election_candidates ec ON eo.id = ec.office
                                        WHERE eo.id != ? AND eo.status = ?
                                        GROUP BY eo.id
                                        HAVING candidateCount > 0 ORDER BY eo.sn");
                                        $stmt->bind_param("ss", $id, $status);
                                        $stmt->execute() or die(mysqli_error($conn));
                                        $result = $stmt->get_result();
                                        $officeNum = 0;

                                        while ($getActiveOffice = $result->fetch_array()) {
                                            $officeNum++;
                                        ?>
                                            <li class="nav-item ml-0" data-value="<?php echo $getActiveOffice['id']; ?>" onClick="loadOfficeLiveResult(this);" id="navItemLink">
                                                <a class="nav-link  py-2 px-3 px-lg-4 <?php echo (($officeNum == 1) ? 'active' : '') ?>" data-toggle="tab" id="office<?php echo $officeNum; ?>Tab" href="#office<?php echo $officeNum; ?>">
                                                    <i><?php echo $officeNum; ?>.</i> <?php echo ucfirst($getActiveOffice['name']); ?>
                                                </a>
                                            </li>
                                        <?php } ?>


                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Offices Selection Tabs -->

                        <div class="tab-content" style="min-height:30rem">
                            <?php
                            $officeNum = 0;
                            $stmt->execute() or die(mysqli_error($conn));
                            $result2 = $stmt->get_result();
                            while ($getActiveOfficeTab = $result2->fetch_array()) {
                                $officeNum++;
                            ?>
                                <!-- Office Active Tab Starts-->
                                <div id="office<?php echo $officeNum; ?>" class="tab-pane fade <?php echo (($officeNum == 1) ? ' in active' : '') ?>">
                                    <div class="card">
                                        <div class=" card-header justify-content-between align-items-center">
                                            <h4 class="card-title">Office of the <?php echo ucfirst($getActiveOfficeTab['name']); ?></h4>
                                        </div>
                                        <!-- ******display Result table data -->
                                        <div class="card-body" id="displayOfficeLiveResult<?php echo $getActiveOfficeTab['id']; ?>"> </div>
                                        <!-- ******display Result table data -->
                                    </div>
                                </div>
                                <!-- Office Active Tab Ends-->
                            <?php }
                            $stmt->close();
                            ?>
                        </div>
                    </div>
                </div>
                <!-- <div class="alert-warning h-200 text-center">
                        <i class="fa fa-cog fa-spin h1 mt-3"></i>
                        <h3 cal>Module Maintenance</h3>
                        <p>This page is currently under module maintenance and might take a while to finish. </p>
                        <p>Kindly contact <b>ICT Support</b> for more information on this update.</p>
                    </div> -->

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

    <!-- END: Template JS-->

    <!-- START: APP JS-->
    <script src="dist/js/app.js"></script>
    <!-- END: APP JS-->


    <script src="dist/vendors/lineprogressbar/jquery.lineProgressbar.js"></script>
    <script src="dist/vendors/lineprogressbar/jquery.barfiller.js"></script>


</body>
<!-- END: Body-->

<script>
    $(document).ready(function() {
        getLiveStatisticsData(); //Load Live Statistics Data Table at default

        var firstTab = $("#navItemLink");
        loadOfficeLiveResult(firstTab); // Call function with the initial officeID
    });

    //Function to get data value from tab link >>> Starts
    function getOfficeID(tab) {
        return $(tab).data("value");
    }
    //Function to get data value from tab link >>> ENds

    //Get Offices Live Results For all Candidates >>> Starts
    function loadOfficeLiveResult(tab) {
        var officeID = getOfficeID(tab);
        // console.log(officeID);
        $.ajax({
            url: "controllers/get-live-charts",
            type: "POST",
            async: false,
            data: {
                getOfficeResults: 1,
                officeID: officeID
            },
            success: function(olr) {
                $("#displayOfficeLiveResult" + officeID).html(olr).show();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                setTimeout(function() {
                    var firstTab = $("#navItemLink");
                    loadOfficeLiveResult(firstTab); // Call function with the initial officeID
                }, 5000);
            }
        });
    }
    //Get Offices Live Results For all Candidates >>> ENds

    //Get Leading Candidates Table >>> Starts
    function getLeadingCandidates() {
        $.ajax({
            url: "controllers/get-live-charts",
            type: "POST",
            async: false,
            data: {
                getLeadingCandidate: 1
            },
            success: function(lcads) {
                $("#displayLeadingCandidates").html(lcads).show();
            },
            complete: function() {
                var firstTab = $("#navItemLink");
                loadOfficeLiveResult(firstTab);

            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                setTimeout(function() {
                    getLeadingCandidates();
                }, 5000);
            }
        });
    }
    //Get Leading Candidates Table >>> Ends

    //Get Live Statistics Data >>> Starts
    function getLiveStatisticsData() {
        $.ajax({
            url: "controllers/get-live-charts",
            type: "POST",
            async: false,
            data: {
                getLiveStatisticData: 1
            },
            success: function(dlsd) {
                $("#displayLiveStatisticsData").html(dlsd).show();
            },
            complete: function() {
                getLeadingCandidates();

            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                setTimeout(function() {
                    getLiveStatisticsData();
                }, 5000);
            }
        });
    }

    setInterval(getLiveStatisticsData, 5000); // Call every 5000 milliseconds (10 seconds)
    //Get Live Statistics Data >>> Ends
</script>

</html>