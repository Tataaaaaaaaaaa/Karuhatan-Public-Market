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
      if ($row["userRole"] === 'collector') {

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

    <link rel="stylesheet" href="css/collector-dashboard-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg-tenant navbar-dark bg-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="collector-dashboard.php">
              <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="collector-dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="collector-report.php">Reports</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link nav-text" href="collector-tenant-list.php">Tenants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="collector-stall-list.php">Stalls</a>
                    </li>
                    <li class="nav-item" id="hidden-nav">
                      <a class="nav-link nav-text" href="collector-notifications.php">Notifications</a>
                    </li>
                    <li class="nav-item dropdown" id="hidden-nav">
                      <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?>&nbsp;</a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                          <li>
                            <a class="dropdown-item nav-text" href="collector-profile.php">Profile</a>
                          </li>
                          <li>
                            <a class="dropdown-item nav-text" href="collector-change-password.php">Change Password</a>
                          </li>
                          <li>
                            <a class="dropdown-item nav-text" href="php/logout.php">Log Out</a>
                          </li>
                      </ul>
                    </li>
                </ul>
                <div class="d-flex align-items-center justify-content-center" id="hidden-side-nav">
                    <div class="me-5 nav-text">
                        <a href="collector-notifications.php">
                          <i class="fa-solid fa-bell" style="color: #283891"></i>
                        </a>
                    </div>
                    <div class="dropdown ms-6">
                        <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item nav-text" href="collector-profile.php">Profile</a></li>
                            <li><a class="dropdown-item nav-text" href="collector-change-password.php">Change Password</a></li>
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
              <a class="btn btn-get-started btn-lg px-5 gap-3 switzer-semibold-text" href="collector-report.php" role="button">View Full Report</a>
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
                <a href="collector-report.php" style="text-decoration: none"><h6 class="footer-text mb-4">Reports</h6></a>
                <a href="collector-tenant-list.php" style="text-decoration: none"><h6 class="footer-text mb-4">Tenants</h6></a>
                <a href="collector-stall-list.php" style="text-decoration: none"><h6 class="footer-text mb-4">Stalls</h6></a>
              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="collector-notifications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Notifications</h6></a>
                <a href="collector-privacy-policy.php" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
                <a href="collector-terms-and-conditions.php" style="text-decoration: none"><h6 class="footer-text mb-4">Terms and Conditions</h6></a>
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

    <script>
      function redirectStallList() {
        location.replace("collector-stall-list.php");
      }

      function redirectTenantList() {
        location.replace("collector-tenant-list.php");
      }
    </script>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
