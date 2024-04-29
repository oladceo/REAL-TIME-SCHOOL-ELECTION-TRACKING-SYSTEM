<?php
session_start();
include("db_connect.php");

//LOAD VOTERS TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showVoters']) && isset($_POST['query'])) {
  include("db_connect.php");

  //	echo "Great a table"; die();
  $limit = 20; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }


  $whereSQL = " vin != '' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " vin != '' AND (vin LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR sname LIKE '%" . str_replace(' ', '%', $keyword) . "%'  OR fname LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR oname LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR gender LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR email LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR phone LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR address LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR vote_status LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT * FROM election_voters  WHERE $whereSQL ";
  $query .= 'ORDER BY sname ';

  $filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . ' ';

  $statement = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $total_data = mysqli_num_rows($statement);

  $statement = mysqli_query($conn, $filter_query);
  $total_filter_data = mysqli_num_rows($statement);

  $output = ''; // Initialize output to accept HTML content
?>

  <div class="table-responsive">
    <table id="" class="display table dataTable table-striped table-bordered">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">VIN</th>
          <th class="text-center">Last Name</th>
          <th class="text-center">First Name</th>
          <th class="text-center">Other Name(s)</th>
          <th class="text-center">Gender</th>
          <th class="text-center">Email</th>
          <th class="text-center">Phone</th>
          <th class="text-center">Votes Status</th>
          <th class="text-center">Registration Date</th>
          <th class="text-center">Options</th>
        </tr>
      </thead>
      <tbody id="">
        <?php

        if ($total_data > 0) {
          $sn = 0;
          while ($result = mysqli_fetch_array($statement)) {
        ?>

            <tr>
              <td class="text-center">
                <?php
                $passportPath = "resources/" . $result["passport"];
                $defaultImage = "../images/no-preview.jpeg";
                ?>
                <img src="<?php if (file_exists("../" . $passportPath)) {
                            echo $passportPath;
                          } else {
                            echo $defaultImage;
                          } ?>" alt="voter Image" width="50" class="rounded-circle  ml-auto" />
              </td>
              <td class="text-center"><?php echo strtoupper($result['vin']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['sname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['fname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['oname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['gender']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['email']); ?></td>
              <td class="text-center"><?php echo $result['phone']; ?></td>
              <td class="text-center"><?php echo ucfirst($result['vote_status']); ?></td>
              <td class="text-center"><?php echo date("D. d-m-Y, h:i A"); ?></td>
              <td class="text-center">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#votersEditModal" onClick="loadVotersEditModal(this);" data-value="<?php echo $result['vin']; ?>">
                  <span class="fas fa-bars"></span>
                </a>
              </td>
            </tr>

          <?php    }
        } else { ?>
          <tr class="table-danger">
            <td colspan="13" class="text-center">No Voter was found!</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">VIN</th>
          <th class="text-center">Last Name</th>
          <th class="text-center">First Name</th>
          <th class="text-center">Other Name(s)</th>
          <th class="text-center">Gender</th>
          <th class="text-center">Email</th>
          <th class="text-center">Phone</th>
          <th class="text-center">Votes Status</th>
          <th class="text-center">Registration Date</th>
          <th class="text-center">Options</th>
        </tr>
      </tfoot>
    </table>
    <!-- Modal Section for modify Voter Starts -->
    <div class="modal fade bd-example-modal-xl" id="votersEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">

          <div class="modal-body" id="displayVotersInputs"></div>
        </div>
      </div>
    </div>
    <!-- Modal Section for modify Voter Starts -->
  </div>

  <div class="mt-6 card-body">
    <nav aria-label="...">
      <ul class="pagination rounded-active justify-content-center">
        <?php
        $total_links = ceil($total_data / $limit);
        $previous_link = '';
        $next_link = '';
        $page_link = '';

        if ($total_links > 5) {
          if ($page < 5) {
            for ($count = 1; $count <= 5; $count++) {
              $page_array[] = $count;
            }
            $page_array[] = '...';
            $page_array[] = $total_links;
          } else {
            $end_limit = $total_links - 5;
            if ($page > $end_limit) {
              $page_array[] = 1;
              $page_array[] = '...';
              for ($count = $end_limit; $count <= $total_links; $count++) {
                $page_array[] = $count;
              }
            } else {
              $page_array[] = 1;
              $page_array[] = '...';
              for ($count = $page - 1; $count <= $page + 1; $count++) {
                $page_array[] = $count;
              }
              $page_array[] = '...';
              $page_array[] = $total_links;
            }
          }
        } else {
          for ($count = 1; $count <= $total_links; $count++) {
            $page_array[] = $count;
          }
        }

        if (isset($page_array) && count($page_array) >= 1) { // This (if statement) line might be useful on other projects where pagination has been used
          for ($count = 0; $count < count($page_array); $count++) {
            if ($page == $page_array[$count]) {
              $page_link .= '
                            <li class="page-item active">
                                <a class="page-link voter-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link voter-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
              } else {
                $previous_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Previous</a>
                            </li>
                            ';
              }
              $next_id = $page_array[$count] + 1;
              if ($next_id > $total_links) {
                $next_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Next</a>
                            </li>
                            ';
              } else {
                $next_link = '<li class="page-item"><a class="page-link voter-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link voter-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link voter-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
                            ';
              }
            }
          }
        }
        $output .= $previous_link . $page_link . $next_link;
        $start_result = ($page - 1) * $limit + 1;
        $end_result = min($start_result + $limit - 1, $total_data);

        echo $output; ?>
      </ul>
    </nav>
  </div>

  <div class="text-center mt-2"><?php echo  $start_result . ' - ' . $end_result . ' of ' . $total_data . ' Results'; ?></div>
  <?php

  exit();
}
//LOAD VOTERS TABLE FUNCTION ||||||Ends>>>>>>>

//ADD NEW VOTER FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['voterFname'], $_POST['voterEmail'], $_POST['voterSname'])) {

  $electionStatus = 1;
  $sn = 1;

  //Condition to check if election is activated||||-> If election is activated, candidate should not be created
  $stmt = $conn->prepare("SELECT COUNT(*) FROM  election_settings WHERE system_status=? AND sn=?");
  $stmt->bind_param("ss", $electionStatus, $sn);
  $stmt->execute() or die(mysqli_error($conn));
  $stmt->bind_result($getElectionStatus);
  $stmt->fetch();
  $stmt->close();

  if ($getElectionStatus > 0) {

    $status = false;
    $header = 'Restricted!';
    $message = 'You can only proceed with this action when election status is **deactivated**. Kindly update election status on settings to continue!';
    $responseStatus = 'warning';
  } else {


    ///////////////////////////////////////////////////////////////////////////
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $vin = mysqli_real_escape_string($conn, $randomString);
    ///////////////////////////////////////////////////////////////////////////


    date_default_timezone_set("Africa/Lagos"); // Corrected timezone capitalization
    $date = date("j-m-Y, g:i a");
    $voterStatus = "inactive";
    $voterSname = $_POST['voterSname'];
    $voterFname = $_POST['voterFname'];
    $voterOname = $_POST['voterOname'];
    $voterGender = $_POST['voterGender'];
    $voterEmail = $_POST['voterEmail'];
    $voterPhone = $_POST['voterPhone'];
    $voterAddress = $_POST['voterAddress'];
    $modifiedDate = "";
    $biometrics = "";
    $regAgent = $_SESSION['adminID'];
    $last_log = "nill";
    $onlineStatus = "offline";
    $accreditationID = "";
    $vote_date = "";
    $voteSessionID = "";

    // Check for voter duplicates 
    $stmt = $conn->prepare("SELECT * FROM election_voters WHERE sname = ? AND fname=?");
    $stmt->bind_param("ss", $voterSname, $voterFname);
    $result = $stmt->execute() ? $stmt->get_result() : false;
    $getDuplicate = $result ? $result->fetch_array() : false;

    if ($getDuplicate) {
      $status = false;
      $header = 'Duplicate Entry!';
      $message = 'Voter Already Exist';
      $responseStatus = 'warning';
    } else {
      if (empty($_FILES['newVoterImage']['tmp_name'])) {
        $status = false;
        $header = 'Empty Image!';
        $message = 'Voter Image can not be empty';
        $responseStatus = 'warning';
      } else {
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
        $path = 'passport/'; // upload directory
        $directory = '../resources/passport/'; // upload directory

        $img = $_FILES['newVoterImage']['name'];
        $tmp = $_FILES['newVoterImage']['tmp_name'];

        // get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        // can upload same image using rand function
        $final_image = rand(1000, 1000000) . $img;

        // check's valid format
        if (in_array($ext, $valid_extensions)) {
          $path = $path . strtolower($final_image);
          $directory = $directory . strtolower($final_image);

          if (move_uploaded_file($tmp, $directory)) {

            // Insert new voter
            $stmt = $conn->prepare("INSERT INTO election_voters(`vin`, `sname`, `fname`, `oname`, `gender`, `email`, `phone`, `address`, `passport`,  `vote_status`, `reg_date`, `modified_date`,`biometric`,`reg_agent`, `last_log`, `online_status`, `accreditationID`,`vote_date`,`voteSessionID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssssssssssssss", $vin, $voterSname, $voterFname, $voterOname, $voterGender, $voterEmail, $voterPhone, $voterAddress, $path, $voterStatus, $date, $modifiedDate, $biometrics, $regAgent, $last_log, $onlineStatus, $accreditationID, $vote_date, $voteSessionID);
            $result = $stmt->execute() or die(mysqli_error($conn));

            if ($result) {
              $status = true;
              $header = 'Successful!';
              $message = 'Voter Added Successfully';
              $responseStatus = 'success';
            } else {
              $status = false;
              $header = 'Failed!';
              $message = 'An error occurred, try again';
              $responseStatus = 'error';
            }
          }
          $stmt->close();
        }
      }
    }
  }
  $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

  header('Content-Type: application/json');
  echo json_encode($response);


  exit();
}
//ADD NEW VOTER FUNCTION |||||||Ends>>>>>>>>>>

//LOAD VOTER EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getVotersEdit']) && !empty($_POST['vin'])) {
  $vin = mysqli_real_escape_string($conn, $_POST['vin']);

  //get voter information 
  $stmt = $conn->prepare("SELECT * FROM election_voters WHERE vin=?");
  $stmt->bind_param("s", $vin);
  $result = $stmt->execute() ? $stmt->get_result()  : false;
  $getVoterData = $result ? $result->fetch_array() : false;


  if ($getVoterData) { ?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10">Modify <?php echo ucfirst($getVoterData['fname'] . " " . $getVoterData['sname']); ?> Record</h5>
      <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
    </div>
    <form id="voterEditForm">

      <div class="row mt-3">

        <!--Modify Voter Inputs-->
        <div class="col-8 mt-3">
          <div class="card">
            <div class="card-content">
              <div class="card-body py-5">
                <div class="row">
                  <input type="hidden" name="modifyVoterVin" value="<?php echo $getVoterData['vin']; ?>" />
                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterSname">Surname
                        <input class="form-control" type="text" name="modifyVoterSname" id="modifyVoterSname" placeholder="Enter Voter Surname" required="" value="<?php echo $getVoterData['sname']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterFname">First Name
                        <input class="form-control" type="text" name="modifyVoterFname" id="modifyVoterFname" placeholder="Enter Voter First Name" required="" value="<?php echo $getVoterData['fname']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterOname">Other Names
                        <input class="form-control" type="text" name="modifyVoterOname" id="modifyVoterOname" placeholder="Enter Voter Other Names" value="<?php echo $getVoterData['oname']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterGender">Gender
                        <select class="form-control" type="text" name="modifyVoterGender" id="modifyVoterGender" required="">
                          <option value="male" <?php echo (($getVoterData['gender'] == "male") ? 'selected' : ''); ?>>Male</option>
                          <option value="female" <?php echo (($getVoterData['gender'] == "female") ? 'selected' : ''); ?>>Female</option>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterEmail">Email Address
                        <input class="form-control" type="email" name="modifyVoterEmail" id="modifyVoterEmail" placeholder="Enter Voter Email Address" required="" value="<?php echo $getVoterData['email']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterPhone">Phone
                        <input class="form-control" type="text" name="modifyVoterPhone" id="modifyVoterPhone" placeholder="Enter Voter Pone Number" required="" value="<?php echo $getVoterData['phone']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-12">
                    <div class="input-group">
                      <label class="col-12" for="modifyVoterAddress">Voter Resident Address
                        <textarea class="form-control" placeholder="Enter Voter Resident Address" name="modifyVoterAddress" id="modifyVoterAddress" required=""><?php echo $getVoterData['address']; ?></textarea>
                      </label>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Modify Voter Inputs-->

        <!--Voter Image-->
        <div class="col-4 mt-3">
          <div class="card">
            <div class="card-content">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Voter Image Preview</h4>
              </div>
              <div class="card-body py-5">
                <center class="col-12" style="margin: 0px auto;">
                  <?php
                  $passportPath = "resources/" . $getVoterData["passport"];
                  $defaultImage = "../images/no-preview.jpeg";
                  ?>
                  <img src="<?php if (file_exists("../" . $passportPath)) {
                              echo $passportPath;
                            } else {
                              echo $defaultImage;
                            } ?>" style="width:280px;height:280px" id="modifyVoterImagePreview" />
                  <div>&nbsp;</div>
                  <label for="modifyVoterImage" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Voter Image<input id="modifyVoterImage" name="modifyVoterImage" type="file" onchange="previewImage(event);" />
                  </label>
                </center>

              </div>
            </div>
          </div>
        </div>
        <!--Voter Image-->
      </div>
      <div class="modal-footer">
        <center style="margin: 0px auto;">
          <span id="modifyVoterMsg"></span>
        </center>
        <button type="submit" class="btn btn-warning" id="updateVoterBtn">Update Voter</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>
    <script>
      $("#voterEditForm").submit(function(e) {
        e.preventDefault();
        //console.log('welcome');
        var voterForm = new FormData($("#voterEditForm")[0]);
        swal({
            title: "Are you sure to update Voter?",
            text: "Updating this voter is effective across the portal.",
            icon: 'question',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-success',
            cancelButtonClass: 'btn-danger',
            confirmButtonText: 'Yes, Update!',
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
              data: voterForm,
              beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                $("#updateVoterBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
              },
              success: function(response) {

                var status = response.status;
                var message = response.message;
                var responseStatus = response.responseStatus;
                var header = response.header;

                if (status === true) {
                  $("#modifyVoterMsg").html(response).css("color", "green").show();
                  swal(header, message, responseStatus);
                  //loadVoters(); //Reload registered voters table
                } else {
                  swal(header, message, responseStatus);
                }
              },
              error: function() {
                $("#modifyVoterMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
              },
              complete: function() { // Moved the timeout code to the complete callback
                setTimeout(function() {
                  $("#modifyVoterMsg").fadeOut(300);
                }, 3000);
                $("#updateVoterBtn").html("Update Voter").show(); // Reset the button text
              }
            });
          });
      });
    </script>
  <?php
  }

  exit();
}
//LOAD VOTER EDIT MODAL |||||||| Ends >>>>>>>>>>

