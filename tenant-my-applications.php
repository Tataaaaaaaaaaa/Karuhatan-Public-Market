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
      if ($row["userRole"] === 'tenant') {

      $stmt_initial_load = $conn->prepare("SELECT emailAddress FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
      }

      $stmt_initial_load->close();

      $stmt_my_applications = $conn->prepare("SELECT * FROM tenant_rental_application WHERE user_registration_id = ?");
      $stmt_my_applications->bind_param("i", $userID);
      $stmt_my_applications->execute();
      $result_my_applications = $stmt_my_applications->get_result();
          
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

    <link rel="stylesheet" href="css/tenant-applications-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg-tenant navbar-dark bg-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="tenant-dashboard.php">
              <img src="assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid" id="kpmLogo"/>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="tenant-dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="tenant-rental-payment.php">Payment</a>
                    </li>
                    <li class="nav-item dropdown">
                      <div class="m-0 p-0" style="position: relative">
                        <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownApplicationsLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Applications</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownApplicationsLink">
                            <li>
                              <a class="dropdown-item nav-text" href="tenant-applications.php">Apply</a>
                            </li>
                            <li>
                              <a class="dropdown-item nav-text" href="tenant-my-applications.php">My Applications</a>
                            </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link nav-text" href="tenant-document.php">Document</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-text" href="tenant-guide-map.php">Guide Map</a>
                    </li>
                    <li class="nav-item" id="hidden-nav">
                      <a class="nav-link nav-text" href="tenant-notifications.php">Notifications</a>
                    </li>
                    <li class="nav-item dropdown" id="hidden-nav">
                      <a class="nav-link nav-text dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?>&nbsp;</a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                          <li>
                            <a class="dropdown-item nav-text" href="tenant-profile.php">Profile</a>
                          </li>
                          <li>
                            <a class="dropdown-item nav-text" href="tenant-change-password.php">Change Password</a>
                          </li>
                          <li>
                            <a class="dropdown-item nav-text" href="php/logout.php">Log Out</a>
                          </li>
                      </ul>
                    </li>
                </ul>
                <div class="d-flex align-items-center justify-content-center" id="hidden-side-nav">
                    <div class="me-5 nav-text">
                        <a href="tenant-notifications.php">
                          <i class="fa-solid fa-bell" style="color: #283891"></i>
                        </a>
                    </div>
                    <div class="dropdown ms-6">
                        <button class="btn nav-text dropdown-toggle" style="border: none; padding-left: 5px" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $emailAddress; ?></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item nav-text" href="tenant-profile.php">Profile</a></li>
                            <li><a class="dropdown-item nav-text" href="tenant-change-password.php">Change Password</a></li>
                            <li><a class="dropdown-item nav-text" href="php/logout.php">Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
        <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
            <h2 class="display-3 switzer-bold-text-white">My Applications</h2>
        </div>
    </div>

    
    <div class="container py-8 px-5">
    <?php
        if($result_my_applications->num_rows > 0) {
          while($row = $result_my_applications->fetch_assoc()) {
            echo '<div class="row py-3 align-items-center mb-2" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">';
              echo '<div class="col-md-6">';
                echo '<h3 class="switzer-semibold-text display-0 ms-4">';
                  
                  switch ($row["applicationStatus"]) {
                    case 'Pending':
                      echo '<i class="fa-regular fa-hourglass-half" style="color:#283891"></i>&nbsp;&nbsp;&nbsp;Pending Application';
                      break;

                    case 'Approved':  
                      echo '<i class="fa-solid fa-file-circle-check" style="color:#4CD964"></i>&nbsp;&nbsp;&nbsp;Approved Application';
                      break;

                    case 'Rejected':
                      echo '<i class="fa-solid fa-file-circle-xmark" style="color:#FC0E55"></i>&nbsp;&nbsp;&nbsp;Rejected Application';
                      break;
                  }

                echo '</h3>';
              echo '</div>';

              echo '<div class="col-md-6 d-flex align-items-center res-justify-date">';
                  $submission_time = $row["submission_time"];
                  $timestamp = strtotime($submission_time);
                  $formatted_time = date("F j, Y g:ia", $timestamp);

                echo '<h3 class="switzer-medium-text display-xsm">'.$formatted_time.'</h3>';
                
              echo '</div>';

              echo '<div class="col-md-12 d-flex align-items-center">';
                echo '<h3 class="switzer-medium-text mx-4 display-xsm mb-0">Name: '.$row["firstName"].' '.$row["lastName"].'</h3>';
              echo '</div>';

              echo '<div class="col-md-12 d-flex align-items-center">';
                echo '<h3 class="switzer-medium-text mx-4 display-xsm mb-0">Stall Category: '.$row["stallCategory"].'</h3>';
              echo '</div>';

              echo '<div class="col-md-12 d-flex align-items-center">';

                if (empty($row["preferredStall"])) {
                  echo '<h3 class="switzer-medium-text mx-4 display-xsm mb-0">Preferred Stall: None</h3>';

                } else {
                  echo '<h3 class="switzer-medium-text mx-4 display-xsm mb-0">Preferred Stall: '.$row["preferredStall"].'</h3>';
                }
              echo '</div>';

              echo '<div class="col-md-12 d-flex align-items-center justify-content-end mt-3">';
                echo '<div class="d-grid gap-2 d-sm-flex justify-content-sm-end">';
                  echo '<a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" href="tenant-my-applications-view.php?application_id='.$row["application_id"].'" role="button">View</a>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
          }
        } else {

          echo '<div class="row py-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="col-md-6">
                      <h3 class="switzer-semibold-text display-0 mx-4 mb-5">No Submitted Applications Yet<br>
                        <span class="display-0 switzer-medium-text">Want to apply?</span>
                      </h3>
                  </div>
                  <div class="col-md-6 d-flex align-items-center custom-text-center">
                      <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                          <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" href="tenant-applications.php" role="button">Apply Now</a>
                      </div>
                  </div>
              </div>';

        }
      ?>
    </div>
    
    <footer class="text-center text-lg-start">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        </section>
        <section class="">
          <div class="container text-center text-md-start mt-5">
            <div class="row mt-3">
              <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <a href="tenant-dashboard.php">
                    <img src="../Developer things/assets/img/Karuhatan Public Market Logo.png" alt="Karuhatan Public Market Logo" class="img-fluid mb-3 footer-logo">
                </a>
                <p class="switzer-medium-text">Join us at the Karuhatan Public Market, where every stall offers quality goods and every visit is an opportunity to discover something new.</p>
              </div>
      
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                <a href="tenant-rental-payment.php" style="text-decoration: none"><h6 class="footer-text mb-4">Payment</h6></a>
                <a href="tenant-document.php" style="text-decoration: none"><h6 class="footer-text mb-4">Document</h6></a>
                <a href="tenant-guide-map.php" style="text-decoration: none"><h6 class="footer-text mb-4">Guide Map</h6></a>
              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="tenant-notifications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Notifications</h6></a>
                <a href="tenant-privacy-policy.php" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
                <a href="tenant-terms-and-conditions.php" style="text-decoration: none"><h6 class="footer-text mb-4">Terms and Conditions</h6></a>
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
