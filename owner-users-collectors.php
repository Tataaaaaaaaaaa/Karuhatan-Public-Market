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
        $emailAddress = $row_initial_load['emailAddress'];
        $occupiedPhase = $row_initial_load['occupiedPhase'];
      }

      $stmt_initial_load->close();

      $stmt_collector = $conn->prepare("SELECT * FROM user_registration WHERE userRole = 'collector' AND occupiedPhase = ?");
      $stmt_collector->bind_param("s", $occupiedPhase);
      $stmt_collector->execute();
      $result_collector = $stmt_collector->get_result();
          
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

    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/owner-users-collector-style.css">
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
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center-header">
          <h2 class="display-3 switzer-bold-text-white">Collectors</h2>
      </div>
    </div>

    <?php
      if ($result_collector->num_rows > 0) {
        echo '<div class="container py-7 px-0">
              <div class="py-5 px-5 mx-auto" style="max-width: 1700px; box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                <div class="d-flex justify-content-start mb-1">
                  <div>
                      <button class="btn btn-signup" type="button" id="addCollectorBtn">
                        Add
                      </button>
                    </div>
                </div>
                  <table id="collectorsTable" class="table" style="width:100%">
                      <thead>
                          <tr class="display-sm">
                              <th>Name</th>
                              <th>Email Address</th>
                              <th>Phone Number</th>
                              <th style="text-align: center">Actions</th>
                          </tr>
                      </thead>
                      <tbody>';
                        while ($row_collectors = $result_collector->fetch_assoc()) {
                          echo '<tr>';
                          echo '<td>' . $row_collectors["firstName"] . ' ' . $row_collectors["middleName"] . ' ' . $row_collectors["lastName"] . '</td>';
                          echo '<td>' . $row_collectors["emailAddress"] . '</td>';
                          echo '<td>' . $row_collectors["phoneNumber"] . '</td>';
                          echo '<td>
                                  <div class="dropdown">
                                      <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                          Select
                                      </button>
                                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                          <li><a class="dropdown-item switzer-semibold-text" href="owner-users-collectors-view.php?user_registration_id='.$row_collectors["user_registration_id"].'">View</a></li>
                                          <li><a class="dropdown-item switzer-semibold-text remind-link" href="#" data-user-id="'.$row_collectors["user_registration_id"].'">Remind</a></li>
                                          <li><a class="dropdown-item switzer-semibold-text toggleDelete" href="" data-bs-toggle="modal" data-bs-target="#deleteModal" data-user-id="'.$row_collectors["user_registration_id"].'">Delete</a></li>
                                      </ul>
                                  </div>
                              </td>';
                        echo '</tr>';
                        }
                echo '</tbody>
                </table>
              </div>
          </div>';
      } else {
        echo '<div class="container py-9 px-5">
                <div class="row py-5 align-items-center mb-4" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
                  <div class="col-md-6">
                    <h3 class="switzer-semibold-text display-0 mx-4 mb-4">No Collectors Yet<br>
                      <span class="display-0 switzer-medium-text">Want to add?</span>
                    </h3>
                  </div>
                  <div class="col-md-6 d-flex align-items-center custom-text-center">
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                      <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" href="owner-users-collectors-add.php" role="button">Add Collector</a>
                    </div>
                  </div>
                </div>
              </div>';
      }
    ?>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="successModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="color: #4CD964;"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Reminder Sent</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">A rental payment notification has been sent to Juan Dela Cruz</h4>
            </div>
            <div class="py-1"><button type="button" class="btn btn-lg btn-outline-success rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Continue</button></div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="reminderSuccessModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div id="checkIcon">
              <i class="fa-solid fa-circle-check" style="color: #4CD964;"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Reminder Sent</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">A reminder for the collector to collect the stall rent payments has been successfully sent.</h4>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="detailModal">
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
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Are you sure?</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">This action cannot be undone. All data associated with this record will be lost.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="btn btn-lg btn-outline-secondary rounded-pill px-5" id="cancelButton" style="font-family: 'Switzer-Semibold'" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" id="confirmDeleteButton" style="font-family: 'Switzer-Semibold'">Delete</button>
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

    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var addCollectorBtn = document.getElementById('addCollectorBtn');
      
      addCollectorBtn.addEventListener('click', function() {
        window.location.href = 'owner-users-collectors-add.php';
      });
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
            xhr.open('GET', '../Developer things/php/notify-remind-collector-connect.php?user_registration_id=' + userId, true);
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
        document.addEventListener('DOMContentLoaded', function() {
            const deleteIcons = document.querySelectorAll('.toggleDelete');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');
            let userIdToDelete = null;

            deleteIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    userIdToDelete = this.getAttribute('data-user-id');
                });
            });

            confirmDeleteButton.addEventListener('click', function() {
                if (userIdToDelete) {
                    window.location.href = '../Developer things/php/delete-collector.php?user_id=' + userIdToDelete;
                }
            });
        });
    </script>

    <script>
    $(document).ready(function() {
        var table = $('#collectorsTable').DataTable({            
            "searching": false,
            
            "ordering": false
        });
    });
    </script>
</body>
</html>
