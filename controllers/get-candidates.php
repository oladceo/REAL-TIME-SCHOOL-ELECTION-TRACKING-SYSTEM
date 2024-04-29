<?php
session_start();
include("db_connect.php");

//LOAD CANDIDATES TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showCandidates']) && isset($_POST['query'])) {
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


  $whereSQL = " c.id != '' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " c.id != '' AND (c.id LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.sname LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.oname LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.gender LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.email LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.phone LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.address LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR o.name LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR c.status LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT o.name AS officeName, o.*,c.* FROM election_candidates c INNER JOIN election_offices o ON o.id = c.office  WHERE $whereSQL ";
  $query .= 'ORDER BY c.sname ';

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
          <th class="text-center">Last Name</th>
          <th class="text-center">First Name</th>
          <th class="text-center">Other Name(s)</th>
          <th class="text-center">Vying Office</th>
          <th class="text-center">Gender</th>
          <th class="text-center">Email</th>
          <th class="text-center">Phone</th>
          <th class="text-center">Votes</th>
          <th class="text-center">Status</th>
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
                          } ?>" alt="Candidate Image" width="50" class="rounded-circle  ml-auto" />
              </td>
              <td class="text-center"><?php echo ucfirst($result['sname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['fname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['oname']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['officeName']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['gender']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['email']); ?></td>
              <td class="text-center"><?php echo $result['phone']; ?></td>
              <td class="text-center"><?php number_format(0); ?></td>
              <td class="text-center"><?php echo ucfirst($result['status']); ?></td>
              <td class="text-center"><?php echo date("D. d-m-Y, h:i A"); ?></td>
              <td class="text-center">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#candidatesEditModal" onClick="loadCandidatesEditModal(this);" data-value="<?php echo $result['id']; ?>">
                  <span class="fas fa-bars"></span>
                </a>
              </td>
            </tr>

          <?php    }
        } else { ?>
          <tr class="table-danger">
            <td colspan="13" class="text-center">No Candidate was found!</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-center">#</th>
          <th class="text-center">Last Name</th>
          <th class="text-center">First Name</th>
          <th class="text-center">Other Name(s)</th>
          <th class="text-center">Vying Office</th>
          <th class="text-center">Gender</th>
          <th class="text-center">Email</th>
          <th class="text-center">Phone</th>
          <th class="text-center">Votes</th>
          <th class="text-center">Status</th>
          <th class="text-center">Registration Date</th>
          <th class="text-center">Options</th>
        </tr>
      </tfoot>
    </table>
    <!-- Modal Section for modify Candidate Starts -->
    <div class="modal fade bd-example-modal-xl" id="candidatesEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">

          <div class="modal-body" id="displayCandidatesInputs"></div>
        </div>
      </div>
    </div>
    <!-- Modal Section for modify Candidate Starts -->
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
                                <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link candidate-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
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
                $next_link = '<li class="page-item"><a class="page-link candidate-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link candidate-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link candidate-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
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
//LOAD CANDIDATES TABLE FUNCTION ||||||Ends>>>>>>>

//ADD NEW CANDIDATE FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['candidateOffice'], $_POST['candidateEmail'], $_POST['candidateSname'])) {

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
    $randomString = 'CD-V';
    for ($i = 0; $i < 5; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $candidateID = mysqli_real_escape_string($conn, $randomString);
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
      $randomString .= $characters[rand(
        0,
        $charactersLength - 1
      )];
    }
    $vin = mysqli_real_escape_string($conn, $randomString);
    ///////////////////////////////////////////////////////////////////////////

    date_default_timezone_set("Africa/Lagos"); // Corrected timezone capitalization
    $date = date("j-m-Y, g:i a");
    $candidateOffice = $_POST['candidateOffice'];
    $candidateStatus = $_POST['candidateStatus'];
    $candidateSname = $_POST['candidateSname'];
    $candidateFname = $_POST['candidateFname'];
    $candidateOname = $_POST['candidateOname'];
    $candidateGender = $_POST['candidateGender'];
    $candidateEmail = $_POST['candidateEmail'];
    $candidatePhone = $_POST['candidatePhone'];
    $candidateAddress = $_POST['candidateAddress'];
    $manifesto = $_POST['candidateManifesto'];
    $modifiedDate = " ";
    $voterStatus = "inactive";
    $biometrics = " ";
    $regAgent = $_SESSION['adminID'];
    $last_log = "nill";
    $onlineStatus = "offline";
    $accreditationID = "";
    $vote_date = "";
    $voteSessionID = "";


    // Check for candidate duplicates 
    $stmt = $conn->prepare("SELECT * FROM election_candidates WHERE sname = ? AND fname=?");
    $stmt->bind_param("ss", $candidateSname, $candidateFname);
    $result = $stmt->execute() ? $stmt->get_result() : false;
    $getDuplicate = $result ? $result->fetch_array() : false;

    if ($getDuplicate) {
      $status = false;
      $header = 'Duplicate Entry!';
      $message = 'Candidate Already Exist';
      $responseStatus = 'warning';
    } else {
      if (empty($_FILES['newCandidateImage']['tmp_name'])) {
        $status = false;
        $header = 'Empty Image!';
        $message = 'Candidate Image can not be empty';
        $responseStatus = 'warning';
      } else {
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
        $path = 'passport/'; // upload directory
        $directory = '../resources/passport/'; // upload directory

        $img = $_FILES['newCandidateImage']['name'];
        $tmp = $_FILES['newCandidateImage']['tmp_name'];

        // get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        // can upload same image using rand function
        $final_image = rand(1000, 1000000) . $img;

        // check's valid format
        if (in_array($ext, $valid_extensions)) {
          $path = $path . strtolower($final_image);
          $directory = $directory . strtolower($final_image);

          if (move_uploaded_file($tmp, $directory)) {

            // Insert new candidate
            $stmt = $conn->prepare("INSERT INTO election_candidates(`id`, `sname`, `fname`, `oname`, `gender`, `email`, `phone`, `address`, `passport`, `office`, `status`, `reg_date`, `modified_date`,`manifesto`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssssssssss", $candidateID, $candidateSname, $candidateFname, $candidateOname, $candidateGender, $candidateEmail, $candidatePhone, $candidateAddress, $path, $candidateOffice, $candidateStatus, $date, $modifiedDate, $manifesto);
            $result = $stmt->execute() or die($stmt->error);

            //Create candidate voter's account
            $stmt = $conn->prepare("INSERT INTO election_voters(`vin`, `sname`, `fname`, `oname`, `gender`, `email`, `phone`, `address`, `passport`,  `vote_status`, `reg_date`, `modified_date`,`biometric`,`reg_agent`, `last_log`, `online_status`, `accreditationID`,`vote_date`,`voteSessionID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssssssssssssss", $vin, $candidateSname, $candidateFname, $candidateOname, $candidateGender, $candidateEmail, $candidatePhone, $candidateAddress, $path, $voterStatus, $date, $modifiedDate, $biometrics, $regAgent, $last_log, $onlineStatus, $accreditationID, $vote_date, $voteSessionID);
            $result = $stmt->execute() or die(mysqli_error($conn));

            if ($result) {
              $status = true;
              $header = 'Successful!';
              $message = 'Candidate Added Successfully';
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
//ADD NEW CANDIDATE FUNCTION |||||||Ends>>>>>>>>>>

//LOAD CANDIDATE EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getCandidatesEdit']) && !empty($_POST['candidateID'])) {
  $candidateID = mysqli_real_escape_string($conn, $_POST['candidateID']);

  //get candidate information 
  $stmt = $conn->prepare("SELECT * FROM election_candidates WHERE id=?");
  $stmt->bind_param("s", $candidateID);
  $result = $stmt->execute() ? $stmt->get_result()  : false;
  $getCandidateData = $result ? $result->fetch_array() : false;


  if ($getCandidateData) { ?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10">Modify <?php echo ucfirst($getCandidateData['fname'] . " " . $getCandidateData['sname']); ?> Record</h5>
      <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
    </div>
    <form id="candidateEditForm">

      <div class="row mt-3">

        <!--Modify Candidate Inputs-->
        <div class="col-8 mt-3">
          <div class="card">
            <div class="card-content">
              <div class="card-body py-5">
                <div class="row">

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateOffice">Vying Office<span class="text-danger">*</span> <a href="offices" class="text-primary" style="float:right">+ Add New</a>
                        <input type="hidden" name="modifyCandidateID" value="<?php echo $getCandidateData['id']; ?>" />
                        <select class="form-control" id="modifyCandidateOffice" name="modifyCandidateOffice" required="">
                          <option value="">Select an Office</option>
                          <?php
                          $id = " ";
                          $status = "active";
                          $stmt = $conn->prepare("SELECT * FROM election_offices WHERE id !=? AND status=?");
                          $stmt->bind_param("ss", $id, $status);
                          $stmt->execute() or die(mysqli_error($conn));
                          $result = $stmt->get_result();
                          if ($result->num_rows > 0) {
                            while ($getOffice = $result->fetch_assoc()) { ?>
                              <option value="<?php echo $getOffice['id']; ?>" <?php echo (($getOffice['id'] == $getCandidateData['office']) ? 'selected' : ''); ?>><?php echo $getOffice['name']; ?></option>
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
                      <label class="col-12" for="modifyCandidateStatus">Status<span class="text-danger">*</span>
                        <select class="form-control" type="text" name="modifyCandidateStatus" id="modifyCandidateStatus" required="">
                          <option value="active" <?php echo (($getCandidateData['status'] == "active") ? 'selected' : ''); ?>>Active</option>
                          <option value="inactive" <?php echo (($getCandidateData['status'] == "inactive") ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateSname">Surname<span class="text-danger">*</span>
                        <input class="form-control" type="text" name="modifyCandidateSname" id="modifyCandidateSname" placeholder="Enter Candidate Surname" required="" value="<?php echo $getCandidateData['sname']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateFname">First Name<span class="text-danger">*</span>
                        <input class="form-control" type="text" name="modifyCandidateFname" id="modifyCandidateFname" placeholder="Enter Candidate First Name" required="" value="<?php echo $getCandidateData['fname']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateOname">Other Names
                        <input class="form-control" type="text" name="modifyCandidateOname" id="modifyCandidateOname" placeholder="Enter Candidate Other Names" value="<?php echo $getCandidateData['oname']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateGender">Gender<span class="text-danger">*</span>
                        <select class="form-control" type="text" name="modifyCandidateGender" id="modifyCandidateGender" required="">
                          <option value="male" <?php echo (($getCandidateData['gender'] == "male") ? 'selected' : ''); ?>>Male</option>
                          <option value="female" <?php echo (($getCandidateData['gender'] == "female") ? 'selected' : ''); ?>>Female</option>
                        </select>
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateEmail">Email Address<span class="text-danger">*</span>
                        <input class="form-control" type="email" name="modifyCandidateEmail" id="modifyCandidateEmail" placeholder="Enter Candidate Email Address" required="" value="<?php echo $getCandidateData['email']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-6">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidatePhone">Phone<span class="text-danger">*</span>
                        <input class="form-control" type="text" name="modifyCandidatePhone" id="modifyCandidatePhone" placeholder="Enter Candidate Pone Number" required="" value="<?php echo $getCandidateData['phone']; ?>" />
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-12">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateAddress">Candidate Resident Address<span class="text-danger">*</span>
                        <textarea class="form-control" placeholder="Enter Candidate Resident Address" name="modifyCandidateAddress" id="modifyCandidateAddress" required=""><?php echo $getCandidateData['address']; ?></textarea>
                      </label>
                    </div>
                  </div>

                  <div class="form-group col-sm-12">
                    <div class="input-group">
                      <label class="col-12" for="modifyCandidateManifesto">Candidate Manifesto
                        <textarea class="form-control" rows="5" maxlength="250" placeholder="Enter Candidate Manifesto" name="modifyCandidateManifesto" id="modifyCandidateManifesto"><?php echo $getCandidateData['manifesto']; ?></textarea>
                      </label>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Modify Candidate Inputs-->

        <!--Candidate Image-->
        <div class="col-4 mt-3">
          <div class="card">
            <div class="card-content">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Candidate Image Preview</h4>
              </div>
              <div class="card-body py-5">
                <center class="col-12" style="margin: 0px auto;">
                  <?php
                  $passportPath = "resources/" . $getCandidateData["passport"];
                  $defaultImage = "../images/no-preview.jpeg";
                  ?>
                  <img src="<?php if (file_exists("../" . $passportPath)) {
                              echo $passportPath;
                            } else {
                              echo $defaultImage;
                            } ?>" style="width:280px;height:280px" id="modifyCandidateImagePreview" />
                  <div>&nbsp;</div>
                  <label for="modifyCandidateImage" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Candidate Image<input id="modifyCandidateImage" name="modifyCandidateImage" type="file" onchange="previewImage(event);" />
                  </label>
                </center>

              </div>
            </div>
          </div>
        </div>
        <!--Candidate Image-->
      </div>
      <div class="modal-footer">
        <center style="margin: 0px auto;">
          <span id="modifyCandidateMsg"></span>
        </center>
        <button type="submit" class="btn btn-warning" id="updateCandidateBtn">Update Candidate</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>
    <script>
      $("#candidateEditForm").submit(function(e) {
        e.preventDefault();
        //console.log('welcome');
        var candidateForm = new FormData($("#candidateEditForm")[0]);
        swal({
            title: "Are you sure to update Candidate?",
            text: "Updating this candidate is effective across the portal.",
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
              url: 'controllers/get-candidates',
              async: true,
              processData: false,
              contentType: false,
              // mimeType: 'multipart/form-data',
              // cache: false,
              data: candidateForm,
              beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                $("#updateCandidateBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
              },
              success: function(response) {

                var status = response.status;
                var message = response.message;
                var responseStatus = response.responseStatus;
                var header = response.header;

                if (status === true) {
                  $("#modifyCandidateMsg").html(response).css("color", "green").show();
                  swal(header, message, responseStatus);
                  //loadCandidates(); //Reload registered candidates table
                } else {
                  swal(header, message, responseStatus);
                }
              },
              error: function() {
                $("#modifyCandidateMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
              },
              complete: function() { // Moved the timeout code to the complete callback
                setTimeout(function() {
                  $("#modifyCandidateMsg").fadeOut(300);
                }, 3000);
                $("#updateCandidateBtn").html("Update Candidate").show(); // Reset the button text
              }
            });
          });
      });
    </script>
<?php
  }

  exit();
}
//LOAD CANDIDATE EDIT MODAL |||||||| Ends >>>>>>>>>>

//UPDATE CANDIDATE FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['modifyCandidateOffice'], $_POST['modifyCandidateStatus'], $_POST['modifyCandidateEmail'])) {

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
    $modifyCandidateID = $_POST['modifyCandidateID'];
    $modifyCandidateOffice = $_POST['modifyCandidateOffice'];
    $modifyCandidateStatus = $_POST['modifyCandidateStatus'];
    $modifyCandidateSname = $_POST['modifyCandidateSname'];
    $modifyCandidateFname = $_POST['modifyCandidateFname'];
    $modifyCandidateOname = $_POST['modifyCandidateOname'];
    $modifyCandidateGender = $_POST['modifyCandidateGender'];
    $modifyCandidateEmail = $_POST['modifyCandidateEmail'];
    $modifyCandidatePhone = $_POST['modifyCandidatePhone'];
    $modifyCandidateAddress = $_POST['modifyCandidateAddress'];
    $modifyCandidateManifesto = $_POST['modifyCandidateManifesto'];

    // Check for candidate duplicates 
    $stmt = $conn->prepare("SELECT COUNT(*) FROM election_candidates WHERE id = ? AND sname = ? AND fname = ? AND office= ? AND status=? AND oname =? AND gender=? AND email =? AND phone= ? AND address =? AND manifesto=?");
    $stmt->bind_param("sssssssssss", $modifyCandidateID, $modifyCandidateSname, $modifyCandidateFname, $modifyCandidateOffice,  $modifyCandidateStatus, $modifyCandidateOname, $modifyCandidateGender, $modifyCandidateEmail, $modifyCandidatePhone, $modifyCandidateAddress, $modifyCandidateManifesto);
    $stmt->execute();
    $stmt->bind_result($duplicateCount);
    $stmt->fetch();
    $stmt->close();


    // echo $_FILES['modifyCandidateImage']['tmp_name'];
    // die();
    if ($duplicateCount > 0 && empty($_FILES['modifyCandidateImage']['tmp_name'])) {
      $status = false;
      $header = 'No Changes!';
      $message = 'There is noting to update';
      $responseStatus = 'warning';
    } else {

      if (!empty($_FILES['modifyCandidateImage']['tmp_name'])) { //if there is a new file to upload for candidate passport
        $getCandidateID = mysqli_real_escape_string($conn, $_POST['modifyCandidateID']);
        //get candidate existing image
        $queryCandidate = mysqli_query($conn, "SELECT * FROM election_candidates WHERE id ='$getCandidateID'") or die(mysqli_error($conn));
        $getCandidateData = mysqli_fetch_array($queryCandidate);

        //remove the existing image
        $unlinkPath = "../resources/" . $getCandidateData["passport"];
        if (file_exists($unlinkPath)) { //first check if file exists
          unlink($unlinkPath);
        }

        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
        $path = 'passport/'; // upload directory
        $directory = '../resources/passport/'; // upload directory

        $img = $_FILES['modifyCandidateImage']['name'];
        $tmp = $_FILES['modifyCandidateImage']['tmp_name'];

        // get uploaded file's extension            
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        // can upload same image using rand function
        $final_image = rand(1000, 1000000) . $img;

        // check's valid format
        if (in_array($ext, $valid_extensions)) {
          $path = $path . strtolower($final_image);
          $directory = $directory . strtolower($final_image);

          if (move_uploaded_file($tmp, $directory)) {
            // Updated Registered candidate with new passport
            $stmt = $conn->prepare("UPDATE election_candidates SET sname = ?, fname = ?, oname = ?, gender = ?, email = ?, phone = ?, address = ?, passport = ?, office = ?, status = ?, modified_date = ?, manifesto=? WHERE id = ?");
            $stmt->bind_param("sssssssssssss", $modifyCandidateSname, $modifyCandidateFname, $modifyCandidateOname, $modifyCandidateGender, $modifyCandidateEmail, $modifyCandidatePhone, $modifyCandidateAddress, $path, $modifyCandidateOffice, $modifyCandidateStatus, $date, $modifyCandidateManifesto, $modifyCandidateID);
            $queryUpdateCandidate = $stmt->execute() or die(mysqli_error($conn));

            if ($queryUpdateCandidate) {

              $status = true;
              $header = 'Successful!';
              $message = 'Candidate Updated Successfully';
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
        // Updated Registered candidate
        $stmt = $conn->prepare("UPDATE election_candidates SET sname = ?, fname = ?, oname = ?, gender = ?, email = ?, phone = ?, address = ?, office = ?, status = ?, modified_date = ?, manifesto=? WHERE id = ?");
        $stmt->bind_param("ssssssssssss", $modifyCandidateSname, $modifyCandidateFname, $modifyCandidateOname, $modifyCandidateGender, $modifyCandidateEmail, $modifyCandidatePhone, $modifyCandidateAddress, $modifyCandidateOffice, $modifyCandidateStatus, $date, $modifyCandidateManifesto, $modifyCandidateID);
        $queryUpdateCandidate = $stmt->execute() or die(mysqli_error($conn));

        if ($queryUpdateCandidate) {

          $status = true;
          $header = 'Successful!';
          $message = 'Candidate Updated Successfully';
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
//UPDATE CANDIDATE FUNCTION |||||||Ends>>>>>>>>>>

//VERIFY CANDIDATES EMAIL BEFORE ENTRY |||||| Starts >>>>>>>
if (isset($_POST['candidateEmailVer'])) {

  $email = $_POST['email'];


  $stmt = $conn->prepare("SELECT COUNT(*) FROM election_candidates WHERE email =?");
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
//VERIFY CANDIDATES EMAIL BEFORE ENTRY |||||| Ends >>>>>>>

?>