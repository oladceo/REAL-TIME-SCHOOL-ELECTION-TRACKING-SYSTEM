<?php
session_start();
include("db_connect.php");

//GET OFFICE AND OFFICE CANDIDATE RESULTS |||||>>>Starts  
if (isset($_POST['getOfficeResults'], $_POST['officeID'])) {
  $officeID = $_POST["officeID"];
  $stmt = $conn->prepare("SELECT * FROM election_candidates WHERE office = ?");
  $stmt->bind_param("s", $officeID);
  $stmt->execute() or die(mysqli_error($conn));
  $result = $stmt->get_result();

  $colors = array(
    "#1ee0ac", "#ffc107", "#17a2b8", "#f64e60",
    "#6f42c1", "#007bff", "#28a745", "#dc3545",
    "#6610f2", "#fd7e14", "#20c997", "#ff5722",
    "#7952b3", "#2196f3", "#4caf50", "#e91e63"
  );
  $colorIndex = 0; // Initialize color index

?>
  <div class="card-content">
    <div class="card-body p-0">
      <ul class="list-group list-unstyled">
        <?php
        while ($getCandidate = $result->fetch_array()) {
          $color = $colors[$colorIndex];

          // Increment color index, and reset if it exceeds the array length
          $colorIndex++;
          if ($colorIndex >= count($colors)) {
            $colorIndex = 0;
          }

          $candidateID = $getCandidate['id'];
          $voteDate = "";

          // Get total valid votes for the candidate
          $stmt_votes = $conn->prepare("SELECT COUNT(*) FROM election_votes ev  INNER JOIN election_voters vv ON ev.vin = vv.vin AND ev.accreditationID = vv.accreditationID WHERE ev.candidateID = ? AND vv.vote_date !=?");
          $stmt_votes->bind_param("ss", $candidateID, $voteDate);
          $stmt_votes->execute() or die(mysqli_error($conn));
          $stmt_votes->bind_result($getCandidateVotes);
          $stmt_votes->fetch();
          $stmt_votes->close();

          // Get total valid votes for all candidates in the office
          $stmt_all_votes = $conn->prepare("SELECT COUNT(*)  FROM election_votes ev INNER JOIN election_voters vv  ON ev.vin = vv.vin AND ev.accreditationID = vv.accreditationID INNER JOIN election_candidates ec ON ev.candidateID = ec.id WHERE ec.office = ? AND vv.vote_date !=?");
          $stmt_all_votes->bind_param("ss", $officeID, $voteDate);
          $stmt_all_votes->execute() or die(mysqli_error($conn));
          $stmt_all_votes->bind_result($getAllVoteEntries);
          $stmt_all_votes->fetch();
          $stmt_all_votes->close();

          // Calculate vote percentage
          $percentage = ($getAllVoteEntries > 0) ? ($getCandidateVotes / $getAllVoteEntries) * 100 : 0;
        ?>
          <li class="p-4 border-bottom">
            <div class="w-100">
              <?php
              $passportPath = "resources/" . $getCandidate["passport"];
              $defaultImage = "../images/no-preview.jpeg";
              ?>
              <a href="javascript:void(0);">
                <img src="<?php echo (file_exists("../" . $passportPath) ? $passportPath : $defaultImage); ?>" alt="" class="img-fluid ml-0 mb-2  rounded-circle" width="50">
              </a>
              <div class="media-body align-self-center pl-2">
                <span class="mb-0 h6 font-w-900"><b> <?php echo strtoupper($getCandidate['sname'] . " " . $getCandidate['fname'] . " " . $getCandidate['oname']); ?></b> (<?php echo number_format($getCandidateVotes); ?>)</span><br>
              </div>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $percentage; ?>%; background-color: <?php echo $color; ?>" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"><b style="font-size: 18px;"><?php echo round($percentage); ?>%</b></div>
              </div>
            </div>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>

<?php
  exit();
}
//GET OFFICE AND OFFICE CANDIDATE RESULTS |||||>>>Ends

//GET LEADING CANDIDATES  TABLE |||||>>>Starts    
if (isset($_POST['getLeadingCandidate'])) {

  $voteDate = " ";
  $stmt = $conn->prepare("SELECT c.*, v.voteCount,o.name AS office FROM election_candidates c INNER JOIN (SELECT candidateID, COUNT(*) AS voteCount FROM election_votes ev INNER JOIN election_voters vv ON ev.vin = vv.vin AND ev.accreditationID = vv.accreditationID WHERE vv.vote_date !=? GROUP BY candidateID) v ON c.id = v.candidateID INNER JOIN election_offices o ON o.id = c.office INNER JOIN (SELECT MAX(voteCount) AS maxVoteCount, office FROM (SELECT COUNT(*) AS voteCount, candidateID, office FROM election_votes ev INNER JOIN election_candidates ec ON ev.candidateID = ec.id INNER JOIN election_voters vv ON ev.vin = vv.vin AND ev.accreditationID = vv.accreditationID WHERE vv.vote_date !=? GROUP BY candidateID, office) AS subquery GROUP BY office) AS maxVotes ON v.voteCount = maxVotes.maxVoteCount AND o.id = maxVotes.office ORDER BY o.sn");
  $stmt->bind_param("ss", $voteDate, $voteDate);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
?>
  <div class="card-content">
    <div class="card-body p-0">
      <ul class="list-group list-unstyled">
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $candidateName = $row['sname'] . ' ' . $row['fname'] . ' ' . $row['oname'];
            $votes = $row['voteCount'];
            $office = $row['office'];
            $imagePath = "resources/" . $row['passport'];
            $imagePath = ((file_exists("../" . $imagePath)) ? $imagePath  : "../images/no-preview.jpeg");
        ?>
            <li class="p-2 border-bottom zoom">
              <div class="media d-flex w-100">
                <a href="#"><img src="<?php echo $imagePath; ?>" alt="" class="img-fluid ml-0 mt-2 rounded-circle" width="50"></a>
                <div class="media-body align-self-center pl-2">
                  <span class="mb-0 h6 font-w-900"><?php echo $candidateName; ?></span><br>
                </div>
                <div class="media-body align-self-center pl-2">
                  <span class="mb-0 h6 font-w-900"><?php echo $votes; ?> Votes</span><br>
                </div>
                <div class="media-body align-self-center pl-2">
                  <span class="mb-0 h6 font-w-900"><?php echo $office; ?></span><br>
                </div>
              </div>
            </li>
          <?php
          }
        } else { ?>
          <div class="alert alert-danger">No Candidate FOund!</div>
        <?php
        }
        ?>
      </ul>
    </div>
  </div>
