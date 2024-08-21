<?php
session_start();

include '../Developer things/php/connect.php';

if (isset($_SESSION['emailAddress'])) {
  $emailAddress = $_SESSION['emailAddress'];

  $sql = "SELECT firstName FROM user_registration WHERE emailAddress = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $emailAddress);
  $stmt->execute();
  $stmt->fetch();
  $stmt->close();

} else {
  header("Location: log-in.html");
  exit();
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

    <link rel="stylesheet" href="css/owner-notifications-style.css">
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
                <li class="nav-item">
                  <a class="nav-link nav-text" href="owner-users.php">Users</a>
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
                        <a class="dropdown-item nav-text" href="home.html">Sign Out</a>
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
                        <li><a class="dropdown-item nav-text" href="home.html">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  </nav>

  <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
    <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
        <h2 class="display-3 switzer-bold-text-white">Notification</h2>
    </div>
  </div>

  <div class="container py-3 px-5 mb-1">
    <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="col-md-6">
            <h3 class="switzer-semibold-text display-0 ms-4"><i class="fa-solid fa-circle-check" style="color: #4CD964"></i>&nbsp;&nbsp;&nbsp;Sample</h3>
        </div>
        <div class="col-md-6 d-flex align-items-center res-justify-date">
          <h3 class="switzer-medium-text display-xsm">00:00 AM 00/00/00</h3>
        </div>
        <div class="col-md-12 d-flex align-items-center">
          <h3 class="switzer-medium-text mx-4 display-xsm">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Odio, quis!</h3>
        </div>
    </div>
  </div>

  <div class="container py-3 px-5 mb-1">
    <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="col-md-6">
            <h3 class="switzer-semibold-text display-0 ms-4"><i class="fa-solid fa-circle-info" style="color: #283891"></i>&nbsp;&nbsp;&nbsp;Sample</h3>
        </div>
        <div class="col-md-6 d-flex align-items-center res-justify-date">
          <h3 class="switzer-medium-text display-xsm">00:00 AM 00/00/00</h3>
        </div>
        <div class="col-md-12 d-flex align-items-center">
          <h3 class="switzer-medium-text mx-4 display-xsm">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Doloribus, beatae.</h3>
        </div>
    </div>
  </div>

  <div class="container py-3 px-5 mb-5">
    <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="col-md-6">
            <h3 class="switzer-semibold-text display-0 ms-4"><i class="fa-solid fa-circle-xmark" style="color: #FC0E55"></i>&nbsp;&nbsp;&nbsp;Sample</h3>
        </div>
        <div class="col-md-6 d-flex align-items-center res-justify-date">
          <h3 class="switzer-medium-text display-xsm">00:00 AM 00/00/00</h3>
        </div>
        <div class="col-md-12 d-flex align-items-center">
          <h3 class="switzer-medium-text mx-4 display-xsm">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum, rem!</h3>
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

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
