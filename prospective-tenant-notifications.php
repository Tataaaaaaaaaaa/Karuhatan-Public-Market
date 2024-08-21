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
      if ($row["userRole"] === 'prospective-tenant') {

      $stmt_initial_load = $conn->prepare("SELECT emailAddress FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
      }

      $stmt_initial_load->close();

      $stmt_get_notifications = $conn->prepare("SELECT * FROM tbl_notifications WHERE user_id = ? ORDER BY action_date_time DESC");
      $stmt_get_notifications->bind_param("i", $userID);
      $stmt_get_notifications->execute();
      $result_get_notifications = $stmt_get_notifications->get_result();
          
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/prospective-tenant-notifications-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="prospective-tenant-dashboard.php">
          <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link nav-text" href="prospective-tenant-dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                  <div class="m-0 p-0" style="position: relative">
                    <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownApplicationsLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Applications</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownApplicationsLink">
                        <li>
                          <a class="dropdown-item nav-text" href="prospective-tenant-applications.php">Apply</a>
                        </li>
                        <li>
                          <a class="dropdown-item nav-text" href="prospective-tenant-my-applications.php">My Applications</a>
                        </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-text" href="prospective-tenant-guide-map.php">Guide Map</a>
                </li>
                <li class="nav-item" id="hidden-nav">
                  <a class="nav-link nav-text" href="prospective-tenant-notifications.php">Notifications</a>
                </li>
                <li class="nav-item dropdown" id="hidden-nav">
                  <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?>&nbsp;</a>
                  <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <li>
                        <a class="dropdown-item nav-text" href="prospective-tenant-profile.php">Profile</a>
                      </li>
                      <li>
                        <a class="dropdown-item nav-text" href="prospective-tenant-change-password.php">Change Password</a>
                      </li>
                      <li>
                        <a class="dropdown-item nav-text" href="php/logout.php">Log Out</a>
                      </li>
                  </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center justify-content-center" id="hidden-side-nav">
                <div class="me-5 nav-text">
                    <a href="prospective-tenant-notifications.php">
                      <i class="fa-solid fa-bell" style="color: #283891"></i>
                    </a>
                </div>
                <div class="dropdown ms-6">
                    <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?></button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item nav-text" href="prospective-tenant-profile.php">Profile</a></li>
                        <li><a class="dropdown-item nav-text" href="prospective-tenant-change-password.php">Change Password</a></li>
                        <li><a class="dropdown-item nav-text" href="php/logout.php">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  </nav>

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center mb-5" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
        <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
            <h2 class="display-3 switzer-bold-text-white">Notifications</h2>
        </div>
    </div>

    <div class="container py-9 px-5">

    <?php
        if($result_get_notifications->num_rows > 0) {
          while($row = $result_get_notifications->fetch_assoc()) {
            echo '<div class="row py-3 align-items-center mb-4" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">';
              echo '<div class="col-md-6">';
                echo '<h3 class="switzer-semibold-text display-0 ms-4">';
                switch ($row["action"]) {
                  case 'submitTRA':
                    echo '<i class="fa-solid fa-paper-plane" style="color:#283891"></i>&nbsp;&nbsp;&nbsp;Application Sent';
                    break;

                  case 'approvedTRA':  
                    echo '<i class="fa-solid fa-file-circle-check" style="color:#4CD964"></i>&nbsp;&nbsp;&nbsp;Application Approved';
                    break;

                  case 'rejectedTRA':
                    echo '<i class="fa-solid fa-file-circle-xmark" style="color:#FC0E55"></i>&nbsp;&nbsp;&nbsp;Application Rejected';
                    break;
                }
                echo '</h3>';
              echo '</div>';

              echo '<div class="col-md-6 d-flex align-items-center res-justify-date">';
                $action_date_time = $row["action_date_time"];
                $timestamp = strtotime($action_date_time);
                $formatted_time = date("F j, Y g:ia", $timestamp);
                echo '<h3 class="switzer-medium-text display-xsm">'.$formatted_time.'</h3>';
              echo '</div>';

              echo '<div class="col-md-12 d-flex align-items-center">';
                switch ($row["action"]) {
                  case 'submitTRA':
                    echo '<h3 class="switzer-medium-text mx-4 display-xsm">Your tenant rental application has been successfully sent. Please wait for the approval.</h3>';
                    break;

                  case 'approvedTRA':  
                    echo '<h3 class="switzer-medium-text mx-4 display-xsm">Congratulations! Your tenant rental application has been approved!</h3>';
                    break;

                  case 'rejectedTRA':
                    echo '<h3 class="switzer-medium-text mx-4 display-xsm">I am sorry but your tenant rental application has been rejected.</h3>';
                    break;
                }
              echo '</div>';
            echo '</div>';

          }

        } else {
          echo '<div class="row py-5 align-items-center mb-4" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="col-12 text-center">
                      <h3 class="switzer-semibold-text display-0">No Notifications Available</h3>
                  </div>
                </div>';
        }
      ?>

    </div>

    
    <!-- <div class="container py-3 px-5 mb-1">
      <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
          <div class="col-md-6">
              <h3 class="switzer-semibold-text display-0 ms-4"><i class="fa-solid fa-circle-check" style="color: #4CD964"></i>&nbsp;&nbsp;&nbsp;Application Approved</h3>
          </div>
          <div class="col-md-6 d-flex align-items-center res-justify-date">
            <h3 class="switzer-medium-text display-xsm">00:00 AM 00/00/00</h3>
          </div>
          <div class="col-md-12 d-flex align-items-center">
            <h3 class="switzer-medium-text mx-4 display-xsm">Congratulations! Your tenant rental application has been approved! Please see attached details.</h3>
          </div>
      </div>
    </div>

    <div class="container py-3 px-5 mb-5">
      <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
          <div class="col-md-6">
              <h3 class="switzer-semibold-text display-0 ms-4"><i class="fa-solid fa-circle-xmark" style="color: #FC0E55"></i>&nbsp;&nbsp;&nbsp;Application Rejected</h3>
          </div>
          <div class="col-md-6 d-flex align-items-center res-justify-date">
            <h3 class="switzer-medium-text display-xsm">00:00 AM 00/00/00</h3>
          </div>
          <div class="col-md-12 d-flex align-items-center">
            <h3 class="switzer-medium-text mx-4 display-xsm">I am sorry but your tenant rental application has been rejected.</h3>
          </div>
      </div>
    </div> -->
    
    <footer class="text-center text-lg-start mt-8">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        </section>
      
        <section class="">
          <div class="container text-center text-md-start mt-5">
            <div class="row mt-3">
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <a href="prospective-tenant-dashboard.php">
                    <img src="../Developer things/assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid mb-3 footer-logo">
                </a>
                <p class="switzer-medium-text">Join us at the Karuhatan Public Market, where every stall offers quality goods and every visit is an opportunity to discover something new.</p>
              </div>
      
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <a href="prospective-tenant-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Apply</h6></a>
                <a href="prospective-tenant-my-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">My Applications</h6></a>
                <a href="prospective-tenant-guide-map.php" style="text-decoration: none"><h6 class="footer-text mb-4">Guide Map</h6></a>
              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="prospective-tenant-notifications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Notifications</h6></a>
                <a href="prospective-tenant-privacy-policy.php" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
                <a href="prospective-tenant-terms-and-conditions.php" style="text-decoration: none"><h6 class="footer-text mb-4">Terms and Conditions</h6></a>
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

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