<?php
  $conn->close();
  exit();
}

//GET LEADING CANDIDATES  TABLE |||||>>>Ends 

//GET LIVE STATISTICS DATA TABLE |||||>>>Starts    
if (isset($_POST['getLiveStatisticData'])) { ?>

  <!-- Election Count Down -->
  <div class="d-flex">
    <div class="media-body align-self-center ">
      <span class="mb-0 h5 font-w-600"><img id="liveNotification" src="images/live.gif" style="width: 15px;height:15px;" /> Live Stats Reports </span><br>
      <!-- <p class="mb-0 font-w-500 tx-s-12">San Francisco, California, USA</p> -->
    </div>
    <div class="ml-auto p-2 text-dark font-w-800 h4 border-0 " id="countdown">
      <span class="mb-0">00:00:00</span>
    </div>
  </div>
  <!-- Election Count Down -->

  <div class="d-flex mt-4">
    <div class="border-0 outline-badge-info w-50 p-3 rounded text-center">
      <?php
      $vin = " ";
      $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin !=?");
      $stmt->bind_param("s", $vin);
      $stmt->execute();
      $stmt->bind_result($registeredVoters);
      $stmt->fetch();
      $stmt->close();
      ?>
      <span class="h2"><?php echo number_format($registeredVoters); ?></span><br />
      <span class="h4 mb-0">Total Registered Voters</span>
    </div>
    <div class="border-0 outline-badge-primary w-50 p-3 rounded ml-2 text-center">
      <?php
      $vin = " ";
      $accreditationID = " ";
      $vote_status = "active";

      $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin !=? AND accreditationID !=? AND vote_status=?");
      $stmt->bind_param("sss", $vin, $accreditationID, $vote_status);
      $stmt->execute() or die(mysqli_error($conn));
      $stmt->bind_result($accreditedVoters);
      $stmt->fetch();
      $stmt->close();
      ?>
      <span class="h3"><?php echo number_format($accreditedVoters); ?></span><br />
      <span class="h4 mb-0">Accredited Voters</span>
    </div>
  </div>

  <div class="d-flex mt-3">
    <div class="border-0 outline-badge-secondary w-50 p-3 rounded text-center">
      <?php
      $vin = " ";
      $voteDate = " ";
      $accreditationID = " ";
      $voteSessionID = " ";
      $voteDate = " ";

      //get voter vote status, if voted or not voted
      $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin !=? AND accreditationID !=? AND voteSessionID !=? AND vote_date !=?");
      $stmt->bind_param("ssss", $vin, $accreditationID, $voteSessionID, $voteDate);
      $stmt->execute() or die(mysqli_error($conn));
      $stmt->bind_result($voterCount);
      $stmt->fetch();
      $stmt->close();

      ?>
      <span class="h3"><?php echo number_format($voterCount); ?></span><br />
      <span class="h4 mb-0">Total Votes</span>
    </div>
    <div class="border-0 outline-badge-danger w-50 p-3 rounded ml-2 text-center">
      <span class="h3"><?php echo number_format($registeredVoters - $voterCount); ?></span><br />
      <span class="h4 mb-0">Yet to vote</span>
    </div>
  </div>

  <script>
    // Election Countdown Set Timeout >>>Starts
    <?php
    $sn = 1;
    $electionStatus = 1;
    $stmt = $conn->prepare("SELECT * FROM election_settings WHERE sn =? AND system_status=? ");
    $stmt->bind_param("ss",  $sn, $electionStatus);
    $stmt->execute() or die(mysqli_error($conn));
    $result = $stmt->get_result();
    $getSettings = $result->fetch_array();
    $stmt->close();

    if ($result->num_rows > 0) {
      $electionDeadline = $getSettings['deadline'];
    } else {
      $electionDeadline = date("d-m-Y h:i a");
    }
    $timeFormat = date("Y/m/d H:i:s", strtotime($electionDeadline)); // Format the date string in a way compatible with JavaScript Date constructor 

    ?>

    var targetDate = new Date("<?php echo $timeFormat; ?>");

    function updateCountdown() {
      const currentDate = new Date();
      const timeLeft = targetDate - currentDate;

      if (timeLeft <= 0) {
        document.getElementById('countdown').innerHTML = '<span class="text-danger h4"> Election Closed!</span>';
        closeElectionActivity(); // Trigger the close election Activity
      } else {
        const hours = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
        const minutes = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
        const seconds = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, '0');

        document.getElementById('countdown').innerHTML = `
            <span>${hours} :</span>
            <span>${minutes} :</span>
            <span>${seconds}</span>
        `;
      }
    }


    // Initial call to set the countdown on page load
    updateCountdown();

    // Call the updateCountdown function every second
    setInterval(updateCountdown, 1000);

    function closeElectionActivity() {
      $("#liveNotification").hide();
      //console.log("Election has been closed");
    }
    // Election Countdown Set Timeout >>>Ends
  </script>

