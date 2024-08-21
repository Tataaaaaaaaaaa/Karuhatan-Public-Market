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

      $stmt_initial_load = $conn->prepare("SELECT firstName, emailAddress, occupiedPhase, occupiedStall, outstandingBalance FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $firstName = $row_initial_load['firstName'];
        $emailAddress = $row_initial_load['emailAddress'];
        $occupiedPhase = $row_initial_load['occupiedPhase'];
        $occupiedStall = $row_initial_load['occupiedStall'];
        $outstandingBalance = $row_initial_load['outstandingBalance'];
      }

      $stallDisplay = !empty($occupiedStall) ? substr($occupiedStall, 3) : 'Not Set';

      $stmt_initial_load->close();

      $phaseNumber;
      switch ($occupiedPhase) {
        case 'phaseOne';
          $phaseNumber = '1';
          break;
        case 'phaseTwo';
          $phaseNumber = '2';
          break;
        case 'phaseThree';
          $phaseNumber = '3';
          break;
        case 'phaseFour';
          $phaseNumber = '4';
          break;
      }

      $stmt_get_stall_payment = $conn->prepare("SELECT daily_rental_payment FROM tbl_stalls WHERE stall_name = ?");
      $stmt_get_stall_payment->bind_param("s", $occupiedStall);
      $stmt_get_stall_payment->execute();
      $result_get_stall_payment = $stmt_get_stall_payment->get_result();

      $row_get_stall_payment = $result_get_stall_payment->fetch_assoc();
      $dailyRentalPayment = $row_get_stall_payment['daily_rental_payment'];

      $sql = "SELECT payment_id, datetime_sent, status, paymentMethod
        FROM tbl_payment 
        WHERE tenant_id = ?
        ORDER BY datetime_sent DESC 
        LIMIT 7";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $userID);
      $stmt->execute();
      $result_recent_payments = $stmt->get_result();
          
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

    <link rel="stylesheet" href="css/tenant-dashboard-style.css">
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
        <div class="container-fluid px-4 custom-py-5 custom-my-5 custom-col-8">
            <h2 class="display-3 switzer-bold-text-white mb-4">
              <span class="welcome-name-style">Welcome, <?php echo $firstName; ?></span>
            </h2>
            <p class="display-sm about-us-text mb-7">Outstanding Balance</p>
            <p class="display-5 switzer-bold-text-white ms-4"><i class="fa-solid fa-peso-sign" style="color: white"></i>&nbsp;<?php echo $outstandingBalance; ?></p>
            <p class="display-sm switzer-medium-text-white mb-1"><i class="fa-solid fa-money-bill-wave" style="color: white"></i>&nbsp;&nbsp;Daily Rental Payment: <i class="fa-solid fa-peso-sign" style="color: white"></i>&nbsp;<?php echo $dailyRentalPayment; ?></p>
            <p class="display-sm switzer-medium-text-white mb-5"><i class="fa-solid fa-store" style="color: white"></i>&nbsp;&nbsp;Phase <?php echo $phaseNumber; ?> - Stall <?php echo $stallDisplay; ?></p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end"> <a class="btn btn-submit btn-lg px-5 gap-3 switzer-semibold-text-white" href="tenant-rental-payment.php" role="button">Make a Payment</a></div>

        </div>
    </div>

    <div class="container py-7 px-0">
      <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
          <div class="col-12">
              <h3 class="switzer-bold-text display-6 mb-1">Recent Rental Payments</h3>
          </div>
          <div class="dropdown mt-3 mb-1">
            <button class="btn btn-signup" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                All
            </button>
            <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="all">All</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="pending">Pending</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="paid">Paid</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="unpaid">Unpaid</a></li>
            </ul>
          </div>
          <table id="recentRentalPaymentsTable" class="table" style="width:100%">
              <thead>
                  <tr class="display-sm">
                      <th>Status</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Payment Method</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  if ($result_recent_payments->num_rows > 0) {
                    while ($row = $result_recent_payments->fetch_assoc()) {
                      echo "<tr class='status-" . strtolower($row["status"]) . "'>";
                      switch($row["status"]) {
                        case 'pending':
                          echo '<td><i class="fa-solid fa-circle" style="color: #ffea00"></i>&nbsp;&nbsp;Pending</td>';
                          break;
                        case 'paid':
                          echo '<td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>';
                          break;
                        case 'unpaid':
                          echo '<td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>';
                          break;
                      }
                      echo '<td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;'.$dailyRentalPayment.'</td>';
                        $datetime_sent = $row["datetime_sent"];
                        $formatted_datetime = date("Y-m-d H:i:s", strtotime($datetime_sent));
                      echo '<td>'.$formatted_datetime.'</td>';
                      echo '<td>'.(!empty($row["paymentMethod"]) ? $row["paymentMethod"] : "--").'</td>';
                      echo '</tr>';
                    }
                  } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>No Payments Yet</td></tr>";
                  }
                ?>


                <!-- <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button" data-bs-toggle="modal" data-bs-target="#detailModal">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>----</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                      </div>
                    </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button" data-bs-toggle="modal" data-bs-target="#detailModal">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>----</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                      </div>
                    </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button" data-bs-toggle="modal" data-bs-target="#detailModal">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>----</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Pay</button> 
                      </div>
                    </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button" data-bs-toggle="modal" data-bs-target="#detailModal">Details</button> 
                      </div>
                    </td>
                </tr> -->
            </tbody>
        </table>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="detailModal">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <div class="py-2">
              <div class="col-md-12">
                <p class="px-4 pb-0 mb-1 switzer-bold-text display-6">Payment Receipt</p>
                <p class="switzer-medium-text display-xxsm px-4 mt-0">Your payment has been successfully done!</p>  
              </div>
              <div class="col-md-12">
                <p class="px-4 pb-0 mb-1 switzer-semibold-text display-sm d-flex justify-content-between">
                  <span>00/00/00</span>
                  <span class="text-end">00:00 XX</span>
                </p>
                <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Transaction ID</span>
                  <span class="text-end switzer-bold-text">XXXXXXXXX</span>
                </p>
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
                <p class="px-4 pb-0 mt-4 mb-1 display-6 d-flex justify-content-between">
                  <span class="switzer-semibold-text">Total</span>
                  <span class="text-end switzer-bold-text"><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;000.00</span>
                </p>
              </div>
            </div>
            <div class="text-center px-4 py-1">
              <button type="button" class="btn btn-lg btn-signup px-5" data-bs-dismiss="modal">Download Receipt</button>
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

    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const dropdownItems = document.querySelectorAll('.statusFilter');
        const tableRows = document.querySelectorAll('#recentRentalPaymentsTable tbody tr');

        dropdownItems.forEach(item => {
            item.addEventListener('click', (event) => {
                event.preventDefault();
                const status = event.target.getAttribute('data-status').toLowerCase();
                filterTable(status);
                document.getElementById('statusFilterDropdown').innerText = event.target.innerText;
            });
        });

        function filterTable(status) {
            tableRows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    if (row.classList.contains(`status-${status}`)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    });
  </script>
</body>
</html>
