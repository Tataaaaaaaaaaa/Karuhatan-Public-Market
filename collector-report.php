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
      
      $sql = "SELECT p.payment_id, p.tenant_id, p.datetime_sent, p.status, p.paymentMethod, u.occupiedPhase, u.occupiedStall, u.firstName, u.middleName, u.lastName, s.daily_rental_payment
      FROM tbl_payment p
      JOIN user_registration u ON p.tenant_id = u.user_registration_id
      JOIN tbl_stalls s ON p.tenant_id = s.tenant_id
      WHERE u.occupiedPhase = ?";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $occupiedPhase);
      $stmt->execute();
      $result_payments = $stmt->get_result();


      $stmt_get_outstanding = $conn->prepare("
          SELECT 
              ur.firstName, 
              ur.middleName, 
              ur.lastName, 
              ur.occupiedStall,
              ur.outstandingBalance,
              ts.daily_rental_payment
          FROM 
              user_registration ur
          JOIN 
              tbl_stalls ts ON ur.user_registration_id = ts.tenant_id
          WHERE 
              ur.occupiedPhase = ? AND 
              (ur.outstandingBalance IS NOT NULL AND ur.outstandingBalance != 0)
      ");
      $stmt_get_outstanding->bind_param("s", $occupiedPhase);
      $stmt_get_outstanding->execute();
      $result_get_outstanding = $stmt_get_outstanding->get_result();
          
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

    <link rel="stylesheet" href="css/collector-report-style.css">
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
          <h2 class="display-3 switzer-bold-text-white">Reports</h2>
      </div>
    </div>

    <div class="container py-7 px-0">
      <div class="py-5 px-5 mx-auto" style="max-width: 1700px; box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
          <div class="dropdown mb-1">
            <button class="btn btn-signup dropdown-toggle" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                All
            </button>
            <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="all">All</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="pending">Pending</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="paid">Paid</a></li>
                <li><a class="dropdown-item statusFilter switzer-semibold-text" href="#" data-status="unpaid">Unpaid</a></li>
            </ul>
          </div>
          <table id="reportPayment" class="table" style="width:100%">
              <thead>
                  <tr class="display-sm">
                      <th>Status</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Tenant</th>
                      <th>Stall</th>
                      <th>Payment Method</th>
                      <th class="text-center">Actions</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  $total_rows = $result_payments->num_rows + $result_get_outstanding->num_rows;
                  if ($total_rows > 0) {
                    while ($row = $result_get_outstanding->fetch_assoc()) {
                      echo "<tr class='status-unpaid'>";
                      echo '<td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>';
                      echo '<td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;'.$row["outstandingBalance"].'</td>'; 
                      date_default_timezone_set('Asia/Manila');
                      $current_date_time = date('Y-m-d H:i:s');
                      echo '<td>'.$current_date_time.'</td>';
                      echo '<td>'.$row["firstName"].' '.$row["middleName"].' '.$row["lastName"].'</td>';
                      echo '<td>'.$row["occupiedStall"].'</td>';
                      echo '<td>--</td>';
                      echo '<td>
                              <div class="dropdown">
                                  <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                      Select an Action
                                  </button>
                              </div>
                          </td>';

                    }


                    while ($row = $result_payments->fetch_assoc()) {
                      $datetime_sent = $row["datetime_sent"];
                      $date = new DateTime($datetime_sent);
                      $formatted_datetime_sent = $date->format('Y-m-d H:i:s');

                      $formatted_status = ucfirst($row["status"]);

                      echo "<tr class='status-" . strtolower($row["status"]) . "'>";
                      switch($row["status"]) {
                        case 'pending':
                          echo '<td><i class="fa-solid fa-circle" style="color: #ffea00"></i>&nbsp;&nbsp;Pending</td>';
                          break;
                        case 'paid':
                          echo '<td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>';
                          break;
                      }
                      echo '<td><i class="fa-solid fa-peso-sign" style="color:#283891"></i>&nbsp;'.$row["daily_rental_payment"].'</td>';
                      $datetime_sent = $row["datetime_sent"];
                      $formatted_datetime = date("Y-m-d H:i:s", strtotime($datetime_sent));
                      echo '<td>'.$formatted_datetime.'</td>';
                      echo '<td>'.$row["firstName"].' '.$row["middleName"].' '.$row["lastName"].'</td>';
                      echo '<td>'.$row["occupiedStall"].'</td>';
                      echo '<td>'.(!empty($row["paymentMethod"]) ? $row["paymentMethod"] : "--").'</td>';
                        switch ($row["status"]) {
                          case 'paid':
                              echo '<td>
                              <div class="dropdown">
                                  <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                      Select an Action
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <li><a class="dropdown-item switzer-semibold-text" data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-paymentid = "'.$row["payment_id"].'"
                                        data-datetimesent = "'.$formatted_datetime_sent.'"
                                        data-status = "'.$formatted_status.'"
                                        data-occupiedstall = "'.$row["occupiedStall"].'"
                                        data-firstname = "'.$row["firstName"].'"
                                        data-middlename = "'.$row["middleName"].'"
                                        data-lastname = "'.$row["lastName"].'"
                                        data-dailyrentalpayment = "'.$row["daily_rental_payment"].'"
                                      >View</a></li>
                                  </ul>
                              </div>
                          </td>';
                            break;

                          default:
                              echo '<td>
                              <div class="dropdown">
                                  <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                      Select an Action
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      <li><a class="dropdown-item switzer-semibold-text" data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-paymentid = "'.$row["payment_id"].'"
                                        data-datetimesent = "'.$formatted_datetime_sent.'"
                                        data-status = "'.$formatted_status.'"
                                        data-occupiedstall = "'.$row["occupiedStall"].'"
                                        data-firstname = "'.$row["firstName"].'"
                                        data-middlename = "'.$row["middleName"].'"
                                        data-lastname = "'.$row["lastName"].'"
                                        data-dailyrentalpayment = "'.$row["daily_rental_payment"].'"
                                      >View</a></li>
                                      <li>
                                          <a href="../Developer things/php/paid-cash-connect.php?payment_id='.$row["payment_id"].'" class="dropdown-item switzer-semibold-text">
                                              Paid Cash
                                          </a>
                                      </li>
                                      <li>
                                          <a href="../Developer things/php/paid-gcash-connect.php?payment_id='.$row["payment_id"].'" class="dropdown-item switzer-semibold-text">
                                              Paid Gcash
                                          </a>
                                      </li>
                                  </ul>
                              </div>
                          </td>';
                          break;
                        }
                      echo '</tr>';
                    }
                  }else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No Payments Yet</td></tr>";
                  }
                ?>
                <!-- <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#successModal">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#detailModal">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#successModal">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#detailModal">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                    <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#successModal">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#detailModal">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="paid">
                    <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                    <td>₱ 000.00</td>
                    <td>Juan Dela Cruz</td>
                    <td>PX - XX</td>
                    <td>GCash</td>
                    <td>
                      <div class="dropdown">
                          <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                              Select an Action
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                              <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
                  <td>Juan Dela Cruz</td>
                  <td>PX - XX</td>
                  <td>GCash</td>
                  <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Select an Action
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#successModal">Send Reminder</a></li>
                            <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#detailModal">Add Payment Received</a></li>
                        </ul>
                    </div>
                </td>
              </tr>
              <tr class="paid">
                  <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                  <td>₱ 000.00</td>
                  <td>Juan Dela Cruz</td>
                  <td>PX - XX</td>
                  <td>GCash</td>
                  <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Select an Action
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                            <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                        </ul>
                    </div>
                </td>
              </tr>
              <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
                  <td>Juan Dela Cruz</td>
                  <td>PX - XX</td>
                  <td>GCash</td>
                  <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Select an Action
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#successModal">Send Reminder</a></li>
                            <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#detailModal">Add Payment Received</a></li>
                        </ul>
                    </div>
                </td>
              </tr>
              <tr class="paid">
                  <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                  <td>₱ 000.00</td>
                  <td>Juan Dela Cruz</td>
                  <td>PX - XX</td>
                  <td>GCash</td>
                  <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Select an Action
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                            <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                        </ul>
                    </div>
                </td>
              </tr>
              <tr class="unpaid">
                  <td><i class="fa-solid fa-circle" style="color: #FC0E55"></i>&nbsp;&nbsp;Unpaid</td>
                  <td>₱ 000.00</td>
                  <td>Juan Dela Cruz</td>
                  <td>PX - XX</td>
                  <td>GCash</td>
                  <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Select an Action
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#successModal">Send Reminder</a></li>
                            <li><a class="dropdown-item switzer-semibold-text" href="#" data-bs-toggle="modal" data-bs-target="#detailModal">Add Payment Received</a></li>
                        </ul>
                    </div>
                </td>
              </tr>
              <tr class="paid">
                  <td><i class="fa-solid fa-circle" style="color: #4CD964"></i>&nbsp;&nbsp;Paid</td>
                  <td>₱ 000.00</td>
                  <td>Juan Dela Cruz</td>
                  <td>PX - XX</td>
                  <td>GCash</td>
                  <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-signup btn-login px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Select an Action
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item switzer-semibold-text" href="#">Send Reminder</a></li>
                            <li><a class="dropdown-item switzer-semibold-text" href="#">Add Payment Received</a></li>
                        </ul>
                    </div>
                </td>
              </tr> -->
            </tbody>
        </table>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body">
          
          </div>
        </div>
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


    <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>

    <script>
    $(document).ready(function() {
        var table = $('#reportPayment').DataTable({            
            "searching": false,
            
            "ordering": true
        });
    });
    </script>

