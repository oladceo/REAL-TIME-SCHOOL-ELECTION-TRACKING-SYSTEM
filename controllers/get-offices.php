<?php
session_start();
include("db_connect.php");

//LOAD OFFICES TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showOffices']) && isset($_POST['query'])) {
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


    $whereSQL = " id != '' ";


    if ($_POST['query'] != '') { //to work on this for role access later
        $keyword = mysqli_real_escape_string($conn, $_POST['query']);

        $whereSQL = " id != '' AND (id LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR name LIKE '%" . str_replace(' ', '%', $keyword) . "%'  OR abbr LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR status LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
    }

    // Fetch records based on the query
    $query = "SELECT * FROM election_offices  WHERE $whereSQL ";
    $query .= 'ORDER BY name ';

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
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Abbreviation</th>
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
                            <td class="text-center"><?php echo strtoupper($result['id']); ?></td>
                            <td class="text-center"><?php echo ucfirst($result['name']); ?></td>
                            <td class="text-center"><?php echo ucfirst($result['abbr']); ?></td>
                            <td class="text-center"><?php echo ucfirst($result['status']); ?></td>
                            <td class="text-center"><?php echo date("D. d-m-Y, h:i A"); ?></td>
                            <td class="text-center">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#officesEditModal" onClick="loadOfficesEditModal(this);" data-value="<?php echo $result['id']; ?>">
                                    <span class="fas fa-bars"></span>
                                </a>
                            </td>
                        </tr>

                    <?php    }
                } else { ?>
                    <tr class="table-danger">
                        <td colspan="13" class="text-center">No Office was found!</td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Abbreviation</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Registration Date</th>
                    <th class="text-center">Options</th>
                </tr>
            </tfoot>
        </table>
        <!-- Modal Section for modify Office Starts -->
        <div class="modal fade bd-example-modal-lg" id="officesEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-body" id="displayOfficesInputs"></div>
                </div>
            </div>
        </div>
        <!-- Modal Section for modify Office Starts -->
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
                                <a class="page-link office-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
                            $previous_id = $page_array[$count] - 1;
                            if ($previous_id > 0) {
                                $previous_link = '<li class="page-item"><a class="page-link office-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
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
                                $next_link = '<li class="page-item"><a class="page-link office-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
                            }
                        } else {
                            if ($page_array[$count] == '...') {
                                $page_link .= '
                                <li class="page-link office-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
                            } else {
                                $page_link .= '
                                <li class="page-item"><a class="page-link office-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
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
//LOAD OFFICES TABLE FUNCTION ||||||Ends>>>>>>>

//ADD NEW OFFICE FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['officeName'], $_POST['officeAbbr'], $_POST['officeStatus'])) {


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
        $randomString = 'SOF-';
        for ($i = 0; $i < 3; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $id = mysqli_real_escape_string($conn, $randomString);
        ///////////////////////////////////////////////////////////////////////////


        date_default_timezone_set("Africa/Lagos"); // Corrected timezone capitalization
        $date = date("j-m-Y, g:i a");
        $officeName = $_POST['officeName'];
        $officeAbbr = $_POST['officeAbbr'];
        $officeStatus = $_POST['officeStatus'];

        // Check for office duplicates 
        $stmt = $conn->prepare("SELECT * FROM election_offices WHERE name = ?");
        $stmt->bind_param("s", $officeNname);
        $result = $stmt->execute() ? $stmt->get_result() : false;
        $getDuplicate = $result ? $result->fetch_array() : false;

        if ($getDuplicate) {
            $status = false;
            $header = 'Duplicate Entry!';
            $message = 'Office Already Exist';
            $responseStatus = 'warning';
        } else {
            // Insert new office
            $stmt = $conn->prepare("INSERT INTO election_offices(`id`, `name`, `abbr`, `status`,`reg_date`) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $id, $officeName, $officeAbbr, $officeStatus,  $date);
            $result = $stmt->execute() or die(mysqli_error($conn));

            if ($result) {
                $status = true;
                $header = 'Successful!';
                $message = 'Office Added Successfully';
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

    $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

    header('Content-Type: application/json');
    echo json_encode($response);


    exit();
}
//ADD NEW OFFICE FUNCTION |||||||Ends>>>>>>>>>>

//LOAD OFFICE EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getOfficesEdit']) && !empty($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    //get office information 
    $stmt = $conn->prepare("SELECT * FROM election_offices WHERE id=?");
    $stmt->bind_param("s", $id);
    $result = $stmt->execute() ? $stmt->get_result()  : false;
    $getOfficeData = $result ? $result->fetch_array() : false;


    if ($getOfficeData) { ?>
        <div class="modal-header">
            <h5 class="modal-title" id="myLargeModalLabel10">Modify <?php echo ucfirst($getOfficeData['name']); ?> Record</h5>
            <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
        </div>
        <form id="officeEditForm">

            <div class="row mt-3">

                <!--Modify Office Inputs-->
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body py-5">
                                <div class="row">

                                    <div class="form-group col-sm-6">
                                        <div class="input-group">
                                            <input type="hidden" name="modifyOfficeId" value="<?php echo $getOfficeData['id']; ?>" />
                                            <label class="col-12" for="modifyOfficeStatus">Status
                                                <select class="form-control" type="text" name="modifyOfficeStatus" id="modifyOfficeStatus" required="">
                                                    <option value="active" <?php echo (($getOfficeData['status'] == "active") ? 'selected' : ''); ?>>Active</option>
                                                    <option value="inactive" <?php echo (($getOfficeData['status'] == "inactive") ? 'selected' : ''); ?>>Inactive</option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <div class="input-group">
                                            <label class="col-12" for="modifyOfficeName">Name
                                                <input class="form-control" type="text" name="modifyOfficeName" id="modifyOfficeName" placeholder="Enter Office Abbreviation" required="" value="<?php echo $getOfficeData['name']; ?>" />
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <div class="input-group">
                                            <label class="col-12" for="modifyOfficeAbbr">Abbreviation
                                                <input class="form-control" type="text" name="modifyOfficeAbbr" id="modifyOfficeAbbr" placeholder="Enter Office Abbreviation" required="" value="<?php echo $getOfficeData['abbr']; ?>" />
                                            </label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Modify Office Inputs-->

            </div>
            <div class="modal-footer">
                <center style="margin: 0px auto;">
                    <span id="modifyOfficeMsg"></span>
                </center>
                <button type="submit" class="btn btn-warning" id="updateOfficeBtn">Update Office</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>
        <script>
            $("#officeEditForm").submit(function(e) {
                e.preventDefault();
                //console.log('welcome');
                var officeForm = new FormData($("#officeEditForm")[0]);
                swal({
                        title: "Are you sure to update Office?",
                        text: "Updating this office is effective across the portal.",
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
                            url: 'controllers/get-offices',
                            async: true,
                            processData: false,
                            contentType: false,
                            // mimeType: 'multipart/form-data',
                            // cache: false,
                            data: officeForm,
                            beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                                $("#updateOfficeBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
                            },
                            success: function(response) {

                                var status = response.status;
                                var message = response.message;
                                var responseStatus = response.responseStatus;
                                var header = response.header;

                                if (status === true) {
                                    $("#modifyOfficeMsg").html(response).css("color", "green").show();
                                    swal(header, message, responseStatus);
                                    //loadOffices(); //Reload registered offices table
                                } else {
                                    swal(header, message, responseStatus);
                                }
                            },
                            error: function() {
                                $("#modifyOfficeMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                            },
                            complete: function() { // Moved the timeout code to the complete callback
                                setTimeout(function() {
                                    $("#modifyOfficeMsg").fadeOut(300);
                                }, 3000);
                                $("#updateOfficeBtn").html("Update Office").show(); // Reset the button text
                            }
                        });
                    });
            });
        </script>
<?php
    }

    exit();
}
//LOAD OFFICE EDIT MODAL |||||||| Ends >>>>>>>>>>

//UPDATE OFFICE FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['modifyOfficeId'], $_POST['modifyOfficeName'], $_POST['modifyOfficeAbbr'])) {

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
        $modifyId = $_POST['modifyOfficeId'];
        $modifyOfficeStatus = $_POST['modifyOfficeStatus'];
        $modifyOfficeName = $_POST['modifyOfficeName'];
        $modifyOfficeAbbr = $_POST['modifyOfficeAbbr'];

        // Check for office duplicates 
        $stmt = $conn->prepare("SELECT COUNT(*) FROM election_offices WHERE id = ? AND name =? AND abbr =?");
        $stmt->bind_param("sss", $modifyId, $modifyOfficeName, $modifyOfficeAbbr);
        $stmt->execute();
        $stmt->bind_result($duplicateCount);
        $stmt->fetch();
        $stmt->close();


        if ($duplicateCount > 0) {
            $status = false;
            $header = 'No Changes!';
            $message = 'There is noting to update';
            $responseStatus = 'warning';
        } else {


            // Update office
            $stmt = $conn->prepare("UPDATE election_offices SET name = ?, abbr = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssss", $modifyOfficeName, $modifyOfficeAbbr, $modifyOfficeStatus, $modifyId);
            $queryUpdateOffice = $stmt->execute() or die(mysqli_error($conn));

            if ($queryUpdateOffice) {

                $status = true;
                $header = 'Successful!';
                $message = 'Office Updated Successfully';
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
    $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
//UPDATE OFFICE FUNCTION |||||||Ends>>>>>>>>>>

?>