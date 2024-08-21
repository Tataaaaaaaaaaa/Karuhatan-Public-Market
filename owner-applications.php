<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
  echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
} else {
  if (isset($_SESSION['userId'])) {
    $userID = $_SESSION['userId'];

    $stmt = $conn->prepare("SELECT * FROM user_registration WHERE user_registration_id = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if ($row["userRole"] === 'owner') {

      $stmt_initial_load = $conn->prepare("SELECT emailAddress, occupiedPhase FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $userEmailAddress = $row_initial_load['emailAddress'];
        $occupiedPhase = $row_initial_load['occupiedPhase'];
      }

      $stmt_initial_load->close();

      $sql = "SELECT application_id, firstName, middleName, lastName, address, emailAddress, phoneNumber, stallCategory, preferredStall, DATE(submission_time) AS submission_date, applicationStatus FROM tenant_rental_application";
      $result = $conn->query($sql);

      $sql_export = "SELECT application_id, firstName, middleName, lastName, address, emailAddress, phoneNumber, stallCategory, preferredStall, DATE(submission_time) AS submission_date, applicationStatus FROM tenant_rental_application";
      $result_export = $conn->query($sql_export);
          
      } else {
        header("Location: log-in.html");
        exit();
      }
    } else {
      header("Location: log-in.html");
      exit();
    }
  } else {
    header("Location: log-in.html");
    exit();
  }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userId'])) {
  if (isset($_POST['rejectButton'])) {
      $application_id = $_POST['application_id'];
      updateStatus("Rejected", $application_id, $occupiedPhase);
  } elseif (isset($_POST['approveButton'])) {
      $application_id = $_POST['application_id']; 
      updateStatus("Approved", $application_id, $occupiedPhase);
  } 
}


