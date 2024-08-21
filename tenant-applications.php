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

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="  background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
      <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
          <h2 class="display-3 switzer-bold-text-white">Apply</h2>
      </div>
  </div>

  
  <div class="container py-7 px-5">

<div class="row py-4" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
  <div class="col-lg-9">
      <h3 class="switzer-bold-text display-6 custom-mx-4 mb-4">Tenant Rental Application Form</h3>
  </div>
  <div class="col-lg-12">
      <h3 class="switzer-bold-text display-0 custom-mx-5"><i class="fa-solid fa-circle-info"></i>&nbsp;&nbsp;&nbsp;Basic Information</h3>
      <div class="welcome-name-style mb-3"></div>
  </div>

  <form action="" method="POST" id="tenantApplicationForm" onsubmit="return validateTenantApplicationForm()" autocomplete="off">
    <div class="row">
      <div class="col-md-3-1 ms-5 first-col">
        <div class="form-floating mb-4">
          <input name="firstName" type="text" class="form-control form-input-style" id="floatingFirstName" pattern="[\sA-Za-z]+" title="Please enter a valid first name." placeholder="" required autocomplete="off">
          <label for="floatingFirstName" class="form-label-style">First Name</label>
        </div>
      </div>
      <div class="col-md-3-1">
        <div class="form-floating mb-4">
          <input name="middleName" type="text" class="form-control form-input-style" id="floatingMiddleName" pattern="[\sA-Za-z]+" title="Please enter a valid middle name." placeholder="" required autocomplete="off">
          <label for="floatingMiddleName" class="form-label-style">Middle Name</label>
        </div>
      </div>
      <div class="col-md-3-1">
        <div class="form-floating mb-4">
          <input name="lastName" type="text" class="form-control form-input-style" id="floatingLastName" pattern="[\sA-Za-z]+" title="Please enter a valid last name." placeholder="" required autocomplete="off">
          <label for="floatingLastName" class="form-label-style">Last Name</label>
        </div>
      </div>
        <div class="col-md-10 ms-5 first-col">
          <div class="form-floating mb-4">
            <input name="address" type="text" class="form-control form-input-style" id="address" pattern="^[a-zA-Z0-9\s,.'-]{5,}$" title="Please enter your address." placeholder="" required autocomplete="off">
            <label for="address" class="form-label-style">Address</label>
          </div>
        </div>
      <div class="col-md-5 ms-5 first-col">
        <div class="form-floating mb-4">
          <input name="emailAddress" type="text" class="form-control form-input-style" id="emailAddress" pattern="/^[^\.\s][\w\-\.{2,}]+@([\w-]+\.)+[\w-]{2,}$/gm" title="Please enter a valid email address." placeholder="" required autocomplete="off">
          <label for="emailAddress" class="form-label-style">Email Address</label>
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-floating mb-4">
          <input name="phoneNumber" type="tel" class="form-control form-input-style" id="floatingPhoneNumber" pattern="09[0-9]{9}" title="Please enter a valid 11-digit phone number." placeholder="" required autocomplete="off">
          <label for="floatingPhoneNumber" class="form-label-style">Phone Number</label>
        </div>
      </div>
      <div class="col-md-5 ms-5 first-col">
        <div class="form-floating mb-4">
          <select name="stallCategory" class="form-select form-input-style" id="floatingStallCategory" aria-label="Stall Category" style="height: 4rem;" required>
              <option value="" selected>Select Stall Category</option>
              <option value="Canteen">Canteen</option>
              <option value="Clothing">Clothing</option>
              <option value="Coconut">Coconut</option>
              <option value="Condiments">Condiments</option>
              <option value="Fruits">Fruits</option>
              <option value="Grocery">Grocery</option>
              <option value="Meat">Meat</option>
              <option value="Pharmacy">Pharmacy</option>
              <option value="Poultry">Poultry</option>
              <option value="Rice">Rice</option>
              <option value="SariSari">Sari-Sari</option>
              <option value="Toys">Toys</option>
              <option value="Vegetables">Vegetables</option>
          </select>
          <label for="floatingStallCategory" class="form-label-style">Stall Category</label>
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-floating mb-5">
          <select name="preferredStall" class="form-select form-input-style" id="floatingPreferredStall" aria-label="Preferred Category" style="height: 4rem;">
            <option value="" selected>Select Preferred Stall</option>
            <optgroup label="Phase 1">
              <option value="P1-1">Stall 1</option>
              <option value="P1-2">Stall 2</option>
              <option value="P1-3">Stall 3</option>
              <option value="P1-4">Stall 4</option>
              <option value="P1-5">Stall 5</option>
              <option value="P1-6">Stall 6</option>
              <option value="P1-7">Stall 7</option>
              <option value="P1-8">Stall 8</option>
              <option value="P1-9">Stall 9</option>
              <option value="P1-10">Stall 10</option>
              <option value="P1-11">Stall 11</option>
              <option value="P1-12">Stall 12</option>
              <option value="P1-13">Stall 12</option>
              <option value="P1-14">Stall 14</option>
              <option value="P1-15">Stall 15</option>
            </optgroup>
            <optgroup label="Phase 2">
              <option value="P2-1">Stall 1</option>
              <option value="P2-2">Stall 2</option>
              <option value="P2-3">Stall 3</option>
              <option value="P2-4">Stall 4</option>
              <option value="P2-5">Stall 5</option>
              <option value="P2-6">Stall 6</option>
              <option value="P2-7">Stall 7</option>
              <option value="P2-8">Stall 8</option>
              <option value="P2-9">Stall 9</option>
              <option value="P2-10">Stall 10</option>
              <option value="P2-11">Stall 11</option>
              <option value="P2-12">Stall 12</option>
              <option value="P2-13">Stall 12</option>
              <option value="P2-14">Stall 14</option>
              <option value="P2-15">Stall 15</option>
              <option value="P2-16">Stall 16</option>
              <option value="P2-17">Stall 17</option>
              <option value="P2-18">Stall 18</option>
              <option value="P2-19">Stall 19</option>
              <option value="P2-20">Stall 20</option>
              <option value="P2-21">Stall 21</option>
              <option value="P2-22">Stall 22</option>
              <option value="P2-23">Stall 23</option>
              <option value="P2-24">Stall 24</option>
              <option value="P2-25">Stall 25</option>
              <option value="P2-26">Stall 26</option>
              <option value="P2-27">Stall 27</option>
              <option value="P2-28">Stall 28</option>
              <option value="P2-29">Stall 29</option>
              <option value="P2-30">Stall 30</option>
              <option value="P2-31">Stall 31</option>
              <option value="P2-32">Stall 32</option>
              <option value="P2-33">Stall 33</option>
              <option value="P2-34">Stall 34</option>
            </optgroup>
            <optgroup label="Phase 3">
              <option value="P3-1">Stall 1</option>
              <option value="P3-2">Stall 2</option>
              <option value="P3-3">Stall 3</option>
              <option value="P3-4">Stall 4</option>
              <option value="P3-5">Stall 5</option>
              <option value="P3-6">Stall 6</option>
              <option value="P3-7">Stall 7</option>
              <option value="P3-8">Stall 8</option>
              <option value="P3-9">Stall 9</option>
              <option value="P3-10">Stall 10</option>
              <option value="P3-11">Stall 11</option>
              <option value="P3-12">Stall 12</option>
              <option value="P3-13">Stall 12</option>
              <option value="P3-14">Stall 14</option>
              <option value="P3-15">Stall 15</option>
              <option value="P3-16">Stall 16</option>
              <option value="P3-17">Stall 17</option>
              <option value="P3-18">Stall 18</option>
              <option value="P3-19">Stall 19</option>
              <option value="P3-20">Stall 20</option>
              <option value="P3-21">Stall 21</option>
              <option value="P3-22">Stall 22</option>
              <option value="P3-23">Stall 23</option>
              <option value="P3-24">Stall 24</option>
              <option value="P3-25">Stall 25</option>
              <option value="P3-26">Stall 26</option>
            </optgroup>
            <optgroup label="Phase 4">
              <option value="P4-1">Stall 1</option>
              <option value="P4-2">Stall 2</option>
              <option value="P4-3">Stall 3</option>
              <option value="P4-4">Stall 4</option>
              <option value="P4-5">Stall 5</option>
              <option value="P4-6">Stall 6</option>
              <option value="P4-7">Stall 7</option>
              <option value="P4-8">Stall 8</option>
              <option value="P4-9">Stall 9</option>
              <option value="P4-10">Stall 10</option>
              <option value="P4-11">Stall 11</option>
              <option value="P4-12">Stall 12</option>
              <option value="P4-13">Stall 12</option>
              <option value="P4-14">Stall 14</option>
              <option value="P4-15">Stall 15</option>
              <option value="P4-16">Stall 16</option>
              <option value="P4-17">Stall 17</option>
              <option value="P4-18">Stall 18</option>
              <option value="P4-19">Stall 19</option>
              <option value="P4-20">Stall 20</option>
              <option value="P4-21">Stall 21</option>
              <option value="P4-22">Stall 22</option>
              <option value="P4-23">Stall 23</option>
              <option value="P4-24">Stall 24</option>
              <option value="P4-25">Stall 25</option>
              <option value="P4-26">Stall 26</option>
              <option value="P4-27">Stall 27</option>
              <option value="P4-28">Stall 28</option>
              <option value="P4-29">Stall 29</option>
              <option value="P4-30">Stall 30</option>
              <option value="P4-31">Stall 31</option>
              <option value="P4-32">Stall 32</option>
            </optgroup>
        </select>
          <label for="floatingPreferredStall" class="form-label-style">Preferred Stall (Optional)</label>
        </div>
      </div>
    </div>

    <div class="col-lg-12">
      <h3 class="switzer-bold-text display-0 custom-mx-5"><i class="fa-regular fa-folder-open"></i>&nbsp;&nbsp;&nbsp;Documents</h3>
      <div class="welcome-name-style mb-3"></div>
    </div>

    <div class="container py-3 custom-px-6">
      <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="col-md-6">
          <h3 class="switzer-semibold-text display-0 ms-4">One (1) Valid ID</h3>
        </div>
        <div class="col-md-6 d-flex align-items-center res-justify-content">
          <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
            <input name="validId" type="file" id="validId" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" title="Please upload a valid ID." required>
          </div>
        </div>
      </div>
    </div>

    <div class="container py-3 custom-px-6 mb-5">
      <div class="row py-3 align-items-center" style="box-shadow: 0px 0px 20px #ADB4D6; border-radius: 10px">
        <div class="col-md-6">
          <h3 class="switzer-semibold-text display-0 ms-4">Cedula</h3>
        </div>
        <div class="col-md-6 d-flex align-items-center res-justify-content">
          <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
            <input name="cedula" type="file" id="cedula" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" title="Please upload a cedula." required>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-12 d-flex res-justify-content px-3">
      <button class="px-6 btn btn-lg btn-submit btn-login mb-2 btn-widen" type="submit">Submit</button> 
    </div>
  </form>
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
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Submission Successful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Your tenant rental application has been successfully sent! Please wait for the approval.</h4>
            </div>
            <div class="py-1"><button type="button" class="btn btn-lg btn-outline-success rounded-pill px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button></div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failEmailAddressPhoneNumberModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Submission!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">A tenant rental application form with the same email address and phone number has already been submitted. Please wait for the approval.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failEmailAddressModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Submission!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">A tenant rental application form with the same email address has already been submitted. Please wait for the approval.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failPhoneNumberModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Submission!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">A tenant rental application form with the same phone number has already been submitted. Please wait for the approval.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="duplicateSubmissionModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Submission!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">You already have a pending tenancy application form. Please wait for your current application to be processed before submitting another one.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Close</button>
            </div>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">Unfortunately we encountered an issue with submitting your tenant application. Please try again later.</h4>
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
                <a href="tenant-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">Apply</h6></a>
                <a href="tenant-my-applications.php" style="text-decoration: none"><h6 class="footer-text mb-4">My Applications</h6></a>
                <a href="tenant-document.php" style="text-decoration: none"><h6 class="footer-text mb-4">Document</h6></a>
              </div>
      
              <div class="col-md-3 col-lg-2 col-xl-3 mx-auto mb-4">
                <a href="tenant-guide-map.php" style="text-decoration: none"><h6 class="footer-text mb-4">Guide Map</h6></a>
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
      var emailAddressStatus = false;
      var emailAddressInput = document.getElementById("emailAddress");
        
      emailAddressInput.addEventListener("input", function() {
          var emailAddressValue = emailAddressInput.value;
          var emailAddressPattern = /^[^\.\s][\w\-\.{2,}]+@([\w-]+\.)+[\w-]{2,}$/gm;
          if (!emailAddressPattern.test(emailAddressValue)) {
              emailAddressInput.setCustomValidity("Please enter a valid email address.");
              emailAddressStatus = false;
          } else {
              emailAddressInput.setCustomValidity("");
              emailAddressStatus = true;
          }
      });



      var validIdStatus = false;
      var validIdInput = document.getElementById('validId');

      validIdInput.addEventListener('change', function() {
        if (validIdInput.files.length === 0) {
          validIdInput.setCustomValidity('Please select a valid ID.');
          validIdStatus = false;
        } else {
          validIdInput.setCustomValidity('');
          validIdStatus = true;
        }
      });

      var cedulaStatus = false;
      var cedulaInput = document.getElementById('cedula');
      cedulaInput.addEventListener('change', function() {
        if (cedulaInput.files.length === 0) {
          cedulaInput.setCustomValidity('Please select a cedula.');
          cedulaStatus = false;
        } else {
          cedulaInput.setCustomValidity('');
          cedulaStatus = true;
        }
      });



      var addressStatus = false;
      var addressInput = document.getElementById("address");
        
      addressInput.addEventListener("input", function() {
          var addressValue = addressInput.value;
          var addressPattern = /^[a-zA-Z0-9\s,.'-]{5,}$/;
          if (!addressPattern.test(addressValue)) {
              addressInput.setCustomValidity("Please enter a valid address.");
              addressStatus = false;
          } else {
              addressInput.setCustomValidity("");
              addressStatus = true;
          }
      });

      function validateTenantApplicationForm(){
        if(emailAddressStatus == true && validIdStatus == true && cedulaStatus == true && addressStatus == true) {
            return true;
            
        } else {
        return false;
        
        }
      }
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        var successModal = document.getElementById('successModal');
        var continueButton = document.getElementById('continueButton');
        var tenantApplicationForm = document.getElementById('tenantApplicationForm');

        function clearForm() {
          tenantApplicationForm.reset();
        }

        successModal.addEventListener('hidden.bs.modal', function() {
          clearForm();
        });

        continueButton.addEventListener('click', function() {
          clearForm();
        });
      });
    </script>

    <script>  
      document.getElementById("tenantApplicationForm").addEventListener("submit", function(event) {
        event.preventDefault();
    
        var formData = new FormData(this);
        formData.append("userID", "<?php echo $userID; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/tenant-application-submission-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successModal').modal('show');

                    } else {
                      if (response.errors.includes("Error: Already submitted a tenancy application form.")) {
                        $('#duplicateSubmissionModal').modal('show');
                      } else if (response.errors.includes("Error: Email address and phone number already exists.")) {
                        $('#failEmailAddressPhoneNumberModal').modal('show');
                      } else if (response.errors.includes("Error: Email address already exists.")) {
                        $('#failEmailAddressModal').modal('show');
                      } else if (response.errors.includes("Error: Phone number already exists.")) {
                        $('#failPhoneNumberModal').modal('show');
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
</body>
</html>