<script>
      $(document).ready(function() {
        $('#detailModal').on('show.bs.modal', function(event) {
          var button = $(event.relatedTarget);
          var paymentid = button.data('paymentid');
          var firstname = button.data('firstname');
          var middlename = button.data('middlename');
          var lastname = button.data('lastname');
          var datetimesent = button.data('datetimesent');
          var status = button.data('status');
          var occupiedstall = button.data('occupiedstall');
          var dailyrentalpayment = button.data('dailyrentalpayment');

          var modal = $(this);
          modal.find('.modal-body').html(`
          <div class="py-2">
            <div class="col-md-12">
             <p class="px-4 pb-0 mb-0 switzer-bold-text display-6 d-flex justify-content-between">
              <span>Rental Payment</span>
              <span class="text-end switzer-semibold-text">
                <i class="fa-solid fa-xmark" data-bs-dismiss="modal" style="cursor: pointer"></i>
              </span>
            </p>
            </div>
            <div class="col-md-12">
              <p class="px-4 pb-0 mb-4 display-sm d-flex justify-content-between">
                  <span class="switzer-medium-text">Status: ${status}</span>
                  <span class="text-end switzer-semibold-text">${datetimesent}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Tenant</span>
                  <span class="text-end switzer-bold-text">${firstname} ${middlename} ${lastname}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Stall</span>
                  <span class="text-end switzer-bold-text">${occupiedstall}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">Amount</span>
                  <span class="text-end switzer-bold-text">${dailyrentalpayment}</span>
              </p>
              <p class="px-4 pb-0 mb-1 display-sm d-flex justify-content-between">
                  <span class="switzer-semibold-text">GCash Receipt</span>
                  <span class="text-end switzer-bold-text"><a href="download-gcash-receipt.php?paymentid=${paymentid}" download>Download</a></span>
              </p>
            </div>
          </div>
          `);
        });
      });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const dropdownItems = document.querySelectorAll('.statusFilter');
        const tableRows = document.querySelectorAll('#reportPayment tbody tr');

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
