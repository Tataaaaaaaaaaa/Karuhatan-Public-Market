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

      $stmt_initial_load = $conn->prepare("SELECT emailAddress, occupiedStall, outstandingBalance FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
        $occupiedStall = $row_initial_load['occupiedStall'];
        $outstandingBalance = $row_initial_load['outstandingBalance'];
      }

      $stmt_initial_load->close();

      $stmt_check_payment = $conn->prepare("SELECT daily_rental_payment FROM tbl_stalls WHERE stall_name = ?");
      $stmt_check_payment->bind_param("s", $occupiedStall);
      $stmt_check_payment->execute();
      $result_check_payment = $stmt_check_payment->get_result();

      if ($result_check_payment->num_rows > 0) {
        $row_check_payment = $result_check_payment->fetch_assoc();
        $dailyRentalPayment = $row_check_payment['daily_rental_payment'];

        if (is_null($dailyRentalPayment)) {
          header("Location: " . $_SERVER['HTTP_REFERER']);
          exit();
        }
      }
      
      $stmt_check_payment->close();


      $stmt_get_stall_payment = $conn->prepare("SELECT daily_rental_payment FROM tbl_stalls WHERE stall_name = ?");
      $stmt_get_stall_payment->bind_param("s", $occupiedStall);
      $stmt_get_stall_payment->execute();
      $result_get_stall_payment = $stmt_get_stall_payment->get_result();

      $row_get_stall_payment = $result_get_stall_payment->fetch_assoc();
      $dailyRentalPayment = $row_get_stall_payment['daily_rental_payment'];

      $sql = "SELECT payment_id, datetime_sent, status, paymentMethod
        FROM tbl_payment 
        WHERE tenant_id = ?
        ORDER BY datetime_sent DESC";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $userID);
      $stmt->execute();
      $result_payments_history = $stmt->get_result();

          
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

    <link rel="stylesheet" href="css/tenant-rental-style.css">
    <link rel="stylesheet" href="css/tenant-rental-history-style.css">
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
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Payment (Rental)</h2>
      </div>
    </div>

    <div class="container py-7 px-0" style="margin-bottom: 7.5rem">
      <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="col-12">
          <h3 class="switzer-bold-text display-6 mb-2">Select an amount to pay:</h3>
          <div class="welcome-name-style mb-3"></div>
          <!-- <h3 class="switzer-bold-text display-0">Outstanding Balance:&nbsp;&nbsp;&nbsp;<span><i class="fa-solid fa-peso-sign" style="color:#283891"></i></span><?php echo $outstandingBalance; ?></h3> -->
        </div>
        <div class="grid">
          <label class="card mb-2">
            <input name="plan" class="radio" value="now" type="radio" checked>
            <span class="plan-details">
              <span class="plan-type">Payable Now</span>
              <span class="plan-cost"><span><i class="fa-solid fa-peso-sign" style="color:#283891"></i></span> <?php echo $dailyRentalPayment; ?></span>
              <br>
            </span>
          </label>
          <label class="card mb-2">
            <input name="plan" class="radio" value="outstanding" type="radio">
            <span class="plan-details" aria-hidden="true">
              <span class="plan-type">Outstanding Balance</span>
              <span class="plan-cost"><i class="fa-solid fa-peso-sign" style="color:#283891"></i><?php echo $outstandingBalance; ?></span>
              <br>
            </span>
          </label>
        </div>
        <div class="col-lg-12 d-flex res-justify-content px-3 mt-4">
          <button onclick="updateButtonOnclick()" class="px-6 btn btn-lg btn-signup btn-login mb-2" type="button" id="proceedButton">Proceed</button> 
        </div>
      </div>
    </div>

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Payment (History)</h2>
      </div>
    </div>
    
    <div class="container py-7 px-0">
      <div class="py-5 px-5" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
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
          <table id="paymentHistoryTable" class="table" style="width:100%">
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
                  if(!empty($outstandingBalance) && $outstandingBalance != 0) {
                    echo "<tr class='status-unpaid'>";
                    echo '<td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>';
                    echo '<td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;'.$outstandingBalance.'</td>';
                    date_default_timezone_set('Asia/Manila');
                    $current_date_time = date('Y-m-d H:i:s');
                    echo '<td>'.$current_date_time.'</td>';
                    echo '<td>--</td>';
                    echo '</tr>';
                  }


                  if ($result_payments_history->num_rows > 0 && empty($outstandingBalance) && $outstandingBalance == 0) {
                    while ($row = $result_payments_history->fetch_assoc()) {
                      echo "<tr class='status-" . strtolower($row["status"]) . "'>";
                      switch($row["status"]) {
                        case 'pending':
                          echo '<td><i class="fa-solid fa-circle" style="color: #ffea00"></i>&nbsp;&nbsp;Pending</td>';
                          break;
                        case 'paid':
                          echo '<td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>';
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
                    echo "<tr><td colspan='5' style='text-align:center;'>No Payments Yet</td></tr>";
                  }
                ?>
                <!-- <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
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
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
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
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
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
                    <td>₱ 000.00</td>
                    <td>00/00/0000</td>
                    <td>GCash</td>
                    <td>
                      <div>
                        <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                      </div>
                    </td>
                </tr>
                <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
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
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>GCash</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                    </div>
                  </td>
              </tr>
              <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
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
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>GCash</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                    </div>
                  </td>
              </tr>
              <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
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
                  <td>₱ 000.00</td>
                  <td>00/00/0000</td>
                  <td>GCash</td>
                  <td>
                    <div>
                      <button class="btn btn-sm btn-signup btn-login px-4" type="button">Details</button> 
                    </div>
                  </td>
              </tr> -->
            </tbody>
        </table>
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

    <script>
      function redirectPaymentMethod() {
        location.replace("tenant-rental-payment-method.php");
      }
    </script>

    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/home.js"></script>

    <script>
        $(document).ready(function() {
            function updateButtonOnclick() {
                var selectedPlan = $('input[name="plan"]:checked').val();
                var proceedButton = document.getElementById('proceedButton');
                
                if (selectedPlan === 'now') {
                    proceedButton.onclick = function() {
                        redirectPaymentMethodNow();
                    };
                } else if (selectedPlan === 'outstanding') {
                    proceedButton.onclick = function() {
                        redirectPaymentMethodOutstanding();
                    };
                }
            }

            updateButtonOnclick();

            $('input[name="plan"]').change(function() {
                updateButtonOnclick();
            });
        });

        function redirectPaymentMethodNow() {
          window.location.href = "tenant-rental-payment-method.php"
        }

        function redirectPaymentMethodOutstanding() {
          window.location.href = "tenant-outstanding-balance-payment-method.php"
        }
    </script>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const dropdownItems = document.querySelectorAll('.statusFilter');
        const tableRows = document.querySelectorAll('#paymentHistoryTable tbody tr');

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
