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

      $stmt_initial_load = $conn->prepare("SELECT firstName, emailAddress FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $firstName = $row_initial_load['firstName'];
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/prospective-tenant-dashboard-style.css">
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

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
        <div class="container-fluid px-4 custom-py-5 custom-my-5 custom-col-8">
            <h2 class="display-3 switzer-bold-text-white mb-4">
              <span class="welcome-name-style">Welcome, <?php echo $firstName; ?></span>
            </h2>
            <?php
              if($result_my_applications->num_rows > 0) {
                echo "<p class='custom-display-0 about-us-text mb-6'>You have submitted an application<br>Check it out</p>";
              } else {
                echo "<p class='custom-display-0 about-us-text mb-6'>You haven't submitted any applications yet<br>Apply to occupy a stall</p>";
              }
            ?>
            <?php
              if($result_my_applications->num_rows > 0) {
                echo '<div class="d-grid gap-2 d-sm-flex justify-content-sm-end"> <a class="btn btn-get-started btn-lg px-5 gap-3 switzer-semibold-text" href="prospective-tenant-my-applications.php" role="button">Visit</a></div>';
              } else {
                echo '<div class="d-grid gap-2 d-sm-flex justify-content-sm-end"> <a class="btn btn-get-started btn-lg px-5 gap-3 switzer-semibold-text" href="prospective-tenant-applications.php" role="button">Apply Now</a></div>';
              }
            ?>
        </div>
    </div>

    <div class="container py-7 px-5">
        <div class="row py-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
            <div class="col-md-6">
                <h3 class="switzer-semibold-text display-0 mx-4 mb-5">Interested on what stalls you can occupy?<br>
                  <span class="display-0 switzer-medium-text">See available stalls here</span>
                </h3>
            </div>
            <div class="col-md-6 d-flex align-items-center custom-text-center">
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                    <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" href="prospective-tenant-guide-map.php" role="button">View Map</a>
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
