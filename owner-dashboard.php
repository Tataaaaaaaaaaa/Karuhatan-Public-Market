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

      $stmt_initial_load = $conn->prepare("SELECT firstName, emailAddress, occupiedPhase, gcashFile, gcashPhoneNumber, merchantName  FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $firstName = $row_initial_load['firstName'];
        $emailAddress = $row_initial_load['emailAddress'];
        $occupiedPhase = $row_initial_load['occupiedPhase'];
        $gcashFile = $row_initial_load['gcashFile'];
        $gcashPhoneNumber = $row_initial_load['gcashPhoneNumber'];
        $merchantName = $row_initial_load['merchantName'];
      }

      $stmt_initial_load->close();

      $stmt_get_tenants = $conn->prepare("SELECT * FROM user_registration WHERE occupiedPhase = ? AND userRole = 'tenant'");
      $stmt_get_tenants->bind_param("s", $occupiedPhase);
      $stmt_get_tenants->execute();
      $result_get_tenants = $stmt_get_tenants->get_result();
      $num_tenants = $result_get_tenants->num_rows;

      $stmt_get_tenants->close();

      $stmt_get_occupied_stalls = $conn->prepare("SELECT * FROM user_registration WHERE occupiedPhase = ? AND userRole = 'tenant' AND occupiedStall IS NOT NULL");
      $stmt_get_occupied_stalls->bind_param("s", $occupiedPhase);
      $stmt_get_occupied_stalls->execute();
      $result_get_occupied_stalls = $stmt_get_occupied_stalls->get_result();
      $num_occupied_stalls = $result_get_occupied_stalls->num_rows;

      $stmt_get_occupied_stalls->close();

      $stmt_get_stalls = $conn->prepare("SELECT * FROM tbl_stalls WHERE phase = ?");
      $stmt_get_stalls->bind_param("s", $occupiedPhase);
      $stmt_get_stalls->execute();
      $result_get_stalls = $stmt_get_stalls->get_result();
      $num_stalls = $result_get_stalls->num_rows;

      $stmt_get_stalls->close();

      $stmt_get_outstanding = $conn->prepare("SELECT * FROM user_registration WHERE occupiedPhase = ? AND (outstandingBalance IS NOT NULL AND outstandingBalance != 0)");
      $stmt_get_outstanding->bind_param("s", $occupiedPhase);
      $stmt_get_outstanding->execute();
      $result_get_outstanding = $stmt_get_outstanding->get_result();
      $num_outstanding = $result_get_outstanding->num_rows;

      date_default_timezone_set('Asia/Manila');

      $currentDate = date('Y-m-d');

      $stmt_get_paid = $conn->prepare("
          SELECT COUNT(*) as num_paid
          FROM user_registration ur
          JOIN tbl_payment tp ON ur.user_registration_id = tp.tenant_id
          WHERE ur.occupiedPhase = ?
          AND ur.userRole = 'tenant'
          AND ur.occupiedStall IS NOT NULL
          AND tp.status = 'paid'
          AND DATE(tp.datetime_sent) = ?
      ");

      $stmt_get_paid->bind_param("ss", $occupiedPhase, $currentDate);
      $stmt_get_paid->execute();
      $result_get_paid = $stmt_get_paid->get_result();

      $num_paid_row = $result_get_paid->fetch_assoc();
      $num_paid = $num_paid_row['num_paid'];

          
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karuhatan Public Market</title>
    <link href="assets/img/KPM Logo.png" rel="icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/owner-dashboard-style.css">
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
                      <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?>&nbsp;</a>
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
                        <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?></button>
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
        <div class="container-fluid px-4 custom-py-5 custom-my-5 custom-col-8">
            <h2 class="display-3 switzer-bold-text-white mb-4">
              <span class="welcome-name-style">Welcome, <?php echo $firstName; ?></span>
            </h2>
            <p class="display-sm about-us-text">This is your rental payment report for this day</p>
            <?php
              switch ($occupiedPhase) {
                case 'phaseOne':
                  echo '<p class="display-6 switzer-bold-text-white mb-0">Phase 1:</p>';
                  break;

                case 'phaseTwo':
                  echo '<p class="display-6 switzer-bold-text-white mb-0">Phase 2:</p>';
                  break;

                case 'phaseThree':
                  echo '<p class="display-6 switzer-bold-text-white mb-0">Phase 3:</p>';
                  break;

                case 'phaseFour':
                  echo '<p class="display-6 switzer-bold-text-white mb-0">Phase 4:</p>';
                  break;
              }
            ?>

            <p class="display-5 switzer-bold-text-white"><i class="fa-solid fa-store" style="color: white"></i>&nbsp;<?php echo $num_occupied_stalls; ?>
              <span class="display-sm switzer-medium-text-white">Total Occupied Stalls</span>
            </p>
            <p class="display-6 switzer-medium-text-white mb-1"><i class="fa-solid fa-money-bill-wave" style="color: white"></i>&nbsp;&nbsp;&nbsp;<?php echo $num_paid; ?> 
              <span class="display-sm">Paid</span>
            </p>
            <p class="display-6 switzer-medium-text-white mb-5"><i class="fa-solid fa-calendar-xmark" style="color: white"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $num_outstanding; ?> 
              <span class="display-sm">Unpaid</span>
            </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
              <a class="btn btn-get-started btn-lg px-5 gap-3 switzer-semibold-text" href="owner-report.php" role="button">View Full Report</a>
            </div>
        </div>
    </div>

    <div class="container py-7">
      <div class="row justify-content-between">

          <div class="col-md-6 mb-5">
              <?php
                echo '<div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                          <h3 class="switzer-bold-text display-6 custom-mx-4 mb-0"><i class="fa-solid fa-store" style="color: #283891"></i>&nbsp;&nbsp;Total Stalls</h3>
                          <span class="display-6 switzer-bold-text">'.$num_stalls.'</span>
                        </div>

                        <div class="col-lg-12 text-center px-3 mt-5">
                          <button onclick="redirectStallList()" class="px-6 btn btn-lg btn-signup btn-login mb-2" type="button">View List</button> 
                        </div>
                      </div>';
              ?>
          </div>
          <div class="col-md-6 mb-5">
              <?php
                if($num_tenants === 0) {
                  echo '<div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px;">
                          <div class="d-flex justify-content-center align-items-center">
                            <h3 class="switzer-bold-text display-6 custom-mx-4 mb-0"><i class="fa-solid fa-user-large" style="color: #283891"></i>&nbsp;&nbsp;No Tenants Yet</h3>
                          </div>
          
                          <div class="col-lg-12 text-center px-3 mt-5">
                            <button onclick="redirectTenantList()" class="px-6 btn btn-lg btn-signup btn-login mb-2" type="button">Check Applications</button> 
                          </div>
                        </div>';
                } else {
                  echo '<div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px;">
                          <div class="d-flex justify-content-between align-items-center">
                            <h3 class="switzer-bold-text display-6 custom-mx-4 mb-0"><i class="fa-solid fa-user-large" style="color: #283891"></i>&nbsp;&nbsp;Total Tenants</h3>
                            <span class="display-6 switzer-bold-text">'.$num_tenants.'</span>
                          </div>
          
                          <div class="col-lg-12 text-center px-3 mt-5">
                            <button onclick="redirectTenantList()" class="px-6 btn btn-lg btn-signup btn-login mb-2" type="button">View List</button> 
                          </div>
                        </div>';
                }
              ?>
          </div>
                
            <?php
              if(!empty($gcashPhoneNumber) && !empty($merchantName)){
                echo '<form action="" method="POST" id="phoneNumberForm" autocomplete="off" class="col-md-12 mb-5">
                        <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                          <div class="row d-flex align-items-center">
                            <div class="col-md-6">
                              <h3 class="switzer-bold-text display-6 mx-4">Merchant Name</h3>
                            </div>
                            <div class="col-md-6 d-flex align-items-center custom-text-center">
                              <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <h3 class="switzer-bold-text display-6 mx-4" id="displayMerchantName">'.$merchantName.'</h3>
                                <input name="merchantName" type="text" class="form-control form-input-style custom-mx-4" id="merchantName" pattern="[\sA-Za-z]+" title="Please enter a valid merchant name." placeholder="" required autocomplete="off" style="display:none">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <h3 class="switzer-bold-text display-6 mx-4">GCash Phone Number</h3>
                            </div>
                            <div class="col-md-6 d-flex align-items-center custom-text-center">
                              <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <h3 class="switzer-bold-text display-6 mx-4" id="displayGCashPhoneNumber">'.$gcashPhoneNumber.'</h3>
                                <input name="gcashPhoneNumber" type="tel" class="form-control form-input-style custom-mx-4" id="gcashPhoneNumber" pattern="09[0-9]{9}" title="Please enter a valid 11-digit phone number." placeholder="" required autocomplete="off" style="display:none">
                              </div>
                            </div>
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6 d-flex align-items-center custom-text-center">
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                              <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" id="editBtn_2">Edit</a>
                            </div>
                          </div>
                          <div class="col-lg-12 d-flex custom-text-center px-3">
                            <button class="btn btn-green btn-lg px-5 switzer-semibold-text-white btn-widen" id="saveBtn_2" style="display: none" type="submit">Save</button>
                            <a class="btn btn-red btn-lg px-5 switzer-semibold-text-white custom-mx-btn btn-widen" id="cancelBtn_2" role="button" style="display: none">Cancel</a>
                          </div>
                          </div>
                        </div>
                      </form>';
              } else {
                echo '<form action="" method="POST" id="phoneNumberForm" autocomplete="off" class="col-md-12 mb-5">
                        <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                          <div class="row d-flex align-items-center">
                            <div class="col-md-6">
                              <h3 class="switzer-bold-text display-6 mx-4">No Merchant Name Yet</h3>
                            </div>
                            <div class="col-md-6 d-flex align-items-center custom-text-center">
                              <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <input name="merchantName" type="text" class="form-control form-input-style custom-mx-4" id="merchantName" pattern="[\sA-Za-z]+" title="Please enter a valid merchant name." placeholder="" required autocomplete="off">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <h3 class="switzer-bold-text display-6 mx-4">No GCash Phone Number Yet</h3>
                            </div>
                            <div class="col-md-6 d-flex align-items-center custom-text-center">
                              <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <input name="gcashPhoneNumber" type="tel" class="form-control form-input-style custom-mx-4" id="gcashPhoneNumber" pattern="09[0-9]{9}" title="Please enter a valid 11-digit phone number." placeholder="" required autocomplete="off">
                              </div>
                            </div>
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6 d-flex align-items-center custom-text-center">
                              <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <button class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4">Submit</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>';
              }
            ?>
              <!-- <form action="" method="POST" id="phoneNumberForm" autocomplete="off" class="col-md-12 mb-5">
                <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="row d-flex align-items-center">
                    <div class="col-md-6">
                      <h3 class="switzer-bold-text display-6 mx-4">GCash Phone Number:</h3>
                    </div>
                    <div class="col-md-6 d-flex align-items-center custom-text-center">
                      <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <h3 class="switzer-bold-text display-6 mx-4" id="displayGCashPhoneNumber"><?php echo $gcashPhoneNumber; ?></h3>
                        <input name="gcashPhoneNumber" type="tel" class="form-control form-input-style custom-mx-4" id="gcashPhoneNumber" pattern="09[0-9]{9}" title="Please enter a valid 11-digit phone number." placeholder="" required autocomplete="off">
                      </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6 d-flex align-items-center custom-text-center">
                      <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <button class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4">Submit</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form> -->

          <?php
            if(!empty($gcashFile)) {
              echo '<form action="" method="POST" id="gcashQRCodeForm" onsubmit="return validateGCashQRCodeForm()" autocomplete="off" class="col-md-12">
                      <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                        <div class="row d-flex align-items-center">
                          <div class="col-md-6">
                            <h3 class="switzer-bold-text display-6 mx-4">GCash QR Code</h3>
                          </div>
                          <div class="col-md-6 d-flex align-items-center custom-text-center">
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                              <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white custom-mx-4" id="downloadGCash" href="download-gcash-qr-code.php?occupiedPhase='.$occupiedPhase.'">Download QR</a>
                              <input name="gcashQRCode" type="file" id="gcashQRCode" class="form-control custom-mx-4" accept="images/jpg, images/png" title="Please upload a GCash QR Code." required style="display:none">
                            </div>
                          </div>
                          <div class="col-md-6">
                          </div>
                          <div class="col-md-6 d-flex align-items-center custom-text-center">
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                              <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" id="editBtn">Edit</a>
                            </div>
                          </div>
                          <div class="col-lg-12 d-flex custom-text-center px-3">
                            <button class="btn btn-green btn-lg px-5 switzer-semibold-text-white btn-widen" id="saveBtn" style="display: none" type="submit">Save</button>
                            <a class="btn btn-red btn-lg px-5 switzer-semibold-text-white custom-mx-btn btn-widen" id="cancelBtn" role="button" style="display: none">Cancel</a>
                          </div>
                        </div>
                      </div>
                    </form>';

            } else {
              echo '<form action="" method="POST" id="gcashQRCodeForm" onsubmit="return validateGCashQRCodeForm()" autocomplete="off" class="col-md-12">
                      <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                        <div class="row d-flex align-items-center">
                          <div class="col-md-6">
                            <h3 class="switzer-bold-text display-6 mx-4">No GCash QR Code Yet</h3>
                          </div>
                          <div class="col-md-6 d-flex align-items-center custom-text-center">
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                              <input name="gcashQRCode" type="file" id="gcashQRCode" class="form-control custom-mx-4" accept="images/jpg, images/png" title="Please upload a GCash QR Code." required>
                            </div>
                          </div>
                          <div class="col-md-6">
                          </div>
                          <div class="col-md-6 d-flex align-items-center custom-text-center">
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                              <button class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4">Submit</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>';
            }
          ?>
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
                <a href="owner-users-collectors.php" style="text-decoration: none"><h6 class="footer-text mb-4">Collectors</h6></a>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="invalidUploadModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Upload!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Please select a JPG or PNG file.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="successQRCodeModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(76,217,100,0.5); color: #4CD964"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Uploaded Successfully</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The GCash QR Code has been uploaded successfully</h4>
            </div>
            <div class="d-flex justify-content-center py-1">
              <button type="button" class="btn btn-lg btn-outline-success rounded-pill me-2 px-5" data-bs-dismiss="modal" id="continueButtonQRCode" style="font-family: 'Switzer-Semibold'">Continue</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="successPhoneNumberModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(76,217,100,0.5); color: #4CD964"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Changes Made</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The merchant name and phone number for GCash rental payment has been successfully uploaded</h4>
            </div>
            <div class="d-flex justify-content-center py-1">
              <button type="button" class="btn btn-lg btn-outline-success rounded-pill me-2 px-5" data-bs-dismiss="modal" id="continueButtonPhoneNumber" style="font-family: 'Switzer-Semibold'">Continue</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      function redirectStallList() {
        location.replace("owner-stalls.php");
      }

      function redirectTenantList() {
        location.replace("owner-users-tenants.php");
      }

      function redirectApplications() {
        location.replace("owner-applications.php");
      }
    </script>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
      document.getElementById("editBtn_2").addEventListener("click", function() {
          document.getElementById("editBtn_2").style.display = "none";
          document.getElementById("saveBtn_2").style.display = "block";
          document.getElementById("cancelBtn_2").style.display = "block";
          document.getElementById("gcashPhoneNumber").style.display = "block";
          document.getElementById("merchantName").style.display = "block";
          document.getElementById("displayGCashPhoneNumber").style.display = "none";
          document.getElementById("displayMerchantName").style.display = "none";
      });

      document.getElementById("cancelBtn_2").addEventListener("click", function() {
          document.getElementById("editBtn_2").style.display = "block";
          document.getElementById("saveBtn_2").style.display = "none";
          document.getElementById("cancelBtn_2").style.display = "none";
          document.getElementById("gcashPhoneNumber").style.display = "none";
          document.getElementById("merchantName").style.display = "none";
          document.getElementById("displayGCashPhoneNumber").style.display = "block";
          document.getElementById("displayMerchantName").style.display = "block";

          var phoneNumberForm = document.getElementById("phoneNumberForm");
          phoneNumberForm.reset();
      });
    </script>

    <script>
      document.getElementById("editBtn").addEventListener("click", function() {
          document.getElementById("editBtn").style.display = "none";
          document.getElementById("saveBtn").style.display = "block";
          document.getElementById("cancelBtn").style.display = "block";
          document.getElementById("gcashQRCode").style.display = "block";
          document.getElementById("downloadGCash").style.display = "none";
      });

      document.getElementById("cancelBtn").addEventListener("click", function() {
          document.getElementById("editBtn").style.display = "block";
          document.getElementById("saveBtn").style.display = "none";
          document.getElementById("cancelBtn").style.display = "none";
          document.getElementById("gcashQRCode").style.display = "none";
          document.getElementById("downloadGCash").style.display = "block";

          var gcashQRCodeForm = document.getElementById("gcashQRCodeForm");
          gcashQRCodeForm.reset();
      });
    </script>

    <script>
      var gCashQRCodeInput = document.getElementById('gcashQRCode');
      var gCashQRCodeInputStatus = false;

      gCashQRCodeInput.addEventListener('change', e => {
          const file = e.target.files[0];
          if (file) {
              const fileType = file.type;
              if (fileType === 'image/jpeg' || fileType === 'image/png') {
                  gCashQRCodeInputStatus = true;
                  var reader = new FileReader();
                  reader.onload = function() {

                  };
                  reader.readAsDataURL(file);
              } else {
                  $('#invalidUploadModal').modal('show');
                  gCashQRCodeInputStatus = false;
                  gCashQRCodeInput.value = '';
              }
          }
      });

      function validateGCashQRCodeForm(){
        if(gCashQRCodeInputStatus == true) {
            return true;
            
        } else {
        return false;
        
        }
      }
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        var successQRCodeModal = document.getElementById('successQRCodeModal');
        var continueButtonQRCode = document.getElementById('continueButtonQRCode');

        successQRCodeModal.addEventListener('hidden.bs.modal', function() {
          window.location.href = "owner-dashboard.php";
        });

        continueButtonQRCode.addEventListener('click', function() {
          window.location.href = "owner-dashboard.php";
        });

        var successPhoneNumberModal = document.getElementById('successPhoneNumberModal');
        var continueButtonPhoneNumber = document.getElementById('continueButtonPhoneNumber');

        successPhoneNumberModal.addEventListener('hidden.bs.modal', function() {
          window.location.href = "owner-dashboard.php";
        });

        continueButtonPhoneNumber.addEventListener('click', function() {
          window.location.href = "owner-dashboard.php";
        });
      });
    </script>

    <script>  
      document.getElementById("gcashQRCodeForm").addEventListener("submit", function(event) {
        event.preventDefault();
    
        var formData = new FormData(this);
        formData.append("occupiedPhase", "<?php echo $occupiedPhase; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/gcash-qr-code-submission-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successQRCodeModal').modal('show');
                    }
                } else {
                  $('#failModal').modal('show');
                }
            }
        };
        xhr.send(formData);
      });
    </script>

    <script>  
      document.getElementById("phoneNumberForm").addEventListener("submit", function(event) {
        event.preventDefault();
    
        var formData = new FormData(this);
        formData.append("occupiedPhase", "<?php echo $occupiedPhase; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/gcash-phonenumber-submission-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successPhoneNumberModal').modal('show');
                    }
                } else {
                  $('#failModal').modal('show');
                }
            }
        };
        xhr.send(formData);
      });
    </script>
    
</body>
</html>
