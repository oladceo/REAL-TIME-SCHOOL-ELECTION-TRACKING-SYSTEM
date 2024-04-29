<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "voters";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Administrative Portal :: Election Voters</title>
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
              <h4 class="mb-0">Voters</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="voters">Voters</a></li>
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
          <!-- Voters Tabs -->
          <div class="profile-menu mt-4 theme-background border z-index-1 p-2">
            <div class="d-sm-flex">
              <div class="align-self-center">
                <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                  <li class="nav-item ml-0">
                    <a class="nav-link  py-2 px-3 px-lg-4  active" data-toggle="tab" id="registeredVotersTab" href="#registeredVoters"><i class="fas fa-users"></i> Registered Voters</a>
                  </li>
                  <li class="nav-item ml-0">
                    <a class="nav-link  py-2 px-3 px-lg-4" data-toggle="tab" id="accreditedVotersTab" href="#accreditedVoters"><i class="fas fa-user-check"></i> Accredited Voters</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- Voters Tabs -->

          <div class="tab-content" style="min-height:35rem">
            <!-- Registered Voters Starts-->
            <div id="registeredVoters" class="tab-pane fade in active">
              <div class="card">
                <div class=" card-header justify-content-between align-items-center">
                  <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewVoter" style="float:right"> + Add New Voter</button>
                </div>
                <div class="card-body">
                  <input type="search" id="votersSearchEntry" class="form-control mt-4" placeholder="Search For Voters" />
                  <!-- ******display table data -->
                  <div class="card-body" id="displayVoters"> </div>
                  <!-- ******display table data -->
                </div>
              </div>
            </div>
            <!-- Registered Voters Ends-->

            <!-- Accredited Voters Starts-->
            <div id="accreditedVoters" class="tab-pane fade">
              <div class="card">
                <div class="card-body">
                  <input type="search" id="accreditedVotersEntry" class="form-control mt-4" placeholder="Search For Accredited Voters" />
                  <!-- ******display table data -->
                  <div class="card-body" id="displayAccreditedVoters"> </div>
                  <!-- ******display table data -->
                </div>
              </div>
            </div>
            <!-- Accredited Voters Ends-->
          </div>


        </div>
        <!-- Modal Section to Add New Voter -->
        <div class="modal fade bd-example-modal-xl" id="addNewVoter" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel10">Add New Voter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="addNewVoterForm">
                <div class="modal-body">
                  <div class="row mt-3">

                    <!--New Voter Inputs-->
                    <div class="col-8 mt-3">
                      <div class="card">
                        <div class="card-content">
                          <div class="card-body py-5">
                            <div class="row">

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="voterSname">Surname
                                    <input class="form-control" type="text" name="voterSname" id="voterSname" placeholder="Enter Voter Surname" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="voterFname">First Name
                                    <input class="form-control" type="text" name="voterFname" id="voterFname" placeholder="Enter Voter First Name" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="voterOname">Other Names
                                    <input class="form-control" type="text" name="voterOname" id="voterOname" placeholder="Enter Voter Other Names" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="voterGender">Gender
                                    <select class="form-control" type="text" name="voterGender" id="voterGender" required="">
                                      <option value="male">Male</option>
                                      <option value="female">Female</option>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="voterEmail">Email Address
                                    <input class="form-control" type="email" name="voterEmail" id="voterEmail" placeholder="Enter Voter Email Address" required="" />
                                    <span id="emailVerMsg"></span>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="voterPhone">Phone
                                    <input class="form-control" type="text" name="voterPhone" id="voterPhone" placeholder="Enter Voter Pone Number" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="voterAddress">Voter Resident Address
                                    <textarea class="form-control" placeholder="Enter Voter Resident Address" name="voterAddress" id="voterAddress" required=""></textarea>
                                  </label>
                                </div>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--New Voter Inputs-->


                    <!--Voter Image-->
                    <div class="col-4 mt-3">
                      <div class="card">
                        <div class="card-content">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Voter Image Preview</h4>
                          </div>
                          <div class="card-body py-5">
                            <center class="col-12" style="margin: 0px auto;">
                              <img src="../images/no-preview.jpeg" style="width:280px;height:280px" id="newVoterImagePreview" />
                              <div>&nbsp;</div>
                              <label for="newVoterImage" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Voter Image<input id="newVoterImage" name="newVoterImage" type="file" onchange="previewImage(event);" />
                              </label>
                            </center>

                          </div>
                        </div>
                      </div>
                    </div>
                    <!--Voter Image-->
                  </div>

                </div>
                <div class="modal-footer">
                  <center style="margin: 0px auto;">
                    <span id="addNewVoterMsg"></span>
                  </center>
                  <button type="submit" class="btn btn-primary" id="saveNewVoter">Save Voter</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Modal Section to Add New Voter -->
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
      loadVoters(1);

      $("#registeredVotersTab").click(function() { //Show tab table when tab is clicked upon
        loadVoters(1); //display available registered Voters
      });

      $("#accreditedVotersTab").click(function() { //Show tab table when tab is clicked upon
        loadAccreditedVoters(1); //display available Accredited Voters
      });
    });

    // PREVIEW VOTER IMAGE ONCE ITS SELECTED STARTS
    /*** Create an arrow function that will be called when an image is selected.*/
    const previewImage = (event) => {
      /**
       * Get the selected files.
       */
      const imageFiles = event.target.files;
      /**
       * Count the number of files selected.
       */
      const imageFilesLength = imageFiles.length;
      /**
       * If at least one image is selected, then proceed to display the preview.
       */
      if (imageFilesLength > 0) {
        /**
         * Get the image path.
         */
        const imageSrc = URL.createObjectURL(imageFiles[0]);
        /**
         * Select the image preview element.
         */
        const imagePreviewElement = document.querySelector("#newVoterImagePreview");
        const imagePreviewElement2 = document.querySelector("#modifyVoterImagePreview");
        /**
         * Assign the path to the image preview element.
         */
        imagePreviewElement.src = imageSrc;
        imagePreviewElement2.src = imageSrc;
        /**
         * Show the element by changing the display value to "block".
         */
        //			imagePreviewElement.style.display = "block";
        imagePreviewElement.style.display = "";
        imagePreviewElement2.style.display = "";
      }
    };
    // PREVIEW VOTER IMAGE ONCE ITS SELECTED ENDS

    //LOAD VOTERS FUNCTION>>>>>>STARTS
    function loadVoters(page, query = '') {
      //var sortStatus = $("#sortStatus").val();
      //var votersPageLimit = $("#paymentPageLimit").val();

      $.ajax({
        url: "controllers/get-voters",
        method: "POST",
        async: true,
        data: {
          showVoters: 1,
          query: query,
          page: page
        },
        beforeSend: function() {
          $("#displayVoters").html("").show();
          showVotersSkeletonLoader();
        },
        success: function(data) {
          setTimeout(function() {
            $("#displayVoters").html(data).show();

            $('.skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
        },
        error: function(data) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            loadVoters(page);
          }, 5000);
        }
      });
    };

    $(document).on('click', '.voter-page-link', function() {
      var page = $(this).data('page_number');
      var query = $('#votersSearchEntry').val();
      loadVoters(page, query);
    });

    $('#votersSearchEntry').on('keyup change paste', function() {
      var query = $('#votersSearchEntry').val();
      loadVoters(1, query);

    });

    $("#votersPageLimit").on("change", function() { //page limit
      loadVoters(1);
    });

    // Skeleton Loader
    function showVotersSkeletonLoader() {
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
        $('#displayVoters').append(skeletonHtml);
      }
    }
    //LOAD VOTERS FUNCTION>>>>>>ENDS

    //ADD NEW VOTER FUNCTION>>>>>>STARTS
    $("#addNewVoterForm").submit(function(e) {
      e.preventDefault();


      var addVoterForm = new FormData($("#addNewVoterForm")[0]);
      swal({
          title: "Are you sure to add Voter?",
          text: "You are about adding a new voter to the portal.",
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
            url: 'controllers/get-voters',
            async: true,
            processData: false,
            contentType: false,
            // mimeType: 'multipart/form-data',
            // cache: false,
            data: addVoterForm,
            beforeSend: function() { // Corrected from beforeSubmit to beforeSend
              $("#saveNewVoter").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
            },
            success: function(response) {
              var status = response.status;
              var message = response.message;
              var responseStatus = response.responseStatus;
              var header = response.header;

              if (status === true) {
                $("#addNewVoterMsg").html(response).css("color", "green").show();
                $("#addNewVoterForm")[0].reset();
                document.querySelector("#newVoterImagePreview").src = "../images/no-preview.jpeg"; //reset the images preview.
                swal(header, message, responseStatus);
              } else {
                swal(header, message, responseStatus);
              }
            },
            error: function() {
              $("#addNewVoterMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
              swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
            },
            complete: function() { // Moved the timeout code to the complete callback
              setTimeout(function() {
                $("#addNewVoterMsg").fadeOut(300);
              }, 3000);
              $("#saveNewVoter").html("Save Voter").show(); // Reset the button text
            }
          });
        });
    });
    //ADD NEW VOTER FUNCTION>>>>>>ENDS

    //LOAD VOTER EDIT FUNCTION >>>>STARTS
    function loadVotersEditModal(button) {
      var vin = $(button).data("value");
      //console.log(vin);
      $.ajax({
        url: 'controllers/get-voters',
        method: 'POST',
        async: true,
        data: {
          getVotersEdit: 1,
          vin: vin
        },
        beforeSend: function(gRcs) {
          $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
          $('#displayVotersInputs').html("").show();
          modalSkeletonLoader();
        },
        success: function(gRcs) {
          $("#votersEditModal").modal('show');
          $(button).html('<span class="fas fa-bars"></span>').show();
          setTimeout(function() {
            $('#displayVotersInputs').html(gRcs).show();
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
        $('#displayVotersInputs').append(skeletonHtml);
        $('#displayAllocatedVotersInputs').append(skeletonHtml);
      }
    }
    //LOAD VOTER EDIT FUNCTION >>>>ENDS

    //LOAD ACCREDITED VOTERS FUNCTION>>>>>>STARTS
    function loadAccreditedVoters(page, query = '') {
      //var sortStatus = $("#sortStatus").val();
      //var votersPageLimit = $("#paymentPageLimit").val();

      $.ajax({
        url: "controllers/get-voters",
        method: "POST",
        async: true,
        data: {
          showAccreditedVoters: 1,
          query: query,
          page: page
        },
        beforeSend: function() {
          $("#displayAccreditedVoters").html("").show();
          showAccreditedVotersSkeletonLoader();
        },
        success: function(data) {
          setTimeout(function() {
            $("#displayAccreditedVoters").html(data).show();

            $('.skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
        },
        error: function(data) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            loadAccreditedVoters(page);
          }, 5000);
        }
      });
    };

    $(document).on('click', '.accreditedVoter-page-link', function() {
      var page = $(this).data('page_number');
      var query = $('#accreditedVotersSearchEntry').val();
      loadAccreditedVoters(page, query);
    });

    $("#accreditedVotersSearchEntry").on("keyup change paste", function() {
      var query = $("#accreditedVotersSearchEntry").val();
      loadAccreditedVoters(1, query);
    });

    $("#votersPageLimit").on("change", function() { //page limit
      loadAccreditedVoters(1);
    });

    // Skeleton Loader
    function showAccreditedVotersSkeletonLoader() {
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
        $('#displayAccreditedVoters').append(skeletonHtml);
      }
    }
    //LOAD ACCREDITED VOTERS FUNCTION>>>>>>ENDS    

    //FUNCTION TO VERIFY VOTER EMAIL FOR DUPLICATE  >>>STARTS
    $("#voterEmail").on("input paste", function() {
      verifyVoterEmail();
    });

    function verifyVoterEmail() {
      var email = $("#voterEmail").val();
      $.ajax({
        type: 'POST',
        url: 'controllers/get-voters',
        async: true,
        data: {
          voterEmailVer: 1,
          email: email
        },
        success: function(response) {
          var status = response.status;

          if (status === true) {
            $("#emailVerMsg").html("Email has already exist").css("color", "red").show();
            $("#saveNewVoter").hide();
          } else {
            $("#saveNewVoter").show();
            $("#emailVerMsg").hide();
          }
        },
        error: function() {
          $("#emailVerMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
          //NOTIFICATION ->>STARS
          (function($) {

            'use strict';
            new PNotify({
              title: 'Connection Failed',
              text: 'Error in connectivity, please check your internet connection and try again <i class="fa fa - exclamation - triangle "></i>',
              type: 'error'
            });
          }).apply(this, [jQuery]);
          //NOTIFICATION ->>ENDS
        }
      });
    }
    //FUNCTION TO VERIFY VOTER EMAIL FOR DUPLICATE  >>>ENDS
  </script>

</body>
<!-- END: Body-->

</html>