<?php
  exit();
}
//GET LIVE STATISTICS DATA TABLE |||||>>>Ends 

//GET CANDIDATE LIVE RESULT TABLE FOR VOTERS DASHBOARD |||||>>>Starts
if (isset($_POST['getCandidateLiveResult'])) {
  // Fetch all candidates from all offices along with their vote counts
  $stmt = $conn->prepare("SELECT c.*,o.*, o.name AS officeName, COUNT(v.sn) AS voteCount FROM election_candidates c LEFT JOIN election_votes v ON c.id = v.candidateID LEFT JOIN election_offices o ON o.id = c.office GROUP BY c.id ORDER BY o.sn ASC");
  $stmt->execute() or die(mysqli_error($conn));
  $result = $stmt->get_result();


  $vin = $_SESSION['vin']; //Get Voter VIN in session here
  $accreditationID = " ";
  $voteSessionID = " ";
  $voteDate = " ";

  //get voter vote status, if voted or not voted
  $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin =? AND accreditationID !=? AND voteSessionID !=? AND vote_date !=?");
  $stmt->bind_param("ssss", $vin, $accreditationID, $voteSessionID, $voteDate);
  $stmt->execute();
  $stmt->bind_result($getVoteStatus);
  $stmt->fetch();
  $stmt->close();


  // Define an array of colors for borders
  $colors = array("primary", "warning", "danger", "primary", "secondary", "info", "dark");
  $colorIndex = 0; // Initialize color index
?>
  <div class="card-body">
    <?php
    while ($getCandidate = $result->fetch_array()) {
      // Get candidate information
      $candidateName = $getCandidate['sname'] . ' ' . $getCandidate['fname'] . ' ' . $getCandidate['oname'];
      $votes = $getCandidate['voteCount'];

      // Get candidate's passport path
      $passportPath = "resources/" . $getCandidate["passport"];
      $defaultImage = "../images/passports/user_pass.png";

      // Determine border color
      $color = $colors[$colorIndex];
      $colorIndex = ($colorIndex + 1) % count($colors);

      // Generate HTML for each candidate
    ?>
      <div class="card border-bottom-0 mt-3">
        <div class="card-content border-bottom border-<?php echo $color; ?> border-w-5">
          <div class="card-body p-4">
            <div class="d-flex">
              <img src="<?php echo (file_exists("../" . $passportPath) ? $passportPath : $defaultImage); ?>" alt="author" style="width:60px;height:60px; border-radius:50%">
              <div class="media-body col-6 align-self-center pl-3">
                <span class="mb-0 h4 font-w-600"><?php echo $candidateName; ?></span><br>
                <p class="mb-0 font-w-500 tx-s-12">Office of the <?php echo ucfirst($getCandidate["officeName"]); ?></p>
              </div>
              <?php if ($getVoteStatus > 0) { ?>
                <div class="media-body col-6 align-self-center">
                  <center class="mb-0 h5 font-w-900 text-center"><?php echo number_format($votes); ?> Votes</center><br>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
<?php
}
//GET CANDIDATE LIVE RESULT  TABLE FOR VOTERS DASHBOARD|||||>>>Ends 

//GET ADMIN DASHBOARD STATISTICS|||||>>>Starts
if (isset($_POST["getAdminDashboardStat"])) {
?>
  <div class="row">
    <div class="col-12 col-sm-6 col-xl-3 mt-3">
      <div class="card">
        <div class="card-body">
          <div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
            <i class="fa fa-users icons card-liner-icon mt-2"></i>
            <div class='card-liner-content'>
              <?php
              $vin = " ";
              $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin !=?");
              $stmt->bind_param("s", $vin);
              $stmt->execute();
              $stmt->bind_result($registeredVoters);
              $stmt->fetch();
              $stmt->close();
              ?>
              <h2 class="card-liner-title"><?php echo number_format($registeredVoters); ?></h2>
              <h6 class="card-liner-subtitle">Voters</h6>
            </div>
          </div>
          <!-- <div id="apex_today_order"></div> -->
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 mt-3">
      <div class="card">
        <div class="card-body">
          <div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
            <span class="fa fa-user-check card-liner-icon mt-1"></span>
            <div class='card-liner-content'>
              <?php
              $vin = " ";
              $accreditationID = " ";
              $vote_status = "active";

              $stmt = $conn->prepare("SELECT COUNT(*) FROM election_voters WHERE vin !=? AND accreditationID !=? AND vote_status=?");
              $stmt->bind_param("sss", $vin, $accreditationID, $vote_status);
              $stmt->execute() or die(mysqli_error($conn));
              $stmt->bind_result($accreditedVoters);
              $stmt->fetch();
              $stmt->close();
              ?>
              <h2 class="card-liner-title"><?php echo number_format($accreditedVoters); ?></h2>
              <h6 class="card-liner-subtitle">Accredited Voters</h6>
            </div>
          </div>
          <!-- <div id="apex_today_profit"></div> -->
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 mt-3">
      <div class="card">
        <div class="card-body">
          <div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
            <i class="fa fa-user-tie icons card-liner-icon mt-2"></i>
            <div class='card-liner-content'>
              <?php
              $id = " ";
              $office = " ";
              $status = "active";

              $stmt = $conn->prepare("SELECT COUNT(*) FROM election_candidates WHERE id !=? AND office !=? AND status=?");
              $stmt->bind_param("sss", $id, $office, $status);
              $stmt->execute() or die(mysqli_error($conn));
              $stmt->bind_result($candidates);
              $stmt->fetch();
              $stmt->close();
              ?>
              <h2 class="card-liner-title"><?php echo number_format($candidates); ?></h2>
              <h6 class="card-liner-subtitle">Candidates</h6>
            </div>
          </div>
          <span class="bg-primary card-liner-absolute-icon text-white card-liner-small-tip">+4.8%</span>
          <!-- <div id="apex_today_visitors"></div> -->
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 mt-3">
      <div class="card">
        <div class="card-body">
          <div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
            <i class="fas fa-network-wired card-liner-icon mt-2"></i>
            <div class='card-liner-content'>

              <?php
              $id = " ";
              $name = " ";
              $status = "active";

              $stmt = $conn->prepare("SELECT COUNT(*) FROM election_offices WHERE id !=? AND name !=? AND status=?");
              $stmt->bind_param("sss", $id, $name, $status);
              $stmt->execute() or die(mysqli_error($conn));
              $stmt->bind_result($offices);
              $stmt->fetch();
              $stmt->close();
              ?>
              <h2 class="card-liner-title"><?php echo number_format($offices); ?></h2>
              <h6 class="card-liner-subtitle">Offices/Positions</h6>
            </div>
          </div>
          <!-- <div id="apex_today_sale"></div> -->
        </div>
      </div>
    </div>
  </div>
<?php
  exit();
}
//GET ADMIN DASHBOARD STATISTICS|||||>>>Ends 

//GET ADMIN DASHBOARD RECENT ACCREDITED VOTERS|||||>>>Starts
if (isset($_POST["getAdminDashboardRecentAccredited"])) {
?>
  <!-- Recent Accredited Voters -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="card-title">Recent Accredited Voters</h6>
    </div>
    <div class="card-content">
      <div class="card-body p-0">
        <ul class="list-group list-unstyled">
          <?php
          $accreditationID = " ";
          $stmt = $conn->prepare("SELECT * FROM election_voters WHERE accreditationID !=? ORDER BY modified_date LIMIT 7 ");
          $stmt->bind_param("s", $accreditationID);
          $stmt->execute();
          $result = $stmt->get_result();
          $stmt->close();
          if ($result->num_rows > 0) {
            while ($getNewAccreditedVoters = $result->fetch_array()) {
              // Get candidate's passport path
              $passportPath = "resources/" . $getNewAccreditedVoters["passport"];
              $defaultImage = "../images/no-preview.jpeg";

          ?>
              <li class="p-2 border-bottom">
                <div class="media d-flex w-100">
                  <a href="#"><img src="<?php echo ((file_exists("../" . $passportPath)) ? $passportPath : $defaultImage); ?>" alt="" class="img-fluid ml-0 mt-2 " style="width:40px;border-radius:50%;"></a>
                  <div class="media-body align-self-center pl-2">
                    <span class="mb-0 font-w-600"><?php echo ucfirst($getNewAccreditedVoters['sname'] . " " . $getNewAccreditedVoters['fname']); ?></span><br>
                    <p class="mb-0 font-w-500 tx-s-12"><?php echo $getNewAccreditedVoters['email']; ?> </p>
                  </div>
                  <div class="ml-auto my-auto">
                    <span class="date"><?php echo date("d-m-Y, h:i a", strtotime($getNewAccreditedVoters['modified_date'])); ?></span>
                  </div>
                </div>
              </li>
            <?php
            }
          } else { ?>
            <div class="alert alert-warning m-3 text-center">
              <h4><i class="fas fa-user-slash"></i></h4><br />
              There are currently no recent accredited voters at the moment!
            </div>
          <?php
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
  <!-- Recent Accredited Voters -->
<?php
  exit();
}
//GET ADMIN DASHBOARD RECENT ACCREDITED VOTERS|||||>>>Ends 

?>