//UPDATE VOTER FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['modifyVoterVin'], $_POST['modifyVoterFname'], $_POST['modifyVoterEmail'])) {
  $electionStatus = 1;
  $sn = 1;

  //Condition to check if election is activated||||-> If election is activated, candidate should not be created
  $stmt = $conn->prepare("SELECT COUNT(*) FROM  election_settings WHERE system_status=? AND sn=?");
  $stmt->bind_param("ss", $electionStatus, $sn);
  $stmt->execute() or die(mysqli_error($conn));
  $stmt->bind_result($getElectionStatus);
  $stmt->fetch();
  $stmt->close();

  if ($getElectionStatus > 0) {

    $status = false;
    $header = 'Restricted!';
    $message = 'You can only proceed with this action when election status is **deactivated**. Kindly update election status on settings to continue!';
    $responseStatus = 'warning';
  } else {


    date_default_timezone_set("Africa/Lagos");
    $date = date("j-m-Y, g:i a");
    $modifyVin = $_POST['modifyVoterVin'];
    $modifyVoterSname = $_POST['modifyVoterSname'];
    $modifyVoterFname = $_POST['modifyVoterFname'];
    $modifyVoterOname = $_POST['modifyVoterOname'];
    $modifyVoterGender = $_POST['modifyVoterGender'];
    $modifyVoterEmail = $_POST['modifyVoterEmail'];
    $modifyVoterPhone = $_POST['modifyVoterPhone'];
    $modifyVoterAddress = $_POST['modifyVoterAddress'];

    // Check for voter duplicates 
    $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin = ? AND sname = ? AND fname = ? AND oname =? AND gender=? AND email =? AND phone= ? AND address =?");
    $stmt->bind_param("ssssssss", $modifyVin, $modifyVoterSname, $modifyVoterFname,  $modifyVoterOname, $modifyVoterGender, $modifyVoterEmail, $modifyVoterPhone, $modifyVoterAddress);
    $stmt->execute();
    $stmt->bind_result($duplicateCount);
    $stmt->fetch();
    $stmt->close();


    if ($duplicateCount > 0 && empty($_FILES['modifyVoterImage']['tmp_name'])) {
      $status = false;
      $header = 'No Changes!';
      $message = 'There is noting to update';
      $responseStatus = 'warning';
    } else {

      if (!empty($_FILES['modifyVoterImage']['tmp_name'])) { //if there is a new file to upload for voter passport
        $getVin = mysqli_real_escape_string($conn, $_POST['modifyVoterVin']);
        //get voter existing image
        $queryVoter = mysqli_query($conn, "SELECT * FROM election_voters WHERE vin ='$getVin'") or die(mysqli_error($conn));
        $getVoterData = mysqli_fetch_array($queryVoter);

        //remove the existing image
        $unlinkPath = "../resources/" . $getVoterData["passport"];
        if (file_exists($unlinkPath)) { //first check if file exists
          unlink($unlinkPath);
        }

        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
        $path = 'passport/'; // upload directory
        $directory = '../resources/passport/'; // upload directory

        $img = $_FILES['modifyVoterImage']['name'];
        $tmp = $_FILES['modifyVoterImage']['tmp_name'];

        // get uploaded file's extension            
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        // can upload same image using rand function
        $final_image = rand(1000, 1000000) . $img;

        // check's valid format
        if (in_array($ext, $valid_extensions)) {
          $path = $path . strtolower($final_image);
          $directory = $directory . strtolower($final_image);

          if (move_uploaded_file($tmp, $directory)) {
            // Updated Registered voter with new passport
            $stmt = $conn->prepare("UPDATE election_voters SET sname = ?, fname = ?, oname = ?, gender = ?, email = ?, phone = ?, address = ?, passport = ?, modified_date = ? WHERE vin = ?");
            $stmt->bind_param("ssssssssss", $modifyVoterSname, $modifyVoterFname, $modifyVoterOname, $modifyVoterGender, $modifyVoterEmail, $modifyVoterPhone, $modifyVoterAddress, $path, $date, $modifyVin);
            $queryUpdateVoter = $stmt->execute() or die(mysqli_error($conn));

            if ($queryUpdateVoter) {

              $status = true;
              $header = 'Successful!';
              $message = 'Voter Updated Successfully';
              $responseStatus = 'success';
            } else {

              $status = false;
              $header = 'Failed!';
              $message = 'An error occurred, try again';
              $responseStatus = 'error';
            }
          }
        }
        $stmt->close();
      } else {
        // Updated Registered voter
        $stmt = $conn->prepare("UPDATE election_voters SET sname = ?, fname = ?, oname = ?, gender = ?, email = ?, phone = ?, address = ?, modified_date = ? WHERE vin = ?");
        $stmt->bind_param("sssssssss", $modifyVoterSname, $modifyVoterFname, $modifyVoterOname, $modifyVoterGender, $modifyVoterEmail, $modifyVoterPhone, $modifyVoterAddress, $date, $modifyVin);
        $queryUpdateVoter = $stmt->execute() or die(mysqli_error($conn));

        if ($queryUpdateVoter) {

          $status = true;
          $header = 'Successful!';
          $message = 'Voter Updated Successfully';
          $responseStatus = 'success';
        } else {

          $status = false;
          $header = 'Failed!';
          $message = 'An error occurred, try again';
          $responseStatus = 'error';
        }


        $stmt->close();
      }
    }
  }
  $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//UPDATE VOTER FUNCTION |||||||Ends>>>>>>>>>>

