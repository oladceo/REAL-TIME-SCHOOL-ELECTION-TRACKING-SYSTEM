<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
    header("location:./");
}

$page = "results";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
    <meta charset="UTF-8">
    <title>Administrative Portal :: Election Results</title>
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
    <link rel="stylesheet" href="dist/vendors/morris/morris.css">
    <link rel="stylesheet" href="dist/vendors/weather-icons/css/pe-icon-set-weather.min.css">
    <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">
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
                        <div class="w-sm-100 mr-auto">
                            <h4 class="mb-0">Election Results</h4>
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
                <!-- <div class="card card-body col-12">
                    <div class="alert-warning h-200 text-center">
                        <i class="fa fa-cog fa-spin h1 mt-3"></i>
                        <h3 cal>Module Maintenance</h3>
                        <p>This page is currently under module maintenance and might take a while to finish. </p>
                        <p>Kindly contact <b>ICT Support</b> for more information on this update.</p>
                    </div>
                </div> -->

                <div class="col-12 col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title">Election Result</h6>
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <div id="charts-container" class="row"></div>
                            </div>
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
    <!-- END: Template JS-->

    <!-- START: APP JS-->
    <script src="dist/js/app.js"></script>
    <!-- END: APP JS-->

    <!-- START: Page Vendor JS-->
    <script src="dist/vendors/raphael/raphael.min.js"></script>
    <script src="dist/vendors/morris/morris.min.js"></script>
    <!-- END: Page Vendor JS-->

</body>
<script>
    $(document).ready(function() {
        var primarycolor = getComputedStyle(document.body).getPropertyValue('--primarycolor');

        // Use AJAX to fetch data for each office
        $.ajax({
            url: 'controllers/get-votes',
            type: 'POST',
            dataType: 'json',
            data: {
                get_chart_result: true
            },
            success: function(data) {
                // Iterate over each office's data
                $.each(data, function(index, officeData) {
                    // Generate a unique ID for the chart container
                    var chartId = 'chart-' + index;

                    // Create a div for the chart box with inline-block display
                    var chartBox = $('<div class="chart-box col-md-6"></div>');

                    // Create a div for the chart title within the chart box
                    var chartTitle = $('<div class="chart-title h2"><b>' + officeData.office + '</b></div>');

                    // Prepend the chart title before appending the chart container
                    chartBox.prepend(chartTitle);

                    // Create a chart container within the chart box
                    var chartContainer = $('<div id="' + chartId + '" class="chart"></div>').appendTo(chartBox);

                    // Append the chart box to the charts-container
                    $('#charts-container').append(chartBox);

                    // Rendering Morris chart for the current office
                    if ($('#' + chartId).length > 0) {
                        Morris.Bar({
                            element: chartId,
                            data: officeData.candidates,
                            xkey: 'name',
                            ykeys: ['votes'],
                            labels: ['Votes'],
                            barColors: function(row, series, type) {
                                if (row.y > 0) {
                                    return '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6); // Random color
                                }
                                return '#ccc'; // Gray color for zero votes
                            },
                            barRatio: 0.4,
                            xLabelAngle: 35,
                            hideHover: 'auto',
                            ymin: 0,
                            // Custom label function to add a class to candidate names
                            xLabelMargin: 10,
                            barLabelFontFamily: 'Arial',
                            barLabelFontSize: 14,
                            barLabelAlign: 'center',
                            barLabelFormat: function(y, data) {
                                return '<div class="candidate-name">' + data.x + '</div>';
                            }
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(error, xhr);
            }
        });



    });
</script>
<!-- END: Body-->

</html>