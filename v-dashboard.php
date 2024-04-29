<?php
session_start();

if (!isset($_SESSION['vin']) || !isset($_SESSION['accreditationID'])) {
  header("location:./");
}

$page = "v-dashboard";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

unset($_SESSION['voteSessionID']);
?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Administrative Portal :: Voters Dashboard</title>
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
      <div class="row ">
        <div class="col-12  align-self-center">
          <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
            <div class="w-sm-100 mr-auto">
              <h4 class="mb-0"><?php echo ucfirst((isset($getLoggerInfo['fname']) && !empty($getLoggerInfo['fname'])) ? "Welcome Back," . $getLoggerInfo['fname'] : "Voters Dashboard") ?></h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <?php echo date("D, d-M-Y"); ?>
            </ol>
          </div>
        </div>
      </div>
      <!-- END: Breadcrumbs-->

      <!-- START: Card Data-->
      <div class="row">
        <div class="card card-body col-12">
          <div class="row">
            <div class="col-12 col-md-6 col-lg-8 mt-3">
              <h4 class="pl-4 pr-4 h5 font-w-600">Nominated Candidates Live Report
              </h4>


              <!-- Load Candidate Live Result Table -->
              <div class="card-body" id="displayCandidatesLiveResult" style="height:50rem; overflow:auto"> </div>
              <!-- Load Candidate Live Result Table -->


            </div>
            <div class="col-12 col-md-4 col-lg-4 mt-3">
              <div class="p-2">

                <!-- Load Statistic Reports Data Table -->
                <div class="card-body" id="displayLiveStatisticsData"> </div>
                <!-- Load Statistic Reports Data Table -->


                <center>
                  <button id="voteSessionBtn" type="button" class="btn btn-primary rounded-btn mt-4" style="font-size: 15px;" onClick="handleStartVoteSession();">
                    <i class='fas fa-vote-yea'></i> Start Vote
                  </button>
                </center>

                <div class="col-12 col-md-12 col-lg-12 mt-3">
                  <div class="card card-header">
                    <h4 class="card-title text-center">Candidate Manifestos</h4>
                  </div>
                  <div class="twitter-gradient p-5 text-center h-100">

                    <div id="demo" class="carousel slide pointer-event" data-ride="carousel">
                      <!-- The slideshow -->
                      <div class="carousel-inner">
                        <?php
                        $manifesto = "";
                        $stmt = $conn->prepare("SELECT * FROM election_candidates e INNER JOIN election_offices o ON o.id = e.office WHERE manifesto !=?");
                        $stmt->bind_param("s", $manifesto);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                          $num = 0;
                          while ($getManifesto = $result->fetch_array()) {
                            $num++;
                        ?>
                            <div class="carousel-item py-3 <?php echo (($num == 1) ? 'active' : ''); ?>">
                              <i class="fas fa-comment-dots p-2  border rounded-circle h3 mb-4 mx-auto d-table"></i>
                              <?php echo $getManifesto['manifesto']; ?>
                              <br><small><?php echo ucfirst($getManifesto['sname'] . " " . $getManifesto['fname']); ?></small><br><br>
                              <div class="love px-2 py-1 d-inline-block"> For <?php echo ucfirst($getManifesto['name']); ?></div>
                            </div>
                          <?php
                          }
                        } else { ?>
                          <div class="alert alert-danger">There are no candidate Manifesto available yet! </div>
                        <?php
                        }
                        ?>

                      </div>

                      <!-- Indicators -->
                      <ul class="carousel-indicators position-relative mb-0">
                        <?php

                        $stmt->execute();
                        $result = $stmt->get_result();
                        $dataSlide = 0;
                        while ($getManifesto = $result->fetch_array()) {
                          $dataSlide++;
                        ?>
                          <li data-target="#demo" data-slide-to="<?php echo $dataSlide; ?>" class="<?php echo (($dataSlide == 1) ? 'active' : ''); ?>"></li>
                        <?php
                        }
                        $stmt->close();
                        ?>
                      </ul>

                    </div>
                  </div>
                </div>

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
  <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
  <!-- END: Template JS-->

  <!-- START: APP JS-->
  <script src="dist/js/app.js"></script>
  <!-- END: APP JS-->
  <script>
    $(document).ready(function() {
      getLiveStatisticsData(); //load Statistics data and Live result at deafult

    });

    //Get Candidates Statistics Table >>> Starts
    function getCandidatesLiveResult() {
      $.ajax({
        url: "controllers/get-live-charts",
        type: "POST",
        async: false,
        data: {
          getCandidateLiveResult: 1
        },
        success: function(lcads) {
          $("#displayCandidatesLiveResult").html(lcads).show();
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
    //Get Candidates Statistics Table >>> Ends

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
          getCandidatesLiveResult();

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

    //Function To start vote >>>> Starts
    function handleStartVoteSession() {
      $.ajax({
        url: "controllers/get-votes",
        type: "POST",
        async: true,
        data: {
          enableStartVoteSession: 1
        },
        beforeSend: function(response) {
          $("#voteSessionBtn").prop("disabled", true);
          $("#voteSessionBtn").html("<span><i class='fa fa-spin fa-spinner'></i>Please wait...</span>").show();
        },
        success: function(response) {

          var status = response.status;
          var message = response.message;
          var header = response.header;
          var responseStatus = response.responseStatus;
          var btnText = response.btnText;
          var voteSessionStatus = response.voteSessionStatus;

          swal(header, message, responseStatus);

          $("#voteSessionBtn").prop("disabled", status);
          $("#voteSessionBtn").html(btnText).show();
          if (voteSessionStatus === true) {
            setTimeout(function() {
              window.location.href = './vote';
            }, 3000);
          }

        },
        error: function(xhr, status, error) {
          $("#voteSessionBtn").prop("disabled", false);
          console.error("AJAX Error:", status, error);
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            handleStartVoteSession();
          }, 5000);
        }
      });

    }
    //Function To start vote >>>> Ends
  </script>
</body>
<!-- END: Body-->

</html>