//LOAD ACCREDITED VOTERS TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showAccreditedVoters']) && isset($_POST['query'])) {
  include("db_connect.php");

  //	echo "Great a table"; die();
  $limit = 20; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }


  $whereSQL = " vin != '' AND accreditationID !='' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " vin != '' AND accreditationID !='' AND (vin LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR sname LIKE '%" . str_replace(' ', '%', $keyword) . "%'  OR fname LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR oname LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR gender LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR email LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR phone LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR address LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR vote_status LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR accreditationID LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT * FROM election_voters  WHERE $whereSQL ";
  $query .= 'ORDER BY sname ';

  $filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . ' ';

  $statement = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $total_data = mysqli_num_rows($statement);

  $statement = mysqli_query($conn, $filter_query);
  $total_filter_data = mysqli_num_rows($statement);

  $output = ''; // Initialize output to accept HTML content
  ?>

  <div class="table-responsive">
    <table id="" class="display table dataTable table-striped table-bordered">
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">VIN</th>
          <th class="text-center">Last Name</th>
          <th class="text-center">First Name</th>
          <th class="text-center">Other Name(s)</th>
          <th class="text-center">Gender</th>
          <th class="text-center">Email</th>
          <th class="text-center">Phone</th>
          <th class="text-center">Votes Status</th>
          <th class="text-center">Registration Date</th>
          <th class="text-center">Accreditation Status</th>
        </tr>
      </thead>
      <tbody id="">
        <?php

        if ($total_data > 0) {
          $sn = 0;
          while ($result = mysqli_fetch_array($statement)) {

            $stmt = $conn->prepare("SELECT COUNT(*) FROM election_votes WHERE vin=?");
            $stmt->bind_param("s", $vin);
            $stmt->execute();
            $stmt->bind_result($voteEntryStatus);
            $stmt->fetch();
            $stmt->close();

        ?>

            <tr>
              <td class="text-center">
                <?php
                $passportPath = "resources/" . $result["passport"];
                $defaultImage = "../images/no-preview.jpeg";
                ?>
                <img src="<?php if (file_exists("../" . $passportPath)) {
                            echo $passportPath;
                          } else {
                            echo $defaultImage;
                          } ?>" alt="voter Image" width="50" class="rounded-circle  ml-auto" />
              </td>
              <td class="text-center"><?php echo strtoupper($result['vin']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['sname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['fname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['oname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['gender']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['email']); ?></td>
              <td class="text-center"><?php echo $result['phone']; ?></td>
              <td class="text-center"><?php echo ucfirst($result['vote_status']); ?></td>
              <td class="text-center"><?php echo date("D. d-m-Y, h:i A"); ?></td>
              <td class="text-center">
                <?php echo (($voteEntryStatus > 0) ? "<button type='button' class='btn btn-primary btn-sm'>Voted <i class='fa fa-check'></i></button>" : "<button type='button' class='btn btn-warning btn-sm' onClick='reAccreditVoter(this);' value='" . $result['vin'] . "' ><i class='fas fa-history'></i>Re-accredit Voter</button>"); ?>
              </td>
            </tr>

          <?php    }
        } else { ?>
          <tr class="table-danger">
            <td colspan="13" class="text-center">No Accredited Voter was found!</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">VIN</th>
          <th class="text-center">Last Name</th>
          <th class="text-center">First Name</th>
          <th class="text-center">Other Name(s)</th>
          <th class="text-center">Gender</th>
          <th class="text-center">Email</th>
          <th class="text-center">Phone</th>
          <th class="text-center">Votes Status</th>
          <th class="text-center">Registration Date</th>
          <th class="text-center">Accreditation Status</th>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="mt-6 card-body">
    <nav aria-label="...">
      <ul class="pagination rounded-active justify-content-center">
        <?php
        $total_links = ceil($total_data / $limit);
        $previous_link = '';
        $next_link = '';
        $page_link = '';

        if ($total_links > 5) {
          if ($page < 5) {
            for ($count = 1; $count <= 5; $count++) {
              $page_array[] = $count;
            }
            $page_array[] = '...';
            $page_array[] = $total_links;
          } else {
            $end_limit = $total_links - 5;
            if ($page > $end_limit) {
              $page_array[] = 1;
              $page_array[] = '...';
              for ($count = $end_limit; $count <= $total_links; $count++) {
                $page_array[] = $count;
              }
            } else {
              $page_array[] = 1;
              $page_array[] = '...';
              for ($count = $page - 1; $count <= $page + 1; $count++) {
                $page_array[] = $count;
              }
              $page_array[] = '...';
              $page_array[] = $total_links;
            }
          }
        } else {
          for ($count = 1; $count <= $total_links; $count++) {
            $page_array[] = $count;
          }
        }

        if (isset($page_array) && count($page_array) >= 1) { // This (if statement) line might be useful on other projects where pagination has been used
          for ($count = 0; $count < count($page_array); $count++) {
            if ($page == $page_array[$count]) {
              $page_link .= '
                            <li class="page-item active">
                                <a class="page-link accreditedAccreditedVoter-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link accreditedAccreditedVoter-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
              } else {
                $previous_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Previous</a>
                            </li>
                            ';
              }
              $next_id = $page_array[$count] + 1;
              if ($next_id > $total_links) {
                $next_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Next</a>
                            </li>
                            ';
              } else {
                $next_link = '<li class="page-item"><a class="page-link accreditedAccreditedVoter-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link accreditedAccreditedVoter-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link accreditedAccreditedVoter-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
                            ';
              }
            }
          }
        }
        $output .= $previous_link . $page_link . $next_link;
        $start_result = ($page - 1) * $limit + 1;
        $end_result = min($start_result + $limit - 1, $total_data);

        echo $output; ?>
      </ul>
    </nav>
  </div>

  <div class="text-center mt-2"><?php echo  $start_result . ' - ' . $end_result . ' of ' . $total_data . ' Results'; ?></div>
<?php

  exit();
}
//LOAD ACCREDITED VOTERS TABLE FUNCTION ||||||Ends>>>>>>>

//VERIFY VOTERS EMAIL BEFORE ENTRY |||||| Starts >>>>>>>
if (isset($_POST['voterEmailVer'])) {

  $email = $_POST['email'];


  $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE email =?");
  $stmt->bind_param("s", $email);
  $stmt->execute() or die(mysqli_error($conn));
  $stmt->bind_result($getDuplicate);
  $stmt->fetch();
  $stmt->close();

  if ($getDuplicate > 0) {
    $response = array("status" => true);
  } else {
    $response = array("status" => false);
  }

  header("Content-Type: application/json");
  echo json_encode($response);
  exit();
}
//VERIFY VOTERS EMAIL BEFORE ENTRY |||||| Ends >>>>>>>

?>