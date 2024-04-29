<?php
session_start();
if (isset($_SESSION['vin'], $_SESSION['accreditationID'])) {
  header("location:v-dashboard");
}

if (isset($_SESSION['adminID'])) {
  header("location:dashboard");
}

include("controllers/db_connect.php");
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
  <title>Reophamton SU :: E-voting System</title>
  <!-- <title>ROEHAMPTON SU :: E-voting System</title>-->
  <link rel="shortcut icon" type="image/png" href="images/roe.png">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <script>
    addEventListener("load", function() {
      setTimeout(hideURLbar, 0);
    }, false);

    function hideURLbar() {
      window.scrollTo(0, 1);
    }
  </script>

  <!-- css files -->
  <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
  <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all" />
  <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" media="all" />
  <link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css" media="all" />
  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
  <!-- /css files -->

  <style>
    /*CSS FOR AVOIDING HIDDEN CONTENT FLASH ON PAGE LOAD*/
    .adminLogin {
      display: none;
    }

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
      <div class="w3l-content">
        <div class="left-grid">
          <h2 class="text-w3layouts">Vote in your Right</h2>
          <span class="text-w3layouts"> It's all you've Got!</span>
          <center>
            <img src="images/vote.png" style="overflow:auto;height:100px;border-radius:6px 0px 0px 6px">
          </center>
        </div>
        <div class="right-grid" id="voterLogin">
          <div class="sub-form">
            <form method="post" id="emailValidationForm">
              <p>Enter your registered email address to be authenticated.</p>
              <input type="email" name="votersEmail" id="votersEmail" required="" placeholder="Enter your valid email" value="<?php echo ((isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : '') ?>" />
              <div>&nbsp;</div>
              <button type="submit" class="btn1" id="emailValidationBtn">
                <span class="fa fa-paper-plane" aria-hidden="true"></span>
              </button>
              <div>&nbsp;</div>

              <!-- <a href="index" style="text-decoration:none;color:white;">click here to refresh page</a> -->

            </form>
          </div>
          <span id="validateMessage">
            <?php if (isset($_SESSION["validateMessage"])) {
              echo $_SESSION["validateMessage"];
            } ?>
          </span>

          <!--Show email Validation Messages here!-->
          <span id="validateMessage"></span>

          <!--Show validation information-->
          <div id="validationInfo"> </div>
        </div>

        <!--Administrator Login Starts -->
        <div class="right-grid adminLogin" id="adminLogin">
          <p style="margin-left:15px">Ensure you are not trespassing, only authorized members are allowed here.</p>
          <div class="sub-form">
            <form id="adminLoginForm" style="margin: 0 auto;">

              <input type="email" name="adminUsername" id="adminUsername" size="30" required="" placeholder="Enter your valid Email" style='margin-top:2%' />
              <input type="password" name="adminPassword" id="adminPassword" size="30" required="" placeholder="Enter your valid password" style='margin-top:2%' /><br />
              <button class="btn1" id="adminLoginBtn" style="margin-top:2%">
                <span class="fa fa-paper-plane" aria-hidden="true"></span>
              </button>
            </form>
          </div>
          <span id="adminLoginMsg">
            <?php if (isset($_SESSION["validateMessage"])) {
              echo $_SESSION["validateMessage"];
            } ?></span>

          <!--For Unauthorized access-->

          <div>&nbsp;</div>

          <!--Show Validation Messages here!-->
          <div id="validationAdminMsg" style="margin-top:2%"></div>

          <div>&nbsp;</div>

          <div style="margin-top:2%">
            <!--Go back to vote form from admin-->
            <a style="cursor:pointer;color:#19f074;" id="vote"><i class="fa fa-home"></i> Go back home</a>
          </div>
        </div>
        <!--Administrator Login Starts -->
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
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/qrious.js"></script>

</html>
<script>
  $(document).ready(function() {

  });

  //Validation of email Address for voter >>>> starts
  $("#emailValidationForm").submit(function(e) {
    e.preventDefault();
    var email = $("#votersEmail").val();

    $.ajax({
      type: 'POST',
      url: 'controllers/login',
      async: true,
      data: {
        validateVoterEmail: 1,
        email: email
      },
      beforeSend: function(ve) {
        $("#votersEmail").prop("disabled", true);
        $("#validateMessage").html("<i style='font-size:13px'> <span class='fa fa-spin fa-spinner'></span> Please wait... </i>").css("color", "#ffffff").show();
      },
      success: function(ve) {
        $("#validationInfo").html(ve).show();
      }
    });
  });
  //Validation of  email Address for voter >>>> ends 

  //Switch of panels when clicked starts 
  $("#cPanel").click(function() {
    $("#voterLogin").hide(); //Hide the voters login form
    $("#adminLogin").show(); //show the admin login form
  });
  $("#vote").click(function() {
    $("#adminLogin").hide(); //Hide the voters login form
    $("#voterLogin").show(); //show the admin login form
  });
  //Switch of panels when clicked starts ends 

  //Validating administrator credentials >>>>  starts
  $('#adminLoginForm').submit(function(e) {
    e.preventDefault();
    var username = $("#adminUsername").val();
    var password = $("#adminPassword").val();
    $.ajax({
      type: 'POST',
      url: 'controllers/login',
      async: true,
      data: {
        validateAdminLogin: 1,
        username: username,
        password: password
      },
      beforeSend: function() {
        $("#validationAdminMsg").html("<i style='font-size:13px'> <span class='fa fa-spin fa-spinner'></span> Please wait... </i>").css("color", "#ffffff").show(); //show validation message onload
      },
      success: function(ve) {
        $("#validationInfo").html(ve).show();
      }
    });
  });
  //Validating of administrator credentials >>>> ends  

  //unsetting of invalid access starts 
  setTimeout(function() {
    <?php unset($_SESSION["validateMessage"]); ?>
    $("#validationMsg").hide();
    $("#adminLoginMsg").hide();
  }, 4000);
  //unsetting of invalid access ends
</script>