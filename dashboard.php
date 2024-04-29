<?php
session_start();

if (!isset($_SESSION['adminID']) || !isset($_SESSION['adminEmail'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "dashboard";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Administrative Portal :: Dashboard</title>
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
  <!-- END: Page CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">

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
    <div class="container-fluid site-width ">
      <!-- START: Breadcrumbs-->
      <div class="row ">
        <div class="col-12 align-self-center">
          <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
            <div class="w-sm-100 mr-auto">
              <h4 class="mb-0"><?php echo ucfirst((isset($_SESSION['adminFname']) && !empty($_SESSION['adminFname'])) ? "Welcome Back," . $_SESSION['adminFname'] : "Administrative Dashboard") ?></h4>
              <p>Always stay updated in your Administrative Portal!</p>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <?php echo date("D, d-M-Y"); ?><br />
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li> -->
          </div>
          </ol>
        </div>
      </div>
    </div>
    <!-- END: Breadcrumbs-->

    <!-- START: Card Data-->

    <!-- Display Dashboard Statistics -->
    <div id="displayDashboardStatistics"></div>
    <!-- Display Dashboard Statistics -->

    <div class="row">
      <!-- Candidate Manifesto -->
      <div class="col-12 col-lg-8 mt-3" style="min-height: rem;">
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
      <!-- Candidate Manifesto -->

      <!-- Display Recent Accredited voters -->
      <div class="col-lg-4 mt-3">
        <div id="displayRecentAccredited"></div>
      </div>
      <!-- Display Recent Accredited voters -->
    </div>
    <!-- END: Card DATA-->
    </div>
  </main>
  <!-- END: Content-->



  <!-- START: Footer-->
  <?php include("inc/footer.php"); ?>
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
  <script>
    $(document).ready(function() {
      adminDashboardRecentAccredited();
    });

    function adminDashboardStatistics() {
      $.ajax({
        url: "controllers/get-live-charts",
        type: "POST",
        async: false,
        data: {
          getAdminDashboardStat: 1
        },
        success: function(lDashboard) {
          $("#displayDashboardStatistics").html(lDashboard).show();
        },
        error: function(lDashboard) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
        }
      })
    }

    function adminDashboardRecentAccredited() {
      $.ajax({
        url: "controllers/get-live-charts",
        type: "POST",
        async: false,
        data: {
          getAdminDashboardRecentAccredited: 1
        },
        success: function(lDashboard) {
          $("#displayRecentAccredited").html(lDashboard).show();
          adminDashboardStatistics();
        },
        error: function(lDashboard) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
        }
      })
    }

    setInterval(adminDashboardRecentAccredited(), 2000);
  </script>
</body>
<!-- END: Body-->

</html>