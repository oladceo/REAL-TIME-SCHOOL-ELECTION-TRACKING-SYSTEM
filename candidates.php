<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "candidates";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Administrative Portal :: Election Candidates</title>
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
              <h4 class="mb-0">Candidates</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="candidates">Candidates</a></li>
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
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewCandidate" style="float:right"> + Add New Candidate</button>
            </div>
            <div class="card-body">
              <input type="search" id="candidatesSearchEntry" class="form-control mt-4" placeholder="Search For Candidates" />
              <!-- ******display table data -->
              <div class="card-body" id="displayCandidates"> </div>
              <!-- ******display table data -->
            </div>
          </div>
        </div>
        <!-- Modal Section to Add New Candidate -->
        <div class="modal fade bd-example-modal-xl" id="addNewCandidate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel10">Add New Candidate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="addNewCandidateForm">
                <div class="modal-body">
                  <div class="row mt-3">

                    <!--New Candidate Inputs-->
                    <div class="col-8 mt-3">
                      <div class="card">
                        <div class="card-content">
                          <div class="card-body py-5">
                            <div class="row">

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateOffice">Vying Office<span class="text-danger">*</span><a href="offices" class="text-primary" style="float:right">+ Add New</a>
                                    <select class="form-control" id="candidateOffice" name="candidateOffice" required="">
                                      <option value="">Select an Office</option>
                                      <?php
                                      $id = " ";
                                      $status = "active";
                                      $stmt = $conn->prepare("SELECT * FROM election_offices WHERE id !=? AND status=?");
                                      $stmt->bind_param("ss", $id, $status);
                                      $stmt->execute() or die($stmt->error);
                                      $result = $stmt->get_result();
                                      if ($result->num_rows > 0) {
                                        while ($getOffice = $result->fetch_assoc()) { ?>
                                          <option value="<?php echo $getOffice['id']; ?>"><?php echo $getOffice['name']; ?></option>
                                      <?php
                                        }
                                        $stmt->close();
                                      } else {
                                        echo "<option class='text-danger' value=''>No office is available yet</option>";
                                      }
                                      ?>

                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateStatus">Status<span class="text-danger">*</span>
                                    <select class="form-control" type="text" name="candidateStatus" id="candidateStatus" required="">
                                      <option value="active">Active</option>
                                      <option value="inactive">Inactive</option>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateSname">Surname<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="candidateSname" id="candidateSname" placeholder="Enter Candidate Surname" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateFname">First Name<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="candidateFname" id="candidateFname" placeholder="Enter Candidate First Name" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateOname">Other Names
                                    <input class="form-control" type="text" name="candidateOname" id="candidateOname" placeholder="Enter Candidate Other Names" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateGender">Gender<span class="text-danger">*</span>
                                    <select class="form-control" type="text" name="candidateGender" id="candidateGender" required="">
                                      <option value="male">Male</option>
                                      <option value="female">Female</option>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidateEmail">Email Address<span class="text-danger">*</span>
                                    <input class="form-control" type="email" name="candidateEmail" id="candidateEmail" placeholder="Enter Candidate Email Address" required="" />
                                    <span id="emailVerMsg" class="text-danger"></span>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="candidatePhone">Phone<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="candidatePhone" id="candidatePhone" placeholder="Enter Candidate Pone Number" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="candidateAddress">Candidate Resident Address<span class="text-danger">*</span>
                                    <textarea class="form-control" placeholder="Enter Candidate Resident Address" name="candidateAddress" id="candidateAddress" required=""></textarea>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="candidateManifesto">Candidate Manifesto
                                    <textarea class="form-control" rows="5" maxlength="250" placeholder="Enter Candidate Manifesto" name="candidateManifesto" id="candidateManifesto"></textarea>
                                  </label>
                                </div>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--New Candidate Inputs-->


                    <!--Candidate Image-->
                    <div class="col-4 mt-3">
                      <div class="card">
                        <div class="card-content">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Candidate Image Preview</h4>
                          </div>
                          <div class="card-body py-5">
                            <center class="col-12" style="margin: 0px auto;">
                              <img src="dist/no-preview.jpeg" style="width:280px;height:280px" id="newCandidateImagePreview" />
                              <div>&nbsp;</div>
                              <label for="newCandidateImage" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Candidate Image<input id="newCandidateImage" name="newCandidateImage" type="file" onchange="previewImage(event);" />
                              </label>
                            </center>

                          </div>
                        </div>
                      </div>
                    </div>
                    <!--Candidate Image-->
                  </div>

                </div>
                <div class="modal-footer">
                  <center style="margin: 0px auto;">
                    <span id="addNewCandidateMsg"></span>
                  </center>
                  <button type="submit" class="btn btn-primary" id="saveNewCandidate">Save Candidate</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Modal Section to Add New Candidate -->
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
      loadCandidates(1);
    });

    // PREVIEW CANDIDATE IMAGE ONCE ITS SELECTED STARTS
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
        const imagePreviewElement = document.querySelector("#newCandidateImagePreview");
        const imagePreviewElement2 = document.querySelector("#modifyCandidateImagePreview");
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
    // PREVIEW CANDIDATE IMAGE ONCE ITS SELECTED ENDS

    //LOAD CANDIDATES FUNCTION>>>>>>STARTS
    function loadCandidates(page, query = '') {
      //var sortStatus = $("#sortStatus").val();
      //var candidatesPageLimit = $("#paymentPageLimit").val();

      $.ajax({
        url: "controllers/get-candidates",
        method: "POST",
        async: true,
        data: {
          showCandidates: 1,
          query: query,
          page: page
        },
        beforeSend: function() {
          $("#displayCandidates").html("").show();
          showCandidatesSkeletonLoader();
        },
        success: function(data) {
          setTimeout(function() {
            $("#displayCandidates").html(data).show();

            $('.skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
        },
        error: function(data) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            loadCandidates(page);
          }, 5000);
        }
      });
    };

    $(document).on('click', '.candidate-page-link', function() {
      var page = $(this).data('page_number');
      var query = $('#candidatesSearchEntry').val();
      loadCandidates(page, query);
    });

    $('#candidatesSearchEntry').on('keyup change paste', function() {
      var query = $('#candidatesSearchEntry').val();
      loadCandidates(1, query);

    });

    $("#candidatesPageLimit").on("change", function() { //page limit
      loadCandidates(1);
    });

    // Skeleton Loader
    function showCandidatesSkeletonLoader() {
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
        $('#displayCandidates').append(skeletonHtml);
      }
    }
    //LOAD CANDIDATES FUNCTION>>>>>>ENDS

    //ADD NEW CANDIDATE FUNCTION>>>>>>STARTS
    $("#addNewCandidateForm").submit(function(e) {
      e.preventDefault();


      var addCandidateForm = new FormData($("#addNewCandidateForm")[0]);
      swal({
          title: "Are you sure to add Candidate?",
          text: "You are about adding a new candidate to the portal.",
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
            url: 'controllers/get-candidates',
            async: true,
            processData: false,
            contentType: false,
            // mimeType: 'multipart/form-data',
            // cache: false,
            data: addCandidateForm,
            beforeSend: function() { // Corrected from beforeSubmit to beforeSend
              $("#saveNewCandidate").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
            },
            success: function(response) {
              var status = response.status;
              var message = response.message;
              var responseStatus = response.responseStatus;
              var header = response.header;

              if (status === true) {
                $("#addNewCandidateMsg").html(response).css("color", "green").show();
                $("#addNewCandidateForm")[0].reset();
                document.querySelector("#newCandidateImagePreview").src = "dist/no-preview.jpeg"; //reset the images preview.
                swal(header, message, responseStatus);
              } else {
                swal(header, message, responseStatus);
              }
            },
            error: function() {
              $("#addNewCandidateMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
              swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
            },
            complete: function() { // Moved the timeout code to the complete callback
              setTimeout(function() {
                $("#addNewCandidateMsg").fadeOut(300);
              }, 3000);
              $("#saveNewCandidate").html("Save Candidate").show(); // Reset the button text
            }
          });
        });
    });
    //ADD NEW CANDIDATE FUNCTION>>>>>>ENDS

    //LOAD CANDIDATE EDIT FUNCTION >>>>STARTS
    function loadCandidatesEditModal(button) {
      var candidateID = $(button).data("value");
      //console.log(candidateID);
      $.ajax({
        url: 'controllers/get-candidates',
        method: 'POST',
        async: true,
        data: {
          getCandidatesEdit: 1,
          candidateID: candidateID
        },
        beforeSend: function(gRcs) {
          $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
          $('#displayCandidatesInputs').html("").show();
          modalSkeletonLoader();
        },
        success: function(gRcs) {
          $("#candidatesEditModal").modal('show');
          $(button).html('<span class="fas fa-bars"></span>').show();
          setTimeout(function() {
            $('#displayCandidatesInputs').html(gRcs).show();
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
        $('#displayCandidatesInputs').append(skeletonHtml);
        $('#displayAllocatedCandidatesInputs').append(skeletonHtml);
      }
    }
    //LOAD CANDIDATE EDIT FUNCTION >>>>ENDS

    //FUNCTION TO VERIFY CANDIDATE EMAIL FOR DUPLICATE  >>>STARTS
    $("#candidateEmail").on("input paste", function() {
      verifyCandidateEmail();
    });

    function verifyCandidateEmail() {
      var email = $("#candidateEmail").val();
      $.ajax({
        type: 'POST',
        url: 'controllers/get-candidates',
        async: true,
        data: {
          candidateEmailVer: 1,
          email: email
        },
        success: function(response) {
          var status = response.status;

          if (status === true) {
            $("#emailVerMsg").html("Email has already exist").css("color", "red").show();
            $("#saveNewCandidate").hide();
          } else {
            $("#saveNewCandidate").show();
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
    //FUNCTION TO VERIFY CANDIDATE EMAIL FOR DUPLICATE  >>>ENDS
  </script>

</body>
<!-- END: Body-->

</html>