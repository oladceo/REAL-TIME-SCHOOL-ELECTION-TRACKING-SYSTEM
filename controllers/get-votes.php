<?php
session_start();
include("db_connect.php");

//FUNCTION TO PROCESS VOTE START ||||| Starts>>>>>
if (isset($_POST['enableStartVoteSession'])) {

  // Check if the election is activated and deadline has not exceeded
  $stmt = $conn->prepare("SELECT * FROM election_settings");
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getSettings = $result->fetch_array();
  $stmt->close();

  // Calculate deadline from today
  $today = new DateTime();
  $deadline = DateTime::createFromFormat('d-m-Y, h:i a', $getSettings["deadline"]);

  if ($deadline !== false) {
    // Check if the deadline is in the future
    if ($today < $deadline) {
      // Calculate the time difference
      $dateDiff = $today->diff($deadline);
      $totalHoursRemaining = $dateDiff->h + ($dateDiff->days * 24);
    } else {
      // The deadline has passed
      $totalHoursRemaining = 0;
    }
  } else {
    // Handle invalid deadline format
    echo "Invalid deadline format!";
    exit(); // Exit the script
  }

  // Check election status and time remaining
  if ($totalHoursRemaining == 0) {
    $status = true;
    $message = "Election Has Ended And Presently Closed!";
    $header = "Election Closed!";
    $responseStatus = 'warning';
    $btnText = "<i class='fas fa-vote-yea'></i> Start Vote";
    $voteSessionStatus = false;
  } elseif ($getSettings['system_status'] != 1) {
    $status = false;
    $message = "Election is currently Disabled!";
    $header = "System on hold!";
    $responseStatus = 'error';
    $btnText = "<i class='fas fa-vote-yea'></i> Start Vote";
    $voteSessionStatus = false;
  } else {
    // Check if voters have already voted
    $voteDate = " "; // Assuming this is an empty string

    $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin=? AND accreditationID=? AND vote_date=?");
    $stmt->bind_param("sss", $_SESSION['vin'], $_SESSION['accreditationID'], $voteDate);
    $stmt->execute();
    $stmt->bind_result($getCount);
    $stmt->fetch();
    $stmt->close();

    if ($getCount > 0) {
      // If voter has not yet submitted vote, prepare a new vote session ID
      //====================================================================
      $characters = '1234567890';
      $characters .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < 50; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      $voteSessionID = $randomString;
      //====================================================================


      // Update Voters voteSessionID
      $voteStatus = "active";
      $stmt = $conn->prepare("UPDATE election_voters SET voteSessionID =? WHERE vin=? AND accreditationID=? AND vote_date=? AND vote_status=?");
      $stmt->bind_param("sssss", $voteSessionID, $_SESSION['vin'], $_SESSION['accreditationID'], $voteDate, $voteStatus);
      $updateQuery = $stmt->execute();
      $stmt->close();

      if ($updateQuery) {
        $_SESSION["voteSessionID"] = $voteSessionID;
        $status = true;
        $header = 'Validated';
        $message = 'Preparing your vote session,redirecting shortly...';
        $responseStatus = 'success';
        $btnText = "<i class='fa fa-spin fa-spinner'></i> Preparing vote session...";
        $voteSessionStatus = true;
      } else {
        $status = false;
        $header = 'Failed!';
        $message = 'An error occurred, try again';
        $responseStatus = 'error';
        $btnText = "<i class='fas fa-vote-yea'></i> Start Vote";
        $voteSessionStatus = false;
      }
    } else {
      $status = true;
      $message = "You have already casted your votes";
      $header = "Congratulations!";
      $responseStatus = 'success';
      $btnText = "<i class='fas fa-check'></i> Voted";
      $voteSessionStatus = false;
    }
  }

  // Prepare response
  $response = array(
    'status' => $status,
    'message' => $message,
    'header' => $header,
    'responseStatus' => $responseStatus,
    'btnText' => $btnText,
    'voteSessionStatus' => $voteSessionStatus
  );

  // Send JSON response
  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//FUNCTION TO PROCESS VOTE START ||||| Ends>>>>>

//LOAD CANDIDATES FOR VOTER ELECTION PANEL FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showCandidates'])) {
  include("db_connect.php");

  //	echo "Great a table"; die();
  $limit = 1; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page'] >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }


  $whereSQL = " eo.id !=' ' ";


  // Fetch records based on the query
  $query = "SELECT eo.*, COUNT(ec.id) as candidateCount, eo.name AS officeName FROM election_offices eo LEFT JOIN election_candidates ec ON eo.id = ec.office WHERE eo.id != '' AND eo.status = 'active' GROUP BY eo.id HAVING candidateCount > 0 ";
  $query .= ' ORDER BY eo.sn ';

  $filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . ' ';

  $statement = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $total_data = mysqli_num_rows($statement);

  $statement = mysqli_query($conn, $filter_query);
  $total_filter_data = mysqli_num_rows($statement);

  $output = ''; // Initialize output to accept HTML content


?>
  <div class="panel-body col-md-12">
    <?php
    //Start preparing the pagination 
    $total_links = ceil($total_data / $limit);
    $previous_link = '';
    $next_link = '';
    $page_link = '';
    $start_result = ($page - 1) * $limit + 1;
    //Start preparing the pagination 

    if ($total_data > 0) {
      $sn = 0;
      while ($result = mysqli_fetch_array($statement)) {
        $sn++;
        $stmt = $conn->prepare("SELECT * FROM election_candidates WHERE office =?");
        $stmt->bind_param("s", $result['id']);
        $stmt->execute() or die(mysqli_error($conn));
        $getResult = $stmt->get_result();

    ?>
        <div class="row">
          <div class="col-12 col-lg-8 ">
            <div class="card rounded-4 col-12 col-md-4 col-lg-12">
              <div class="card-header">
                <h3 class="card-title">Office Of The <?php echo ucfirst($result['officeName']); ?></h3>
              </div>
              <div class="card-body" style="min-height:200px">
                <div class='d-flex px-0 px-lg-2 py-2' style="font-size:15px;min-height:25rem">

                  <form id="submitResponseForm" class="py-2 mt-3" style="font-size:15px;cursor:pointer">
                    <div class="row">
                      <?php
                      $numCandidate = 0;

                      // Define an array of colors for borders
                      $colors = array("primary", "warning", "danger", "info", "secondary", "dark", "success");
                      $colorIndex = 0; // Initialize color index

                      while ($getCandidate = $getResult->fetch_array()) {
                        $numCandidate++;

                        // Get candidate's passport path
                        $passportPath = "resources/" . $getCandidate["passport"];
                        $defaultImage = "../images/passports/user_pass.png";

                        // Determine border color
                        $color = $colors[$colorIndex];
                        $colorIndex = ($colorIndex + 1) % count($colors);

                        //Get Voters Candidates for vote Entry table
                        $voteSessionID = " ";
                        $stmt = $conn->prepare("SELECT * FROM election_votes WHERE vin=? AND accreditationID=? AND candidateOffice=? AND voteSessionID !=?");
                        $stmt->bind_param("ssss", $_SESSION['vin'], $_SESSION['accreditationID'], $result['id'], $voteSessionID);
                        $stmt->execute() or die(mysqli_error($conn));
                        $voteResult = $stmt->get_result();
                        $getVotersCandidate = $voteResult->fetch_array();

                      ?>
                        <div class="col-lg-6 col-md-6 mb-3">
                          <div class="custom-control iradio_square">
                            <input type="radio" class="custom-control-input" id="voteResponse<?php echo $numCandidate; ?>" name="voteResponse" value="<?php echo $getCandidate['id']; ?>" <?php echo (($getVotersCandidate && $getVotersCandidate['candidateID'] == $getCandidate['id']) ? 'checked' : 'unchecked'); ?> onClick="saveVoteEntry(this);" data-candidate-office="<?php echo $getCandidate['office']; ?>">
                            <label class="custom-control-label" for="voteResponse<?php echo $numCandidate; ?>" style="cursor:pointer;display:block">
                              <div class="card-content border-bottom border-<?php echo $color; ?> border-w-5">
                                <div class="card-body p-4">
                                  <div class="d-flex">
                                    <img src="<?php echo (file_exists("../" . $passportPath) ? $passportPath : $defaultImage); ?>" alt="author" style="width:60px;height:60px; border-radius:50%">
                                    <div class="media-body align-self-center pl-3">
                                      <span class="mb-0 h4 font-w-600"><?php echo $getCandidate['sname'] . " " . $getCandidate['fname'] . " " . $getCandidate['oname']; ?></span><br>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </label>
                          </div>
                        </div>
                      <?php
                      }
                      $stmt->close();
                      ?>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <nav class="mt-4">
              <ul class="pagination" style="justify-content:center">
                <?php
                $previous_id = $page - 1;
                if ($previous_id > 0) {
                  $previous_link = '
                    <li class="page-item">
                        <button class="page-link px-3 mr-2 text-danger" id="prevBtn" data-page_number="' . $previous_id . '" >
                            <i class="fas fa-angle-double-left"></i> Previous Office 
                        </button>
                    </li>';
                } else {
                  $previous_link = '
                    <li class="page-item disabled">
                    </li>';
                  // <a href="javascript:void(0)" class="page-link px-3 mr-2 text-danger"><i class="fas fa-angle-double-left"></i> Previous</a>
                }

                $next_id = $page + 1;
                if ($next_id <= $total_links) {
                  $next_link = '
                    <li class="page-item">
                        <button class="page-link px-3 ml-2 text-primary" id="nextBtn"  data-page_number="' . $next_id . '" ">
                                        Next Office <i class="fas fa-angle-double-right"></i>
                        </button>
                    </li>';
                } else {
                  $next_link = '
                    <button  type="button" id="examSubmitBtn" onclick="submitVotes();" class="btn btn-primary"> Submit Vote<i class="fas fa-clipboard-check"></i></button>';
                }
                ?>

                <ul class="pagination">
                  <?php echo $previous_link; ?>
                  <?php echo $next_link; ?>
                </ul>

              </ul>
            </nav>

          </div>

          <div class="col-12 col-lg-4 mt-3">
            <div class="p-2">
              <div class="d-flex">
                <div class="media-body align-self-center ">
                  <span class="mb-0 h5 font-w-600">Election Panel</span><br>
                  <p class="mb-0 font-w-500 tx-s-12">Candidates you voted for appears here!</p>
                </div>
                <div class="ml-auto border-0 outline-badge-primary circle-50"><span class="fas fa-fingerprint h3 mb-0"></span></div>
              </div>
              <?php

              //Get Voters Selected Candidates for vote Entry
              $voteSessionID = " ";
              $stmt = $conn->prepare("SELECT * FROM election_votes v LEFT JOIN election_candidates c ON c.id = v.candidateID AND c.office =v.candidateOffice LEFT JOIN election_offices o ON o.id = c.office WHERE v.vin=? AND v.accreditationID=? AND v.voteSessionID !=?");
              $stmt->bind_param("sss", $_SESSION['vin'], $_SESSION['accreditationID'], $voteSessionID);
              $stmt->execute() or die(mysqli_error($conn));
              $votersCandidateResult = $stmt->get_result();


              // Define an array of colors for borders
              $colors = array("primary", "warning", "danger", "primary", "secondary", "info", "dark");
              $colorIndex = 0; // Initialize color index
              if ($votersCandidateResult->num_rows > 0) {
                while ($getVotersCandidate = $votersCandidateResult->fetch_array()) {

                  // Get candidate information
                  $candidateName = $getVotersCandidate['sname'] . ' ' . $getVotersCandidate['fname'] . ' ' . $getVotersCandidate['oname'];

                  // Get candidate's passport path
                  $passportPath = "resources/" . $getVotersCandidate["passport"];
                  $defaultImage = "../images/passports/user_pass.png";

                  // Determine border color
                  $color = $colors[$colorIndex];
                  $colorIndex = ($colorIndex + 1) % count($colors);

                  // Generate HTML for each candidate
              ?>
                  <div class="col-lg-12 col-md-12 mb-3">
                    <div class="card border-bottom-0 mt-3">
                      <div class="card-content border-bottom border-<?php echo $color; ?> border-w-5">
                        <div class="card-body p-4">
                          <div class="d-flex">
                            <img src="<?php echo (file_exists("../" . $passportPath) ? $passportPath : $defaultImage); ?>" alt="author" style="width:60px;height:60px; border-radius:50%">
                            <div class="media-body col-12 align-self-center pl-3">
                              <span class="mb-0 h4 font-w-600"><?php echo $candidateName; ?></span><br>
                              <p class="mb-0 font-w-500 tx-s-12">Office of the <?php echo ucfirst($getVotersCandidate["name"]); ?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php
                }
              } else { //if not selection by voters yet 
                ?>

                <div class="d-flex">
                  <div class="alert alert-warning text-center col-12 m-4">
                    <h4 class="fas fa-user-alt-slash"> </h4><br />
                    You have not selected any candidate yet!
                  </div>
                </div>
              <?php }

              $stmt->close();
              ?>
            </div>
          </div>
        </div>

        <script>
          //Function to save vote response on response check
          function saveVoteEntry(entryChecked) {
            var entry = $(entryChecked).val();
            var office = $(entryChecked).data("candidate-office");
            console.log(entry);
            $.ajax({
              type: "POST",
              url: "controllers/get-votes",
              async: false,
              data: {
                saveVoteEntryFunction: true,
                entry: entry,
                office: office
              },
              success: function(sEntry) {
                loadCandidates(<?php echo $page; ?>);

              },
              error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Error!", "Error in Connectivity, Please try again later!", "error");
              }

            });
          }

          //Function to Submit All Votes
          function submitVotes() {
            var selectedCandidates = $('input[name="voteResponse"]:checked').length;
            if (selectedCandidates == 0) {
              swal("Invalid Submission", "You can only submit vote after selecting a candidate from at least one of the offices!", "warning")

            } else {
              swal({
                  title: "Are you sure to submit vote?",
                  text: "Clicking on continue will submit the selected candidates on the right as your casted vote!",
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
                    type: "POST",
                    url: "controllers/get-votes",
                    async: true,
                    data: {
                      voteSubmission: 1
                    },
                    beforeSend: function(sVotes) {
                      $("#voteSubmitBtn").html("<span style='font-size:13px'><i class='fa fa-spinner fa-spin  '></i> Submitting...</span>").show();
                      $("#voteSubmitBtn").prop("disabled", true);
                    },
                    success: function(sVotes) {
                      $("#displayCandidates").html(sVotes).show();
                    },
                    error: function(sVotes) {
                      swal("Error!", "Error in Connectivity, Please try again later!", "error");
                    }

                  });
                });
            }
          }
        </script>

      <?php

      }
    } else { ?>
      <div class="alert alert-danger text-center">There are no available offices for election</div>
    <?php
    }
    ?>
  </div>
  <div class="text-center mt-2"><?php echo $start_result . ' of ' . $total_data . ' Results'; ?></div>

  <?php
  exit();
}
//LOAD CANDIDATES FOR VOTER ELECTION PANEL FUNCTION ||||||Ends>>>>>>>

