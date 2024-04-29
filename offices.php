<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
    header("location:./");
}

$page = "offices";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
    <meta charset="UTF-8">
    <title>Administrative Portal :: Election Offices</title>
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
    <style>
        /* Skeleton Loader Styles */
        .skeleton-loader {
            animation: shimmer 1.5s infinite linear;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            width: 100%;
            height: 20px;
            margin: 0 15px;
            border-radius: 4px;
            display: inline-block;
        }

        .modal-skeleton-loader {
            animation: shimmer 1.5s infinite linear;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            width: 100%;
            height: 300px;
            border-radius: 4px;
            display: flex;
        }

        /* Additional styling for better visual separation between cells */
        .skeleton-loader+.skeleton-loader {
            margin-top: 0;
        }


        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }
    </style>
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
                            <h4 class="mb-0">Offices</h4>
                        </div>

                        <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
                            <li class="breadcrumb-item active"><a href="offices">Offices</a></li>
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- END: Breadcrumbs-->

            <!-- START: Card Data-->
            <!-- <div class="row">
                <div class="card card-body col-12">
                    <div class="alert-warning h-200 text-center">
                        <i class="fa fa-cog fa-spin h1 mt-3"></i>
                        <h3 cal>Module Maintenance</h3>
                        <p>This page is currently under module maintenance and might take a while to finish. </p>
                        <p>Kindly contact <b>ICT Support</b> for more information on this update.</p>
                    </div>

                </div>
            </div> -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class=" card-header justify-content-between align-items-center">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewOffice" style="float:right"> + Add New Office</button>
                        </div>
                        <div class="card-body">
                            <input type="search" id="officesSearchEntry" class="form-control mt-4" placeholder="Search For Offices" />
                            <!-- ******display table data -->
                            <div class="card-body" id="displayOffices"> </div>
                            <!-- ******display table data -->
                        </div>
                    </div>
                </div>
                <!-- Modal Section to Add New Office -->
                <div class="modal fade bd-example-modal-lg" id="addNewOffice" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel10">Add New Office</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="addNewOfficeForm">
                                <div class="modal-body">
                                    <div class="row mt-3">

                                        <!--New Office Inputs-->
                                        <div class="col-12 mt-3">
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="card-body py-5">
                                                        <div class="row">

                                                            <div class="form-group col-sm-6">
                                                                <div class="input-group">
                                                                    <label class="col-12" for="officeStatus">Status
                                                                        <select class="form-control" type="text" name="officeStatus" id="officeStatus" required="">
                                                                            <option value="active">Active</option>
                                                                            <option value="inactive">Inactive</option>
                                                                        </select>
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-sm-6">
                                                                <div class="input-group">
                                                                    <label class="col-12" for="officeName">Name
                                                                        <input class="form-control" type="text" name="officeName" id="officeName" placeholder="Enter Office Name" required="" />
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-group col-sm-6">
                                                                <div class="input-group">
                                                                    <label class="col-12" for="officeAbbr">Abbreviation
                                                                        <input class="form-control" type="text" name="officeAbbr" id="officeAbbr" placeholder="Enter Office Abbreviation" required="" />
                                                                    </label>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--New Office Inputs-->

                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <center style="margin: 0px auto;">
                                        <span id="addNewOfficeMsg"></span>
                                    </center>
                                    <button type="submit" class="btn btn-primary" id="saveNewOffice">Save Office</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal Section to Add New Office -->
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
            loadOffices(1);
        });

        //LOAD OFFICES FUNCTION>>>>>>STARTS
        function loadOffices(page, query = '') {
            //var sortStatus = $("#sortStatus").val();
            //var officesPageLimit = $("#paymentPageLimit").val();

            $.ajax({
                url: "controllers/get-offices",
                method: "POST",
                async: true,
                data: {
                    showOffices: 1,
                    query: query,
                    page: page
                },
                beforeSend: function() {
                    $("#displayOffices").html("").show();
                    showOfficesSkeletonLoader();
                },
                success: function(data) {
                    setTimeout(function() {
                        $("#displayOffices").html(data).show();

                        $('.skeleton-loader').remove(); // Remove skeleton loader
                    }, 1000);
                },
                error: function(data) {
                    swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                    setTimeout(function() {
                        loadOffices(page);
                    }, 5000);
                }
            });
        };

        $(document).on('click', '.office-page-link', function() {
            var page = $(this).data('page_number');
            var query = $('#officesSearchEntry').val();
            loadOffices(page, query);
        });

        $('#officesSearchEntry').on('keyup change paste', function() {
            var query = $('#officesSearchEntry').val();
            loadOffices(1, query);

        });

        $("#officesPageLimit").on("change", function() { //page limit
            loadOffices(1);
        });

        // Skeleton Loader
        function showOfficesSkeletonLoader() {
            var skeletonHtml = `
					<div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
					</div>
				`;

            for (var i = 0; i < 2; i++) {
                $('#displayOffices').append(skeletonHtml);
            }
        }
        //LOAD OFFICES FUNCTION>>>>>>ENDS

        //ADD NEW OFFICE FUNCTION>>>>>>STARTS
        $("#addNewOfficeForm").submit(function(e) {
            e.preventDefault();


            var addOfficeForm = new FormData($("#addNewOfficeForm")[0]);
            swal({
                    title: "Are you sure to add Office?",
                    text: "You are about adding a new office to the portal.",
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
                        type: 'POST',
                        url: 'controllers/get-offices',
                        async: true,
                        processData: false,
                        contentType: false,
                        // mimeType: 'multipart/form-data',
                        // cache: false,
                        data: addOfficeForm,
                        beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                            $("#saveNewOffice").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
                        },
                        success: function(response) {
                            var status = response.status;
                            var message = response.message;
                            var responseStatus = response.responseStatus;
                            var header = response.header;

                            if (status === true) {
                                $("#addNewOfficeMsg").html(response).css("color", "green").show();
                                $("#addNewOfficeForm")[0].reset();
                                swal(header, message, responseStatus);
                            } else {
                                swal(header, message, responseStatus);
                            }
                        },
                        error: function() {
                            $("#addNewOfficeMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                        },
                        complete: function() { // Moved the timeout code to the complete callback
                            setTimeout(function() {
                                $("#addNewOfficeMsg").fadeOut(300);
                            }, 3000);
                            $("#saveNewOffice").html("Save Office").show(); // Reset the button text
                        }
                    });
                });
        });
        //ADD NEW OFFICE FUNCTION>>>>>>ENDS

        //LOAD OFFICE EDIT FUNCTION >>>>STARTS
        function loadOfficesEditModal(button) {
            var id = $(button).data("value");
            //console.log(vin);
            $.ajax({
                url: 'controllers/get-offices',
                method: 'POST',
                async: true,
                data: {
                    getOfficesEdit: 1,
                    id: id
                },
                beforeSend: function(gRcs) {
                    $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
                    $('#displayOfficesInputs').html("").show();
                    modalSkeletonLoader();
                },
                success: function(gRcs) {
                    $("#officesEditModal").modal('show');
                    $(button).html('<span class="fas fa-bars"></span>').show();
                    setTimeout(function() {
                        $('#displayOfficesInputs').html(gRcs).show();
                        $('.modal-skeleton-loader').remove(); // Remove skeleton loader
                    }, 1000);
                },
                error: function(gRcs) {
                    $(button).html('<span class="fas fa-bars"></span>').show();
                    swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                }
            });
        }
        // Skeleton Loader
        function modalSkeletonLoader() {
            var skeletonHtml = `
						<div  class='modal-skeleton-loader'> </div>
				`;

            for (var i = 0; i < 1; i++) {
                $('#displayOfficesInputs').append(skeletonHtml);
                $('#displayAllocatedOfficesInputs').append(skeletonHtml);
            }
        }
        //LOAD OFFICE EDIT FUNCTION >>>>ENDS
    </script>

</body>
<!-- END: Body-->

</html>