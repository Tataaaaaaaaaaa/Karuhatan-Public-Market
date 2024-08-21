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

      $stmt_initial_load = $conn->prepare("SELECT emailAddress FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
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

    <link rel="stylesheet" href="css/owner-users-style.css">
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
                    <a class="nav-link nav-text" href="owner-stall-list.php">Stalls</a>
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
          <h2 class="display-3 switzer-bold-text-white">Users</h2>
      </div>
    </div>

    <div class="container py-7 px-0">
      <div class="py-5 px-5 mx-auto" style="max-width: 1700px; box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
          <div class="dropdown mb-1">
            <button class="btn btn-signup dropdown-toggle" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                All
            </button>
            <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" id="paidFilter">All</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" id="unpaidFilter">Paid</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" id="allFilter">Unpaid</a></li>
            </ul>
          </div>
          <table id="example" class="table" style="width:100%">
              <thead>
                  <tr class="display-sm">
                      <th>Role</th>
                      <th>Name</th>
                      <th>Email Address</th>
                      <th>Stall</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
                <tr class="paid">
                    <td>Collector</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>-----</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                   <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="paid">
                    <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                    <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="paid">
                    <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                    <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="paid">
                    <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                  <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
              </tr>
              <tr class="paid">
                  <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
              </tr>
              <tr class="unpaid">
                  <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
              </tr>
              <tr class="paid">
                 <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
              </tr>
              <tr class="unpaid">
                  <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
              </tr>
              <tr class="paid">
                  <td>Tenant</td>
                    <td>Juan Dela Cruz</td>
                    <td>juandelacruz@gmail.com</td>
                    <td>PX-XX</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Edit</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Delete</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Change Password</a></li>
                          </ul>
                      </div>
                  </td>
              </tr>
            </tbody>
        </table>
        </div>
    </div>

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
                <a href="owner-users.php" style="text-decoration: none"><h6 class="footer-text mb-4">Users</h6></a>
                <a href="owner-stall-list.php" style="text-decoration: none"><h6 class="footer-text mb-4">Stalls</h6></a>
                <a href="owner-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Applications</h6></a>

              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="privacy-policy.html" style="text-decoration: none"><h6 class="footer-text mb-4">Privacy Policy</h6></a>
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
          <a class="switzer-semibold-text-white" href="#" style="text-decoration: none">Karuhatan Public Market</a>
        </div>
    </footer>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

    <script>
      $(document).ready(function() {
        $(".statusFilter").click(function(){
            var selectedOption = $(this).text();
            $("#statusFilterDropdown").text(selectedOption);
        });
      });

      $(document).ready(function() {
        $(".statusFilter").click(function(event){
            event.preventDefault();
            var selectedOption = $(this).text();
            $("#statusFilterDropdown").text(selectedOption);
            filterRows(selectedOption);
        });

        function filterRows(option) {
            if (option === "All") {
                $("tbody tr").show();
            } else {
                $("tbody tr").hide();
                $("." + option.toLowerCase()).show();
            }
        }
    });

    $(document).ready(function() {
        var table = $('#example').DataTable({            
            "searching": false,
            
            "ordering": false
        });
    });
    </script>
</body>
</html>