function updateStatus($status, $application_id, $occupiedPhase) {
  global $conn;

  $sqlSelectUserId = "SELECT user_registration_id, stallCategory FROM tenant_rental_application WHERE application_id = ?";
  $stmtSelectUserId = $conn->prepare($sqlSelectUserId);
  $stmtSelectUserId->bind_param("i", $application_id);
  $stmtSelectUserId->execute();
  $stmtSelectUserId->bind_result($userRegistrationId, $stallCategory);
  $stmtSelectUserId->fetch();
  $stmtSelectUserId->close();

  $sqlSelectEmailAddress = "SELECT firstName, lastName, emailAddress FROM user_registration WHERE user_registration_id = ?";
  $stmtSelectEmailAddress = $conn->prepare($sqlSelectEmailAddress);
  $stmtSelectEmailAddress->bind_param("i", $userRegistrationId);
  $stmtSelectEmailAddress->execute();
  $stmtSelectEmailAddress->bind_result($firstName, $lastName, $emailAddress);
  $stmtSelectEmailAddress->fetch();
  $stmtSelectEmailAddress->close();

  date_default_timezone_set('Asia/Manila');
  $currentDateTime = date('Y-m-d H:i:s');

  $sqlUpdateStatus = "UPDATE tenant_rental_application SET applicationStatus = ? WHERE application_id = ?";
  $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
  $stmtUpdateStatus->bind_param("si", $status, $application_id);
  $stmtUpdateStatus->execute();
  $stmtUpdateStatus->close();

  if ($status == "Approved") {

    $sqlUpdateUserRole = "UPDATE user_registration AS ur
                          INNER JOIN tenant_rental_application AS tra ON ur.user_registration_id = tra.user_registration_id
                          SET ur.userRole = 'tenant'
                          WHERE tra.application_id = ?";
    $stmtUpdateUserRole = $conn->prepare($sqlUpdateUserRole);
    $stmtUpdateUserRole->bind_param("i", $application_id);
    $stmtUpdateUserRole->execute();
    $stmtUpdateUserRole->close();

    $sqlUpdatePhase = "UPDATE user_registration SET occupiedPhase = ?, stallCategory = ? WHERE user_registration_id = ?";
    $stmtUpdatePhase = $conn->prepare($sqlUpdatePhase);
    $stmtUpdatePhase->bind_param("ssi", $occupiedPhase, $stallCategory, $userRegistrationId);
    $stmtUpdatePhase->execute();
    $stmtUpdatePhase->close();

    $action = 'approvedTRA';

    $stmt_notifications = $conn->prepare("INSERT INTO tbl_notifications (user_id, action_date_time, action) VALUES (?, ?, ?)");
    $stmt_notifications->bind_param("iss", $userRegistrationId, $currentDateTime, $action);  
    $stmt_notifications->execute();
    $stmt_notifications->close();

    echo "<script>
      var formData = new FormData();
      formData.append('emailAddress', '$emailAddress');
      formData.append('firstName', '$firstName');
      formData.append('lastName', '$lastName');
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '../Developer things/php/notify-approved-tenant-connect.php', true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          var response = JSON.parse(xhr.responseText);
          if (xhr.status === 200) {
            if (response.success) {
              
            } else {
              
            }
          } else {
            
          }
        }
      };
      xhr.send(formData);
    </script>";

  } else {

    $action = 'rejectedTRA';

    $stmt_notifications = $conn->prepare("INSERT INTO tbl_notifications (user_id, action_date_time, action) VALUES (?, ?, ?)");
    $stmt_notifications->bind_param("iss", $userRegistrationId, $currentDateTime, $action);  
    $stmt_notifications->execute();
    $stmt_notifications->close();

    echo "<script>
      var formData = new FormData();
      formData.append('emailAddress', '$emailAddress');
      formData.append('firstName', '$firstName');
      formData.append('lastName', '$lastName');
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '../Developer things/php/notify-rejected-tenant-connect.php', true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          var response = JSON.parse(xhr.responseText);
          if (xhr.status === 200) {
            if (response.success) {
              alert('Notification sent successfully');
            } else {
              alert('Failed to send notification');
            }
          } else {
            alert('Failed to send notification');
          }
        }
      };
      xhr.send(formData);
    </script>";

  }

  ?>
    <script>
      window.location.href = "owner-applications.php";
    </script>
  <?php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karuhatan Public Market</title>
    <link href="assets/img/KPM Logo.png" rel="icon">

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css"> -->
    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
    <!-- <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'> -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/owner-applications-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg-tenant navbar-dark bg-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="owner-dashboard.php">
          <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link nav-text" href="owner-dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-text" href="owner-report.php">Reports</a>
                </li>
                <li class="nav-item dropdown">
                  <div class="m-0 p-0" style="position: relative">
                    <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownUsersLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Users</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsersLink">
                        <li>
                          <a class="dropdown-item nav-text" href="owner-users-tenants.php">Tenants</a>
                        </li>
                        <li>
                          <a class="dropdown-item nav-text" href="owner-users-collectors.php">Collectors</a>
                        </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-text" href="owner-stalls.php">Stalls</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link nav-text" href="owner-applications.php">Applications</a>
              </li>
                <li class="nav-item" id="hidden-nav">
                  <a class="nav-link nav-text" href="owner-notifications.php">Notifications</a>
                </li>
                <li class="nav-item dropdown" id="hidden-nav">
                  <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $userEmailAddress; ?>&nbsp;</a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <li>
                        <a class="dropdown-item nav-text" href="owner-profile.php">Profile</a>
                      </li>
                      <li>
                        <a class="dropdown-item nav-text" href="owner-change-password.php">Change Password</a>
                      </li>
                      <li>
                        <a class="dropdown-item nav-text" href="php/logout.php">Log Out</a>
                      </li>
                  </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center justify-content-center" id="hidden-side-nav">
                <div class="me-5 nav-text">
                    <a href="owner-notifications.php">
                      <i class="fa-solid fa-bell" style="color: #283891"></i>
                    </a>
                </div>
                <div class="dropdown ms-6">
                    <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $userEmailAddress; ?></button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item nav-text" href="owner-profile.php">Profile</a></li>
                        <li><a class="dropdown-item nav-text" href="owner-change-password.php">Change Password</a></li>
                        <li><a class="dropdown-item nav-text" href="php/logout.php">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  </nav>


    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Applications</h2>
      </div>
    </div>

    <?php
      if ($result->num_rows > 0) {
        echo '<div class="container py-7 px-0">
                <div class="py-5 px-5 mx-auto" style="max-width: 1700px; box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="d-flex justify-content-start mb-1">
                    <div class="dropdown me-2">
                      <button class="btn btn-signup" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                          All
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                          <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="all">All</a></li>
                          <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="pending">Pending</a></li>
                          <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="approved">Approved</a></li>
                          <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="rejected">Rejected</a></li>
                      </ul>
                    </div>
          
                    <div class="me-2">
                      <button class="btn btn-signup" type="button" id="exportTbl">
                        Export
                      </button>
                    </div>
                  </div>
                    
                    <table id="tenantApplicationsTable" class="table" style="width:100%">
                        <thead>
                            <tr class="display-sm">
                                <th>Name</th>
                                <th>Preferred Stall</th>
                                <th>Application Status</th>
                                <th>Date</th>
                                <th style="text-align: center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
                          while ($row = $result->fetch_assoc()) {
                            echo "<tr class='status-" . strtolower($row["applicationStatus"]) . "'>";
                            echo "<td>" . $row["firstName"] . " " . $row["middleName"] . " " . $row["lastName"] . "</td>";
                            echo "<td>" . (!empty($row["preferredStall"]) ? $row["preferredStall"] : "None") . "</td>";
                            echo "<td>" . $row["applicationStatus"] . "</td>";
                            echo "<td>" . $row["submission_date"] . "</td>";
                            echo "<td>";
                            echo "<div>";
                            echo "<button class='btn btn-sm btn-signup btn-login px-4' type='button' data-bs-toggle='modal' data-bs-target='#detailModal'
                            data-applicationid='" . $row["application_id"] . "'
                            data-firstname='" . $row["firstName"] . "'
                            data-middlename='" . $row["middleName"] . "'
                            data-lastname='" . $row["lastName"] . "'
                            data-address='" . $row["address"] . "'
                            data-email='" . $row["emailAddress"] . "'
                            data-phone='" . $row["phoneNumber"] . "'
                            data-stallcategory='" . $row["stallCategory"] . "'
                            data-preferredstall='" . (!empty($row["preferredStall"]) ? $row["preferredStall"] : "None") . "'
                            data-applicationstatus='" . $row["applicationStatus"] . "'
                            data-submissiondate='" . $row["submission_date"] . "'>Details</button>";
      
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                          }
                  echo '</tbody>
                  </table>
                </div>
              </div>';
      } else {
        echo '<div class="container py-9 px-5">
                <div class="row py-5 align-items-center mb-4" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="col-12 text-center">
                      <h3 class="switzer-semibold-text display-0">No Applications Yet</h3>
                  </div>
                </div>
              </div>';
      }
    ?>

    <!-- <div class="container py-7 px-0">
      <div class="py-5 px-5 mx-auto" style="max-width: 1700px; box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="d-flex justify-content-start mb-1">
          <div class="dropdown me-2">
            <button class="btn btn-signup" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                All
            </button>
            <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="all">All</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="pending">Pending</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="approved">Approved</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="rejected">Rejected</a></li>
            </ul>
          </div>

          <div class="me-2">
            <button class="btn btn-signup" type="button" id="exportTbl">
              Export
            </button>
          </div>
        </div>
          
          <table id="tenantApplicationsTable" class="table" style="width:100%">
              <thead>
                  <tr class="display-sm">
                      <th>Name</th>
                      <th>Preferred Stall</th>
                      <th>Application Status</th>
                      <th>Date</th>
                      <th style="text-align: center">Actions</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr class='status-" . strtolower($row["applicationStatus"]) . "'>";
                      echo "<td>" . $row["firstName"] . " " . $row["middleName"] . " " . $row["lastName"] . "</td>";
                      echo "<td>" . (!empty($row["preferredStall"]) ? $row["preferredStall"] : "None") . "</td>";
                      echo "<td>" . $row["applicationStatus"] . "</td>";
                      echo "<td>" . $row["submission_date"] . "</td>";
                      echo "<td>";
                      echo "<div>";
                      echo "<button class='btn btn-sm btn-signup btn-login px-4' type='button' data-bs-toggle='modal' data-bs-target='#detailModal'
                      data-applicationid='" . $row["application_id"] . "'
                      data-firstname='" . $row["firstName"] . "'
                      data-middlename='" . $row["middleName"] . "'
                      data-lastname='" . $row["lastName"] . "'
                      data-address='" . $row["address"] . "'
                      data-email='" . $row["emailAddress"] . "'
                      data-phone='" . $row["phoneNumber"] . "'
                      data-stallcategory='" . $row["stallCategory"] . "'
                      data-preferredstall='" . (!empty($row["preferredStall"]) ? $row["preferredStall"] : "None") . "'
                      data-applicationstatus='" . $row["applicationStatus"] . "'
                      data-submissiondate='" . $row["submission_date"] . "'>Details</button>";

                      echo "</div>";
                      echo "</td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>No Applications Yet</td></tr>";
                  }
                ?>
            </tbody>
        </table>
        </div>
    </div> -->

    <div style="display: none">
        <table id="tenantApplicationsTable_Export" class="table" style="width:100%; display: none">
            <thead>
                <tr class="display-sm">
                    <th>Application ID</th>
                    <th>Date Submitted</th>
                    <th>Application Status</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Stall Category</th>
                    <th>Preferred Stall</th>
                </tr>
            </thead>
            <tbody>
              <?php
                if ($result_export->num_rows > 0) {
                  while ($row_export = $result_export->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_export["application_id"] . "</td>";
                    echo "<td>" . $row_export["submission_date"] . "</td>";
                    echo "<td>" . $row_export["applicationStatus"] . "</td>";
                    echo "<td>" . $row_export["firstName"] . " " . $row_export["middleName"] . " " . $row_export["lastName"] . "</td>";
                    echo "<td>" . $row_export["address"] . "</td>";
                    echo "<td>" . $row_export["emailAddress"] . "</td>";
                    echo "<td>" . $row_export["phoneNumber"] . "</td>";
                    echo "<td>" . $row_export["stallCategory"] . "</td>";
                    echo "<td>" . (!empty($row_export["preferredStall"]) ? $row_export["preferredStall"] : "None") . "</td>";
                    echo "</tr>";
                  }
                }
              ?>
          </tbody>
      </table>
    </div>
    
    <!-- <div class="modal fade" tabindex="-1" role="dialog" id="detailModal">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <div class="py-2">
              <div class="col-md-12">
                <p class="px-4 pb-0 mb-4 switzer-bold-text display-6">Add Payment</p>
              </div>
              <div class="col-md-12">
                <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Tenant</span>
                  <span class="text-end switzer-bold-text">Juan Dela Cruz</span>
                </p>
                <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Stall</span>
                  <span class="text-end switzer-bold-text">PX-XX</span>
                </p>
                <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Payment Method</span>
                  <span class="text-end switzer-bold-text">GCash</span>
                </p>
                <p class="px-4 pb-0 mt-4 mb-1 display-0 d-flex">
                  <span class="switzer-bold-text">Payment Detail</span>
                </p>
                <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Rent</span>
                  <span class="text-end switzer-bold-text"><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</span>
                </p>
                <p class="px-4 pb-0 mt-4 mb-5 display-6 d-flex justify-content-between">
                  <span class="switzer-semibold-text">Total Amount Paid</span>
                  <span class="text-end switzer-bold-text"><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</span>
                </p>
              </div>
            </div>
            <div class="text-center px-4 py-1">
              <button type="button" class="btn btn-lg btn-signup px-5" data-bs-dismiss="modal">Add Payment</button>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
          
          </div>
        </div>
      </div>
    </div>
    
    <footer class="text-center text-lg-start">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        </section>
      
        <section class="">
          <div class="container text-center text-md-start mt-5">
            <div class="row mt-3">
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <a href="owner-dashboard.php">
                    <img src="../Developer things/assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid mb-3 footer-logo">
                </a>
                <p class="switzer-medium-text">Join us at the Karuhatan Public Market, where every stall offers quality goods and every visit is an opportunity to discover something new.</p>
              </div>
      
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <a href="owner-report.php" style="text-decoration: none"><h6 class="footer-text mb-4">Reports</h6></a>
                <a href="owner-users-tenants.php" style="text-decoration: none"><h6 class="footer-text mb-4">Tenants</h6></a>
                <a href="owner-users-collectors.php" style="text-decoration: none"><h6 class="footer-text mb-4">Users</h6></a>
                <a href="owner-stalls.php" style="text-decoration: none"><h6 class="footer-text mb-4">Stalls</h6></a>
              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="owner-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Applications</h6></a>
                <a href="owner-notifications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Notifications</h6></a>
                <a href="owner-privacy-policy.php" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
                <a href="owner-terms-and-conditions.php" style="text-decoration: none"><h6 class="footer-text mb-4">Terms and Conditions</h6></a>
              </div>
      
              <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="footer-text mb-4">Contact</h6>
                <p class="switzer-medium-text"><i class="fas fa-home me-3 custom-icon-color"></i>#8 Karuhatan Road, Karuhatan Valenzuela City, 1441</p>
                <p class="switzer-medium-text"><i class="fas fa-envelope me-3 custom-icon-color"></i>Karuhatanmarketoffice@gmail.com</p>
                <p class="switzer-medium-text"><i class="fas fa-phone me-3 custom-icon-color"></i>09694252876</p>
              </div>
            </div>
          </div>
        </section>
      
        <div class="text-center switzer-semibold-text-white p-4" style="background-color: #283891;">© 2024 Copyright:
          <a class="switzer-semibold-text-white" style="text-decoration: none">Karuhatan Public Market</a>
        </div>
    </footer>

    <div class="modal fade" tabindex="-1" role="dialog" id="successModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="color: #4CD964;"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Successful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The tenant rental application table has been successfully updated!</h4>
            </div>
            <div class="py-1"><button type="button" class="btn btn-lg btn-outline-success rounded-pill px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button></div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Something went wrong</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Unfortunately we encountered an issue with updating the tenant rental application table. Please try again later.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

    <script>
      $(document).ready(function() {
        $('#detailModal').on('show.bs.modal', function(event) {
          var button = $(event.relatedTarget);
          var applicationid = button.data('applicationid');
          var firstname = button.data('firstname');
          var middlename = button.data('middlename');
          var lastname = button.data('lastname');
          var address = button.data('address');
          var email = button.data('email');
          var phone = button.data('phone');
          var stallcategory = button.data('stallcategory');
          var preferredstall = button.data('preferredstall');
          var validid = button.data('validid');
          var cedula = button.data('cedula');
          var applicationstatus = button.data('applicationstatus');
          var submissiondate = button.data('submissiondate');

          var modal = $(this);
          modal.find('.modal-body').html(`
          <div class="py-2">
            <div class="col-md-12">
             <p class="px-4 pb-0 mb-0 switzer-bold-text display-6 d-flex justify-content-between">
              <span>Application Form</span>
              <span class="text-end switzer-semibold-text">
                <i class="fa-solid fa-xmark" data-bs-dismiss="modal" style="cursor: pointer"></i>
              </span>
            </p>
            </div>
            <div class="col-md-12">
              <p class="px-4 pb-0 mb-4 display-sm d-flex justify-content-between">
                  <span class="switzer-medium-text">Status: ${applicationstatus}</span>
                  <span class="text-end switzer-semibold-text">${submissiondate}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Applicant</span>
                  <span class="text-end switzer-bold-text">${firstname} ${middlename} ${lastname}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Address</span>
                  <span class="text-end switzer-bold-text">${address}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Email Address</span>
                  <span class="text-end switzer-bold-text">${email}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Phone Number</span>
                  <span class="text-end switzer-bold-text">${phone}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Stall Category</span>
                  <span class="text-end switzer-bold-text">${stallcategory}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Preferred Stall</span>
                  <span class="text-end switzer-bold-text">${preferredstall}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Valid ID</span>
                  <span class="text-end switzer-bold-text"><a href="download-prospective-tenant-application.php?application_id=${applicationid}&file_type=validId" download>Download</a></span>
              </p>
              <p class="px-4 pb-0 mb-4 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Cedula</span>
                  <span class="text-end switzer-bold-text"><a href="download-prospective-tenant-application.php?application_id=${applicationid}&file_type=cedula" download>Download</a></span>
              </p>
              <div class="d-flex justify-content-center py-1">
                <form id="applicationForm" method="POST">
                  <input type="hidden" name="application_id" value="${applicationid}">
                    ${applicationstatus === 'Pending' ? `
                      <button type="submit" name="rejectButton" class="btn btn-lg btn-outline-fail rounded-pill me-2 px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Reject</button>
                      <button type="submit" name="approveButton" class="btn btn-lg btn-outline-success rounded-pill me-2 px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Approve</button>
                    ` : ''}
                </form>
              </div>
            </div>
          </div>
          `);
        });
      });

    $(document).ready(function() {
        var table = $('#tenantApplicationsTable').DataTable({            
            "searching": false,
            
            "ordering": false
        });
    });
    </script>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const dropdownItems = document.querySelectorAll('.statusFilter');
        const tableRows = document.querySelectorAll('#tenantApplicationsTable tbody tr');

        dropdownItems.forEach(item => {
            item.addEventListener('click', (event) => {
                event.preventDefault();
                const status = event.target.getAttribute('data-status').toLowerCase();
                filterTable(status);
                document.getElementById('statusFilterDropdown').innerText = event.target.innerText;
            });
        });

        function filterTable(status) {
            tableRows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    if (row.classList.contains(`status-${status}`)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {

      document.getElementById("exportTbl").addEventListener("click", function(event) {
        exportData('csv');
      });

      function exportData(type) {
        const exportedFileName = 'tenant-rental-application-sheet.' + type;
        const tblReport = document.getElementById("tenantApplicationsTable_Export");
        const tblClone = tblReport.cloneNode(true);
        const rows = tblClone.rows;

        for (let i = 1; i < rows.length; i++) {
          let cell = rows[i].cells[6];
          cell.innerText = "'" + cell.innerText;
        }

        const wb = XLSX.utils.table_to_book(tblClone);
        XLSX.writeFile(wb, exportedFileName);
      }
    });
  </script>

<!-- <script>
  document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("printTable").addEventListener("click", function() {
      printTable();
    });

    function printTable() {
      const tblReport = document.getElementById("tenantApplicationsTable_Export");
      const tblClone = tblReport.cloneNode(true);

      const printWindow = window.open('', '_blank');
      printWindow.document.write('<html><head><title>Print Table</title>');
      printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid black; padding: 8px; text-align: left; }</style>');
      printWindow.document.write('</head><body>');
      printWindow.document.write(tblClone.outerHTML);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.print();
    }
  });
</script> -->


<!-- 
  <script>
    $(document).ready(function() {
    $('#applicationForm').on('submit', function(event) {
        event.preventDefault();
        
        var form = $(this);
        var formData = form.serialize();

        var action = '';
        if (form.find('button[name="rejectButton"]').is(':focus')) {
            action = 'reject';
        } else if (form.find('button[name="approveButton"]').is(':focus')) {
            action = 'approve';
        }

        $.ajax({
            type: 'POST',
            url: '../Developer things/php/update-tenant-application-status-connect.php',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                    $('#successModal').modal('show');
                    $('#successModal').on('hidden.bs.modal', function () {
                        window.location.href = 'owner-applications.php';
                    });
                } else {
                    $('#failModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
                alert('An error occurred. Please try again.');
            }
        });
    });
});
  </script> -->
</body>
</html>