//SAVE VOTER VOTES ENTRY ||||| Starts>>>>
if (isset($_POST['saveVoteEntryFunction'])) {

  date_default_timezone_set("Africa/Lagos"); // Corrected timezone capitalization
  $date = date("j-m-Y, g:i a");
  $entry = $_POST['entry']; //candidate ID
  $office = $_POST['office']; //candidate office

  $stmt = $conn->prepare("SELECT * FROM election_votes WHERE vin=? AND accreditationID=? AND candidateOffice=?");
  $stmt->bind_param("sss", $_SESSION['vin'], $_SESSION['accreditationID'], $office);
  $stmt->execute();
  $result = $stmt->get_result();
  $getVotersEntry = $result->fetch_array();
  $stmt->close();
  if ($getVotersEntry && $getVotersEntry > 0) {
    //update the vote entry
    $stmt = $conn->prepare("UPDATE election_votes SET candidateID =?, voteSessionID=?, vote_date=? WHERE vin=? AND accreditationID=? AND candidateOffice=?");
    $stmt->bind_param("ssssss", $entry, $_SESSION['voteSessionID'], $date, $_SESSION['vin'], $_SESSION['accreditationID'], $office);
    $stmt->execute() or die(mysqli_error($conn));
    $stmt->close();
  } else {
    //insert new vote entry 
    $stmt = $conn->prepare("INSERT INTO election_votes(`vin`, `candidateID`, `candidateOffice`, `accreditationID`, `voteSessionID`, `vote_date`) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $_SESSION['vin'], $entry, $office, $_SESSION['accreditationID'], $_SESSION['voteSessionID'], $date);
    $stmt->execute() or die(mysqli_error($conn));
    $stmt->close();
  }

  exit();
}
//SAVE VOTER VOTES ENTRY ||||| Ends>>>>

//SUBMIT VOTERS VOTE ENTRY ||||| Starts>>>>
if (isset($_POST['voteSubmission'])) {

  date_default_timezone_set("Africa/Lagos"); // Corrected timezone capitalization
  $date = date("j-m-Y, g:i a");
  //update voters table to submit votes
  $stmt = $conn->prepare("UPDATE election_voters SET vote_date =?, voteSessionID=? WHERE vin=? AND accreditationID=?");
  $stmt->bind_param("ssss", $date, $_SESSION['voteSessionID'], $_SESSION['vin'], $_SESSION['accreditationID']);
  $queryVoteSubmit = $stmt->execute() or die(mysqli_error($conn));
  $stmt->close();
  if ($queryVoteSubmit) { ?>
    <script>
      swal("Submitted!", "Your vote has been submitted! Redirecting...", "success");
      setTimeout(function() {
        window.location.href = "./dashboard";
      }, 3000);
    </script>
<?php
  }

  exit();
}
//SUBMIT VOTERS VOTE ENTRY ||||| Ends>>>>

//GET VOTES RESULT BASED ON OFFICES AND CREATE A CHART |||||| Starts>>>>>

if (isset($_POST['get_chart_result'])) {

  // Fetch election offices
  $officeQuery = "SELECT id, name FROM election_offices";
  $officeResult = mysqli_query($conn, $officeQuery);
  $electionOffices = mysqli_fetch_all($officeResult, MYSQLI_ASSOC);

  // Constructing data for each office
  $officeData = [];

  // Iterate over each office
  foreach ($electionOffices as $office) {
    $officeId = $office['id'];

    // Fetch candidates for the current office
    $stmt = $conn->prepare("SELECT id, sname, fname FROM election_candidates WHERE office = ?");
    $stmt->bind_param("s", $officeId);
    $stmt->execute();
    $candidateResult = $stmt->get_result();
    $electionCandidates = $candidateResult->fetch_all(MYSQLI_ASSOC);

    // Fetch votes for the current office
    $stmt = $conn->prepare("SELECT candidateID FROM election_votes WHERE candidateOffice = ?");
    $stmt->bind_param("s", $officeId);
    $stmt->execute();
    $voteResult = $stmt->get_result();
    $electionVotes = [];
    while ($row = $voteResult->fetch_assoc()) {
      $electionVotes[] = $row['candidateID'];
    }

    // If there are no candidates or votes for the current office, skip it
    if (
      empty($electionCandidates) || empty($electionVotes)
    ) {
      continue;
    }

    // Constructing data for Morris chart for the current office
    $candidatesData = [];
    foreach ($electionCandidates as $candidate) {
      $candidateId = $candidate['id'];
      // Extract the first character of the first name as the initial
      $initial = substr($candidate['sname'], 0, 1);
      // Construct the abbreviated name
      $abbreviatedName = $initial . '. ' . $candidate['fname'];
      $votesForCandidate = array_count_values($electionVotes)[$candidateId] ?? 0;
      $candidatesData[] = ['name' => $abbreviatedName, 'votes' => $votesForCandidate];
    }

    // Add office data to the result array
    $officeData[] = ['office' => $office['name'], 'candidates' => $candidatesData];
  }

  // Set the content type to JSON
  header('Content-Type: application/json');

  // If there is no data available for any office, return a message
  if (empty($officeData)) {
    echo json_encode(['message' => 'No data available for any office']);
  } else {
    // Return office data as JSON
    echo json_encode($officeData);
  }
  exit();
}
//GET VOTES RESULT BASED ON OFFICES AND CREATE A CHART |||||| Ends>>>>>

?>