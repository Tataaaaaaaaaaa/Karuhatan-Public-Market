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

      $stmt_initial_load = $conn->prepare("SELECT emailAddress, occupiedStall, gcashFile, merchantName, gcashPhoneNumber, outstandingBalance FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
        $occupiedStall = $row_initial_load['occupiedStall'];
        $gcashFile = $row_initial_load['gcashFile'];
        $merchantName = $row_initial_load['merchantName'];
        $gcashPhoneNumber = $row_initial_load['gcashPhoneNumber'];
        $outstandingBalance = $row_initial_load['outstandingBalance'];
      }

      $stmt_initial_load->close();

          
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

    <link rel="stylesheet" href="css/tenant-rental-setup-payment-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
    <style>
      
    </style>
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

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Payment (Rental)</h2>
      </div>
    </div>

    <?php
      if (!empty($merchantName) && !empty($gcashPhoneNumber) && !empty($gcashFile)) {
        $gcashFileData = base64_encode($gcashFile);
        echo '<div class="container py-7 px-0">
                <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="col-12">
                    <h3 class="switzer-bold-text display-6 mb-2"><i onclick="redirectPaymentMethod()" class="fa-solid fa-chevron-left" style="cursor: pointer"></i>&nbsp;&nbsp;&nbsp;Upload GCash Receipt:</h3>
                    <div class="welcome-name-style mb-2"></div>
                    <h3 class="switzer-bold-text display-0">Outstanding Balance:&nbsp;&nbsp;&nbsp;<span><i class="fa-solid fa-peso-sign" style="color:#283891"></i></span>'.$outstandingBalance.'</h3>
                  </div>
                  <div class="col-12">
                    <h3 class="switzer-bold-text display-0">Merchant:&nbsp;&nbsp;&nbsp;'.$merchantName.'</h3>
                  </div>
                  <div class="col-12">
                    <h3 class="switzer-bold-text display-0">Phone Number:&nbsp;&nbsp;&nbsp;'.$gcashPhoneNumber.'</h3>
                  </div>
                  <div class="grid">
                    <label class="card mb-2">
                      <span class="plan-details" style="background-color: #015de6">
                        <div class="row">
                          <div class="col-12 mb-3 text-center">
                            <img class="img-gcash-resize" src="data:image/jpeg;base64,'.$gcashFileData.'" alt="GCash QR Code">
                          </div>
                          <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                          <form id="uploadReceiptForm" method="post" enctype="multipart/form-data" class="d-flex justify-content-center">
                              <label class="btn btn-get-started btn-lg px-4 gap-3 switzer-semibold-text" for="receipt" id="uploadLabel">Upload Receipt</label>
                              <input type="file" id="receipt" name="receipt" style="display: none;" accept=".jpg, .jpeg, .png" onchange="updateLabelText()">
                          </div>
                        </div>
                        <br>
                      </span>
                    </label>
                  </div>
                  <div class="col-lg-12 d-flex res-justify-content px-3 mt-4">
                    <button class="px-6 btn btn-lg btn-signup btn-login mb-2" type="submit">Pay</button> 
                  </div>
                  </form>
                </div>
              </div>';
      } else {
        echo '<div class="container py-7 px-0">
                <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="col-12">
                    <h3 class="switzer-bold-text display-6 mb-2"><i onclick="redirectPaymentMethod()" class="fa-solid fa-chevron-left" style="cursor: pointer"></i>&nbsp;&nbsp;&nbsp;Upload GCash Receipt:</h3>
                    <div class="welcome-name-style mb-2"></div>
                    <h3 class="switzer-bold-text display-0">To Pay:&nbsp;&nbsp;&nbsp;<span><i class="fa-solid fa-peso-sign" style="color:#283891"></i></span>'.$dailyRentalPayment.'</h3>
                  </div>
                  <div class="col-12">
                    <h3 class="switzer-bold-text display-0">Merchant:&nbsp;&nbsp;&nbsp;Not Set</h3>
                  </div>
                  <div class="col-12">
                    <h3 class="switzer-bold-text display-0">Phone Number:&nbsp;&nbsp;&nbsp;Not Set</h3>
                  </div>
                  <div class="grid">
                    <label class="card mb-2">
                      <span class="plan-details" style="background-color: #015de6">
                        <div class="row">
                          <div class="col-12 mb-3 text-center">
                            <img class="img-gcash-resize" src="../Developer things/assets/img/gcash-not-set.png" alt="GCash QR Code Not Set">
                          </div>
                        </div>
                        <br>
                      </span>
                    </label>
                  </div>
                </div>
              </div>';
      }
    ?>
    
    

    <div class="modal fade" tabindex="-1" role="dialog" id="invalidInputModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Payment</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Please upload your GCash receipt and try again.</h4>
              <h4 class="switzer-medium-text display-sm custom-px-4" id="dynamicText"></h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="successModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(76,217,100,0.5); color: #4CD964"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Payment Successful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Your have successfully sent your GCash receipt! Please wait for processing.</h4>
            </div>
            <div class="py-1"><button type="button" class="btn btn-lg btn-outline-success rounded-pill px-5" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button></div>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">Unfortunately we encountered an issue. Please try again later.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
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

    <script>
      uploadReceiptForm.addEventListener("submit", function(event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append("userID", "<?php echo $userID; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/gcash-upload-payment-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successModal').modal('show');
                    } else {
                      if (response.errors.includes("Error: No upload.")) {
                        $('#invalidInputModal').modal('show');
                      } else {
                        $('#failModal').modal('show');
                      }
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
      document.addEventListener("DOMContentLoaded", function() {
        var successModal = document.getElementById('successModal');
        var continueButton = document.getElementById('continueButton');

        successModal.addEventListener('hidden.bs.modal', function() {
          window.location.href = "tenant-dashboard.php";
        });

        continueButton.addEventListener('click', function() {
          window.location.href = "tenant-dashboard.php";
        });
      });
    </script>
      

    <script>
      function updateLabelText() {
        var fileInput = document.getElementById('receipt');
        var label = document.getElementById('uploadLabel');
        label.textContent = fileInput.files[0].name;
      }

      function redirectPaymentMethod() {
        location.replace("tenant-rental-payment-method.php");
      }
    </script>
</body>
</html>
