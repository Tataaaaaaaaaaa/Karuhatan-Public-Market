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

      $stmt_initial_load = $conn->prepare("SELECT emailAddress, occupiedPhase FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
        $occupiedPhase = $row_initial_load['occupiedPhase'];
      }

      $stmt_initial_load->close();

      $tenant_id = $_GET['user_registration_id'];

      $stmt_tenants = $conn->prepare("SELECT * FROM user_registration WHERE userRole = 'tenant' AND occupiedPhase = ?");
      $stmt_tenants->bind_param("s", $occupiedPhase);
      $stmt_tenants->execute();
      $result_tenants = $stmt_tenants->get_result();

      $stmt_outstanding_balance = $conn->prepare("SELECT firstName, middleName, lastName, occupiedStall, outstandingBalance FROM user_registration WHERE user_registration_id = ?");
      $stmt_outstanding_balance->bind_param("i", $tenant_id);
      $stmt_outstanding_balance->execute();
      $result_outstanding_balance = $stmt_outstanding_balance->get_result();

      if ($result_outstanding_balance->num_rows > 0) {
        $row_outstanding_balance = $result_outstanding_balance->fetch_assoc();
        $firstName = $row_outstanding_balance['firstName'];
        $middleName = $row_outstanding_balance['middleName'];
        $lastName = $row_outstanding_balance['lastName'];
        $occupiedStall = $row_outstanding_balance['occupiedStall'];
        $outstandingBalance = $row_outstanding_balance['outstandingBalance'];
      }
          
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

    <link rel="stylesheet" href="css/collector-tenant-list-style.css">
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
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Outstanding Balance</h2>
      </div>
    </div>

    <div class="container py-7">
      <div class="row justify-content-between">
        <?php
          if(empty($outstandingBalance)) {
            $outstandingBalance = 0;
          }

          echo '<form action="" method="POST" id="outstandingBalanceForm" autocomplete="off" class="col-md-12 mb-5">
                  <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                    <div class="row d-flex align-items-center">
                      <div class="col-md-6">
                        <h3 class="switzer-bold-text display-6 mx-4">'.$firstName.' '.$middleName.' '.$lastName.'</h3>
                      </div>
                      <div class="col-md-6 d-flex align-items-center custom-text-center_2">
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                          <h3 class="switzer-bold-text display-6 mx-4" id="displayOutstandingBalance"><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;'.$outstandingBalance.'</h3>
                          <input name="outstandingBalance" type="text" class="form-control form-input-style custom-mx-4" id="outstandingBalance" pattern="[0-9]+(?:\.[0-9]+)?" title="Please enter a valid amount." placeholder="" required autocomplete="off" style="display:none">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <h3 class="switzer-bold-text display-6 mx-4">Stall: '.$occupiedStall.'</h3>
                      </div>
                      <div class="col-md-6 d-flex align-items-center custom-text-center_2">
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                          
                        </div>
                      </div>
                      <div class="col-md-6">
                      </div>
                      <div class="col-md-6 d-flex align-items-center custom-text-center_2">
                      <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" id="editBtn_2">Edit</a>
                      </div>
                    </div>
                    <div class="col-lg-12 d-flex custom-text-center_2 px-3">
                      <button class="btn btn-green btn-lg px-5 switzer-semibold-text-white btn-widen" id="saveBtn_2" style="display: none" type="submit">Save</button>
                      <a class="btn btn-red btn-lg px-5 switzer-semibold-text-white custom-mx-btn btn-widen" id="cancelBtn_2" role="button" style="display: none">Cancel</a>
                    </div>
                    </div>
                  </div>
                </form>';
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

    <div class="modal fade" tabindex="-1" role="dialog" id="reminderSuccessModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="color: #4CD964;"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Reminder Sent</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">A reminder for the tenant to pay the stall rent has been successfully sent.</h4>
            </div>
            <div class="py-1"><button type="button" class="btn btn-lg btn-outline-success rounded-pill px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button></div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="reminderFailModal">
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

    <div class="modal fade" tabindex="-1" role="dialog" id="successModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(76,217,100,0.5); color: #4CD964"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Changes Made</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The outstanding balance has been updated successfully</h4>
            </div>
            <div class="d-flex justify-content-center py-1">
              <button type="button" class="btn btn-lg btn-outline-success rounded-pill me-2 px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button>
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

    <script>
      document.getElementById("editBtn_2").addEventListener("click", function() {
          document.getElementById("editBtn_2").style.display = "none";
          document.getElementById("saveBtn_2").style.display = "block";
          document.getElementById("cancelBtn_2").style.display = "block";
          document.getElementById("outstandingBalance").style.display = "block";
          document.getElementById("displayOutstandingBalance").style.display = "none";
      });

       document.getElementById("cancelBtn_2").addEventListener("click", function() {
          document.getElementById("editBtn_2").style.display = "block";
          document.getElementById("saveBtn_2").style.display = "none";
          document.getElementById("cancelBtn_2").style.display = "none";
          document.getElementById("outstandingBalance").style.display = "none";
          document.getElementById("displayOutstandingBalance").style.display = "block";

          var outstandingBalanceForm = document.getElementById("outstandingBalanceForm");
          outstandingBalanceForm.reset();
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        var successModal = document.getElementById('successModal');
        var continueButton = document.getElementById('continueButton');

        successModal.addEventListener('hidden.bs.modal', function() {
          window.location.href="collector-tenant-outstanding-balance.php?user_registration_id=<?php echo $tenant_id; ?>"
        });

        continueButton.addEventListener('click', function() {
          window.location.href="collector-tenant-outstanding-balance.php?user_registration_id=<?php echo $tenant_id; ?>"
        });
      });
    </script>

<script>  
      document.getElementById("outstandingBalanceForm").addEventListener("submit", function(event) {
        event.preventDefault();
    
        var formData = new FormData(this);
        formData.append("tenant_id", "<?php echo $tenant_id; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/outstanding-balance-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successModal').modal('show');
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
      document.addEventListener('DOMContentLoaded', function() {
        var remindLinks = document.querySelectorAll('.remind-link');
        
        remindLinks.forEach(function(link) {
          link.addEventListener('click', function(event) {
            event.preventDefault();
            
            var userId = this.getAttribute('data-user-id');
            
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../Developer things/php/notify-remind-tenant-connect.php?user_registration_id=' + userId, true);
            xhr.onreadystatechange = function() {
              if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                  $('#reminderSuccessModal').modal('show');
                } else {
                  $('#reminderFailModal').modal('show');
                }
              }
            };
            xhr.send();
          });
        });
      });
    </script>

    <script>
    $(document).ready(function() {
        var table = $('#tenantsTable').DataTable({            
            "searching": false,
            
            "ordering": false
        });
    });
    </script>
</body>
</html>
