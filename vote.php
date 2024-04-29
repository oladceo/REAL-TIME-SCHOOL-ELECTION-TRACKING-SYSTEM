<?php
session_start();

if (!isset($_SESSION['vin']) || !isset($_SESSION['accreditationID']) || !isset($_SESSION['voteSessionID'])) {
    header("location:./");
}

$page = "v-dashboard";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");


?>
<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
    <meta charset="UTF-8">
    <title>Administrative Portal :: Voters Vote Process</title>
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
        <img class="loader" src="images/roe.png" alt="REOHAMPTON">
    </div>
    <!-- END: Pre Loader-->


    <!-- START: Header-->
    <?php include("inc/header.php"); ?>
    <!-- END: Header-->

    <!-- START: Main Content-->
    <main>
        <div class="container-fluid site-width">
            <!-- START: Breadcrumbs-->
            <div class="row">
                <div class="col-12  align-self-center" style="position:relative;">
                    <div class="sub-header mt-3 py-3 px-md-0 align-self-center d-sm-flex w-100 rounded">
                        <div class="w-sm-100 mr-auto">
                            <button type="button" class="btn btn-primary" onclick="location.href='v-dashboard'">
                                <i class="fa fa-home"></i> Return To Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Breadcrumbs-->

            <!-- START: Card Data-->
            <div class="row" style="">
                <div class="col-12 col-lg-12  mt-3">
                    <h4>Election Panel</h4>
                    <div class="card overflow-hidden">
                        <div class="card-content">
                            <!--Display Available Candidates -->
                            <div class="card-body p-4" id="displayCandidates" style="min-height:40em"> </div>
                            <!--Display Available Candidates -->
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
            loadCandidates(1)

        });

        // Function to request full-screen mode
        // function requestFullScreen() {
        //     var element = document.documentElement; // Get the HTML element
        //     if (element.requestFullscreen) {
        //         element.requestFullscreen().catch(function(error) {
        //             console.log("Fullscreen request failed:", error);
        //         });
        //     }
        // }

        // $("#main-container").on("click scroll", function() {
        //     requestFullScreen();

        // })

        //LOAD CANDIDATES FUNCTION>>>>>>STARTS
        function loadCandidates(page) {

            $.ajax({
                url: "controllers/get-votes",
                method: "POST",
                async: false,
                data: {
                    showCandidates: 1,
                    page: page
                },
                success: function(lse) {
                    $("#displayCandidates").html(lse).show();
                }
            });
        };

        $(document).on('click', '.page-link', function() {
            var page = $(this).data('page_number');
            loadCandidates(page);
        });

        //LOAD CANDIDATES FUNCTION>>>>>>ENDS
    </script>
</body>
<!-- END: Body-->

</html>