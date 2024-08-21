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

      $stmt_initial_load = $conn->prepare("SELECT emailAddress FROM user_registration WHERE user_registration_id = ?");
      $stmt_initial_load->bind_param("i", $userID);
      $stmt_initial_load->execute();
      $result_initial_load = $stmt_initial_load->get_result();

      if ($result_initial_load->num_rows > 0) {
        $row_initial_load = $result_initial_load->fetch_assoc();
        $emailAddress = $row_initial_load['emailAddress'];
      }

      $stmt_initial_load->close();

      $applicationId = $_GET['application_id'];

      $stmt_my_applications = $conn->prepare("SELECT * FROM tenant_rental_application WHERE application_id = ? ORDER BY submission_time DESC");
      $stmt_my_applications->bind_param("i", $applicationId);
      $stmt_my_applications->execute();
      $result_my_applications = $stmt_my_applications->get_result();

      $row = $result_my_applications->fetch_assoc();

      $firstName = $row['firstName'];
      $middleName = $row['middleName'];
      $lastName = $row['lastName'];
      $address = $row['address'];
      $emailAddress_2 = $row['emailAddress'];
      $phoneNumber = $row['phoneNumber'];
      $stallCategory = $row['stallCategory'];
      $preferredStall = $row['preferredStall'];

          
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

  <link rel="stylesheet" href="css/prospective-tenant-applications-style.css">
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

    <div lc-helper="background" class="container-fluid py-0 d-flex justify-content-center" style="background-image: url('assets/img/about-us-bg.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
        <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
            <h2 class="display-3 switzer-bold-text-white">My Applications</h2>
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
                  <input name="firstName" type="text" class="form-control form-input-style input-field" id="floatingFirstName" pattern="[\sA-Za-z]+" title="Please enter a valid first name." placeholder="" value="<?php echo $firstName; ?>" required autocomplete="off" disabled>
                  <label for="floatingFirstName" class="form-label-style">First Name</label>
                </div>
              </div>
              <div class="col-md-3-1">
                <div class="form-floating mb-4">
                  <input name="middleName" type="text" class="form-control form-input-style input-field" id="floatingMiddleName" pattern="[\sA-Za-z]+" title="Please enter a valid middle name." placeholder="" value="<?php echo $middleName; ?>" required autocomplete="off" disabled>
                  <label for="floatingMiddleName" class="form-label-style">Middle Name</label>
                </div>
              </div>
              <div class="col-md-3-1">
                <div class="form-floating mb-4">
                  <input name="lastName" type="text" class="form-control form-input-style input-field" id="floatingLastName" pattern="[\sA-Za-z]+" title="Please enter a valid last name." placeholder="" value="<?php echo $lastName; ?>" required autocomplete="off" disabled>
                  <label for="floatingLastName" class="form-label-style">Last Name</label>
                </div>
              </div>
                <div class="col-md-10 ms-5 first-col">
                  <div class="form-floating mb-4">
                    <input name="address" type="text" class="form-control form-input-style input-field" id="address" pattern="^[a-zA-Z0-9\s,.'-]{5,}$" title="Please enter your address." placeholder="" value="<?php echo $address; ?>" required autocomplete="off" disabled>
                    <label for="address" class="form-label-style">Address</label>
                  </div>
                </div>
              <div class="col-md-5 ms-5 first-col">
                <div class="form-floating mb-4">
                  <input name="emailAddress" type="text" class="form-control form-input-style input-field" id="emailAddress" pattern="/^[^\.\s][\w\-\.{2,}]+@([\w-]+\.)+[\w-]{2,}$/gm" title="Please enter a valid email address." placeholder="" value="<?php echo $emailAddress_2; ?>" required autocomplete="off" disabled>
                  <label for="emailAddress" class="form-label-style">Email Address</label>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-floating mb-4">
                  <input name="phoneNumber" type="tel" class="form-control form-input-style input-field" id="floatingPhoneNumber" pattern="09[0-9]{9}" title="Please enter a valid 11-digit phone number." placeholder="" value="<?php echo $phoneNumber; ?>" required autocomplete="off" disabled>
                  <label for="floatingPhoneNumber" class="form-label-style">Phone Number</label>
                </div>
              </div>
              <div class="col-md-5 ms-5 first-col">
                <div class="form-floating mb-4">
                  <select name="stallCategory" class="form-select form-input-style input-field" id="floatingStallCategory" aria-label="Stall Category" style="height: 4rem;" required disabled>
                      <option value="" <?php echo ($stallCategory === '') ? 'selected' : ''; ?>>Select Stall Category</option>
                      <option value="Canteen" <?php echo ($stallCategory === 'Canteen') ? 'selected' : ''; ?>>Canteen</option>
                      <option value="Clothing" <?php echo ($stallCategory === 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
                      <option value="Coconut" <?php echo ($stallCategory === 'Coconut') ? 'selected' : ''; ?>>Coconut</option>
                      <option value="Condiments" <?php echo ($stallCategory === 'Condiments') ? 'selected' : ''; ?>>Condiments</option>
                      <option value="Fruits" <?php echo ($stallCategory === 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                      <option value="Grocery" <?php echo ($stallCategory === 'Grocery') ? 'selected' : ''; ?>>Grocery</option>
                      <option value="Meat" <?php echo ($stallCategory === 'Meat') ? 'selected' : ''; ?>>Meat</option>
                      <option value="Pharmacy" <?php echo ($stallCategory === 'Pharmacy') ? 'selected' : ''; ?>>Pharmacy</option>
                      <option value="Poultry" <?php echo ($stallCategory === 'Poultry') ? 'selected' : ''; ?>>Poultry</option>
                      <option value="Rice" <?php echo ($stallCategory === 'Rice') ? 'selected' : ''; ?>>Rice</option>
                      <option value="SariSari" <?php echo ($stallCategory === 'SariSari') ? 'selected' : ''; ?>>Sari-Sari</option>
                      <option value="Toys" <?php echo ($stallCategory === 'Toys') ? 'selected' : ''; ?>>Toys</option>
                      <option value="Vegetables" <?php echo ($stallCategory === 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                  </select>
                  <label for="floatingStallCategory" class="form-label-style">Stall Category</label>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-floating mb-5">
                  <select name="preferredStall" class="form-select form-input-style input-field" id="floatingPreferredStall" aria-label="Preferred Category" style="height: 4rem;" disabled>
                    <option value="" <?php echo ($preferredStall === '') ? 'selected' : ''; ?>>Select Preferred Stall</option>
                    <optgroup label="Phase 1">
                      <option value="P1-1" <?php echo ($preferredStall === 'P1-1') ? 'selected' : ''; ?>>P1-1</option>
                      <option value="P1-2" <?php echo ($preferredStall === 'P1-2') ? 'selected' : ''; ?>>P1-2</option>
                      <option value="P1-3" <?php echo ($preferredStall === 'P1-3') ? 'selected' : ''; ?>>P1-3</option>
                      <option value="P1-4" <?php echo ($preferredStall === 'P1-4') ? 'selected' : ''; ?>>P1-4</option>
                      <option value="P1-5" <?php echo ($preferredStall === 'P1-5') ? 'selected' : ''; ?>>P1-5</option>
                      <option value="P1-6" <?php echo ($preferredStall === 'P1-6') ? 'selected' : ''; ?>>P1-6</option>
                      <option value="P1-7" <?php echo ($preferredStall === 'P1-7') ? 'selected' : ''; ?>>P1-7</option>
                      <option value="P1-8" <?php echo ($preferredStall === 'P1-8') ? 'selected' : ''; ?>>P1-8</option>
                      <option value="P1-9" <?php echo ($preferredStall === 'P1-9') ? 'selected' : ''; ?>>P1-9</option>
                      <option value="P1-10" <?php echo ($preferredStall === 'P1-10') ? 'selected' : ''; ?>>P1-10</option>
                      <option value="P1-11" <?php echo ($preferredStall === 'P1-11') ? 'selected' : ''; ?>>P1-11</option>
                      <option value="P1-12" <?php echo ($preferredStall === 'P1-12') ? 'selected' : ''; ?>>P1-12</option>
                      <option value="P1-13" <?php echo ($preferredStall === 'P1-13') ? 'selected' : ''; ?>>P1-13</option>
                      <option value="P1-14" <?php echo ($preferredStall === 'P1-14') ? 'selected' : ''; ?>>P1-14</option>
                      <option value="P1-15" <?php echo ($preferredStall === 'P1-15') ? 'selected' : ''; ?>>P1-15</option>
                    </optgroup>
                    <optgroup label="Phase 2">
                      <option value="P2-1" <?php echo ($preferredStall === 'P2-1') ? 'selected' : ''; ?>>P2-1</option>
                      <option value="P2-2" <?php echo ($preferredStall === 'P2-2') ? 'selected' : ''; ?>>P2-2</option>
                      <option value="P2-3" <?php echo ($preferredStall === 'P2-3') ? 'selected' : ''; ?>>P2-3</option>
                      <option value="P2-4" <?php echo ($preferredStall === 'P2-4') ? 'selected' : ''; ?>>P2-4</option>
                      <option value="P2-5" <?php echo ($preferredStall === 'P2-5') ? 'selected' : ''; ?>>P2-5</option>
                      <option value="P2-6" <?php echo ($preferredStall === 'P2-6') ? 'selected' : ''; ?>>P2-6</option>
                      <option value="P2-7" <?php echo ($preferredStall === 'P2-7') ? 'selected' : ''; ?>>P2-7</option>
                      <option value="P2-8" <?php echo ($preferredStall === 'P2-8') ? 'selected' : ''; ?>>P2-8</option>
                      <option value="P2-9" <?php echo ($preferredStall === 'P2-9') ? 'selected' : ''; ?>>P2-9</option>
                      <option value="P2-10" <?php echo ($preferredStall === 'P2-10') ? 'selected' : ''; ?>>P2-10</option>
                      <option value="P2-11" <?php echo ($preferredStall === 'P2-11') ? 'selected' : ''; ?>>P2-11</option>
                      <option value="P2-12" <?php echo ($preferredStall === 'P2-12') ? 'selected' : ''; ?>>P2-12</option>
                      <option value="P2-13" <?php echo ($preferredStall === 'P2-13') ? 'selected' : ''; ?>>P2-13</option>
                      <option value="P2-14" <?php echo ($preferredStall === 'P2-14') ? 'selected' : ''; ?>>P2-14</option>
                      <option value="P2-15" <?php echo ($preferredStall === 'P2-15') ? 'selected' : ''; ?>>P2-15</option>
                      <option value="P2-16" <?php echo ($preferredStall === 'P2-16') ? 'selected' : ''; ?>>P2-16</option>
                      <option value="P2-17" <?php echo ($preferredStall === 'P2-17') ? 'selected' : ''; ?>>P2-17</option>
                      <option value="P2-18" <?php echo ($preferredStall === 'P2-18') ? 'selected' : ''; ?>>P2-18</option>
                      <option value="P2-19" <?php echo ($preferredStall === 'P2-19') ? 'selected' : ''; ?>>P2-19</option>
                      <option value="P2-20" <?php echo ($preferredStall === 'P2-20') ? 'selected' : ''; ?>>P2-20</option>
                      <option value="P2-21" <?php echo ($preferredStall === 'P2-21') ? 'selected' : ''; ?>>P2-21</option>
                      <option value="P2-22" <?php echo ($preferredStall === 'P2-22') ? 'selected' : ''; ?>>P2-22</option>
                      <option value="P2-23" <?php echo ($preferredStall === 'P2-23') ? 'selected' : ''; ?>>P2-23</option>
                      <option value="P2-24" <?php echo ($preferredStall === 'P2-24') ? 'selected' : ''; ?>>P2-24</option>
                      <option value="P2-25" <?php echo ($preferredStall === 'P2-25') ? 'selected' : ''; ?>>P2-25</option>
                      <option value="P2-26" <?php echo ($preferredStall === 'P2-26') ? 'selected' : ''; ?>>P2-26</option>
                      <option value="P2-27" <?php echo ($preferredStall === 'P2-27') ? 'selected' : ''; ?>>P2-27</option>
                      <option value="P2-28" <?php echo ($preferredStall === 'P2-28') ? 'selected' : ''; ?>>P2-28</option>
                      <option value="P2-29" <?php echo ($preferredStall === 'P2-29') ? 'selected' : ''; ?>>P2-29</option>
                      <option value="P2-30" <?php echo ($preferredStall === 'P2-30') ? 'selected' : ''; ?>>P2-30</option>
                      <option value="P2-31" <?php echo ($preferredStall === 'P2-31') ? 'selected' : ''; ?>>P2-31</option>
                      <option value="P2-32" <?php echo ($preferredStall === 'P2-32') ? 'selected' : ''; ?>>P2-32</option>
                      <option value="P2-33" <?php echo ($preferredStall === 'P2-33') ? 'selected' : ''; ?>>P2-33</option>
                      <option value="P2-34" <?php echo ($preferredStall === 'P2-34') ? 'selected' : ''; ?>>P2-34</option>
                      <option value="P2-35" <?php echo ($preferredStall === 'P2-35') ? 'selected' : ''; ?>>P2-34</option>
                      <option value="P2-36" <?php echo ($preferredStall === 'P2-36') ? 'selected' : ''; ?>>P2-36</option>
                      <option value="P2-37" <?php echo ($preferredStall === 'P2-37') ? 'selected' : ''; ?>>P2-37</option>
                      <option value="P2-38" <?php echo ($preferredStall === 'P2-38') ? 'selected' : ''; ?>>P2-38</option>
                      <option value="P2-39" <?php echo ($preferredStall === 'P2-39') ? 'selected' : ''; ?>>P2-39</option>
                    </optgroup>
                    <optgroup label="Phase 3">
                      <option value="P3-1" <?php echo ($preferredStall === 'P3-1') ? 'selected' : ''; ?>>P3-1</option>
                      <option value="P3-2" <?php echo ($preferredStall === 'P3-2') ? 'selected' : ''; ?>>P3-2</option>
                      <option value="P3-3" <?php echo ($preferredStall === 'P3-3') ? 'selected' : ''; ?>>P3-3</option>
                      <option value="P3-4" <?php echo ($preferredStall === 'P3-4') ? 'selected' : ''; ?>>P3-4</option>
                      <option value="P3-5" <?php echo ($preferredStall === 'P3-5') ? 'selected' : ''; ?>>P3-5</option>
                      <option value="P3-6" <?php echo ($preferredStall === 'P3-6') ? 'selected' : ''; ?>>P3-6</option>
                      <option value="P3-7" <?php echo ($preferredStall === 'P3-7') ? 'selected' : ''; ?>>P3-7</option>
                      <option value="P3-8" <?php echo ($preferredStall === 'P3-8') ? 'selected' : ''; ?>>P3-8</option>
                      <option value="P3-9" <?php echo ($preferredStall === 'P3-9') ? 'selected' : ''; ?>>P3-9</option>
                      <option value="P3-10" <?php echo ($preferredStall === 'P3-10') ? 'selected' : ''; ?>>P3-10</option>
                      <option value="P3-11" <?php echo ($preferredStall === 'P3-11') ? 'selected' : ''; ?>>P3-11</option>
                      <option value="P3-12" <?php echo ($preferredStall === 'P3-12') ? 'selected' : ''; ?>>P3-12</option>
                      <option value="P3-13" <?php echo ($preferredStall === 'P3-13') ? 'selected' : ''; ?>>P3-13</option>
                      <option value="P3-14" <?php echo ($preferredStall === 'P3-14') ? 'selected' : ''; ?>>P3-14</option>
                      <option value="P3-15" <?php echo ($preferredStall === 'P3-15') ? 'selected' : ''; ?>>P3-15</option>
                      <option value="P3-16" <?php echo ($preferredStall === 'P3-16') ? 'selected' : ''; ?>>P3-16</option>
                      <option value="P3-17" <?php echo ($preferredStall === 'P3-17') ? 'selected' : ''; ?>>P3-17</option>
                      <option value="P3-18" <?php echo ($preferredStall === 'P3-18') ? 'selected' : ''; ?>>P3-18</option>
                      <option value="P3-19" <?php echo ($preferredStall === 'P3-19') ? 'selected' : ''; ?>>P3-19</option>
                      <option value="P3-20" <?php echo ($preferredStall === 'P3-20') ? 'selected' : ''; ?>>P3-20</option>
                      <option value="P3-21" <?php echo ($preferredStall === 'P3-21') ? 'selected' : ''; ?>>P3-21</option>
                      <option value="P3-22" <?php echo ($preferredStall === 'P3-22') ? 'selected' : ''; ?>>P3-22</option>
                      <option value="P3-23" <?php echo ($preferredStall === 'P3-23') ? 'selected' : ''; ?>>P3-23</option>
                      <option value="P3-24" <?php echo ($preferredStall === 'P3-24') ? 'selected' : ''; ?>>P3-24</option>
                      <option value="P3-25" <?php echo ($preferredStall === 'P3-25') ? 'selected' : ''; ?>>P3-25</option>
                      <option value="P3-26" <?php echo ($preferredStall === 'P3-26') ? 'selected' : ''; ?>>P3-26</option>
                      <option value="P3-27" <?php echo ($preferredStall === 'P3-27') ? 'selected' : ''; ?>>P3-27</option>
                      <option value="P3-28" <?php echo ($preferredStall === 'P3-28') ? 'selected' : ''; ?>>P3-28</option>
                      <option value="P3-29" <?php echo ($preferredStall === 'P3-29') ? 'selected' : ''; ?>>P3-29</option>
                      <option value="P3-30" <?php echo ($preferredStall === 'P3-30') ? 'selected' : ''; ?>>P3-30</option>
                      <option value="P3-31" <?php echo ($preferredStall === 'P3-31') ? 'selected' : ''; ?>>P3-31</option>
                      <option value="P3-32" <?php echo ($preferredStall === 'P3-32') ? 'selected' : ''; ?>>P3-32</option>
                      <option value="P3-33" <?php echo ($preferredStall === 'P3-33') ? 'selected' : ''; ?>>P3-33</option>
                      <option value="P3-34" <?php echo ($preferredStall === 'P3-34') ? 'selected' : ''; ?>>P3-34</option>
                      <option value="P3-35" <?php echo ($preferredStall === 'P3-35') ? 'selected' : ''; ?>>P3-35</option>
                    </optgroup>
                    <optgroup label="Phase 4">
                      <option value="P4-1" <?php echo ($preferredStall === 'P4-1') ? 'selected' : ''; ?>>P4-1</option>
                      <option value="P4-2" <?php echo ($preferredStall === 'P4-2') ? 'selected' : ''; ?>>P4-2</option>
                      <option value="P4-3" <?php echo ($preferredStall === 'P4-3') ? 'selected' : ''; ?>>P4-3</option>
                      <option value="P4-4" <?php echo ($preferredStall === 'P4-4') ? 'selected' : ''; ?>>P4-4</option>
                      <option value="P4-5" <?php echo ($preferredStall === 'P4-5') ? 'selected' : ''; ?>>P4-5</option>
                      <option value="P4-6" <?php echo ($preferredStall === 'P4-6') ? 'selected' : ''; ?>>P4-6</option>
                      <option value="P4-7" <?php echo ($preferredStall === 'P4-7') ? 'selected' : ''; ?>>P4-7</option>
                      <option value="P4-8" <?php echo ($preferredStall === 'P4-8') ? 'selected' : ''; ?>>P4-8</option>
                      <option value="P4-9" <?php echo ($preferredStall === 'P4-9') ? 'selected' : ''; ?>>P4-9</option>
                      <option value="P4-10" <?php echo ($preferredStall === 'P4-10') ? 'selected' : ''; ?>>P4-10</option>
                      <option value="P4-11" <?php echo ($preferredStall === 'P4-11') ? 'selected' : ''; ?>>P4-11</option>
                      <option value="P4-12" <?php echo ($preferredStall === 'P4-12') ? 'selected' : ''; ?>>P4-12</option>
                      <option value="P4-13" <?php echo ($preferredStall === 'P4-13') ? 'selected' : ''; ?>>P4-13</option>
                      <option value="P4-14" <?php echo ($preferredStall === 'P4-14') ? 'selected' : ''; ?>>P4-14</option>
                      <option value="P4-15" <?php echo ($preferredStall === 'P4-15') ? 'selected' : ''; ?>>P4-15</option>
                      <option value="P4-16" <?php echo ($preferredStall === 'P4-16') ? 'selected' : ''; ?>>P4-16</option>
                      <option value="P4-17" <?php echo ($preferredStall === 'P4-17') ? 'selected' : ''; ?>>P4-17</option>
                      <option value="P4-18" <?php echo ($preferredStall === 'P4-18') ? 'selected' : ''; ?>>P4-18</option>
                      <option value="P4-19" <?php echo ($preferredStall === 'P4-19') ? 'selected' : ''; ?>>P4-19</option>
                      <option value="P4-20" <?php echo ($preferredStall === 'P4-20') ? 'selected' : ''; ?>>P4-20</option>
                      <option value="P4-21" <?php echo ($preferredStall === 'P4-21') ? 'selected' : ''; ?>>P4-21</option>
                      <option value="P4-22" <?php echo ($preferredStall === 'P4-22') ? 'selected' : ''; ?>>P4-22</option>
                      <option value="P4-23" <?php echo ($preferredStall === 'P4-23') ? 'selected' : ''; ?>>P4-23</option>
                      <option value="P4-24" <?php echo ($preferredStall === 'P4-24') ? 'selected' : ''; ?>>P4-24</option>
                      <option value="P4-25" <?php echo ($preferredStall === 'P4-25') ? 'selected' : ''; ?>>P4-25</option>
                      <option value="P4-26" <?php echo ($preferredStall === 'P4-26') ? 'selected' : ''; ?>>P4-26</option>
                      <option value="P4-27" <?php echo ($preferredStall === 'P4-27') ? 'selected' : ''; ?>>P4-27</option>
                      <option value="P4-28" <?php echo ($preferredStall === 'P4-28') ? 'selected' : ''; ?>>P4-28</option>
                      <option value="P4-29" <?php echo ($preferredStall === 'P4-29') ? 'selected' : ''; ?>>P4-29</option>
                      <option value="P4-30" <?php echo ($preferredStall === 'P4-30') ? 'selected' : ''; ?>>P4-30</option>
                      <option value="P4-31" <?php echo ($preferredStall === 'P4-31') ? 'selected' : ''; ?>>P4-31</option>
                      <option value="P4-32" <?php echo ($preferredStall === 'P4-32') ? 'selected' : ''; ?>>P4-32</option>
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
                    <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" id="downloadBtnValidId" href="download-prospective-tenant-application.php?application_id=<?php echo $applicationId; ?>&file_type=validId">Download Valid ID</a>                    
                    <input name="validId" type="file" id="validId" class="form-control input-field" style="display: none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" title="Please upload a valid ID." disabled>
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
                    <a class="btn btn-signup btn-lg px-5 gap-3 switzer-semibold-text-white mx-4" id="downloadBtnCedula" href="download-prospective-tenant-application.php?application_id=<?php echo $applicationId; ?>&file_type=cedula">Download Cedula</a>
                    <input name="cedula" type="file" id="cedula" class="form-control input-field" style="display: none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" title="Please upload a cedula." disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-12 d-flex res-justify-content px-3">
                <a class="btn btn-green btn-lg px-5 switzer-semibold-text-white mx-2 btn-widen" id="editBtn">Edit</a>
                <a class="btn btn-red btn-lg px-5 switzer-semibold-text-white mx-2 btn-widen" id="deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</a>
            </div>

            <div class="col-lg-12 d-flex res-justify-content px-3">
                <button class="btn btn-green btn-lg px-5 switzer-semibold-text-white mx-2 btn-widen" id="saveBtn" style="display: none" type="submit">Save</button>
                <a class="btn btn-red btn-lg px-5 switzer-semibold-text-white mx-2 btn-widen" id="cancelBtn" role="button" style="display: none">Cancel</a>
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
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Successful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Your tenant rental application has been successfully updated! Please wait for the approval.</h4>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">A tenant rental application form with the same email address and phone number has already been submitted. Please try another.</h4>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">A tenant rental application form with the same email address has already been submitted. Please try another.</h4>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">A tenant rental application form with the same phone number has already been submitted. Please try another.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">Unfortunately we encountered an issue with updating your tenant application. Please try again later.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
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
              <button type="button" class="try-again-button btn btn-lg btn-outline-secondary rounded-pill px-5" id="modalCancelBtn" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Cancel</button>
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5"  id="confirmDeleteBtn" style="font-family: 'Switzer-Semibold'" data-application-id="<?php echo $applicationId; ?>">Delete</button>
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

    <script>
      document.getElementById("editBtn").addEventListener("click", function() {
          document.getElementById("editBtn").style.display = "none";
          document.getElementById("deleteBtn").style.display = "none";
          document.getElementById("saveBtn").style.display = "block";
          document.getElementById("cancelBtn").style.display = "block";
          document.getElementById("validId").style.display = "block";
          document.getElementById("cedula").style.display = "block";
          document.getElementById("downloadBtnValidId").style.display = "none";
          document.getElementById("downloadBtnCedula").style.display = "none";

          var inputFields = document.querySelectorAll(".input-field");
          inputFields.forEach(function(input) {
              input.disabled = false;
          });
      });

      document.getElementById("cancelBtn").addEventListener("click", function() {
          document.getElementById("editBtn").style.display = "block";
          document.getElementById("deleteBtn").style.display = "block";
          document.getElementById("saveBtn").style.display = "none";
          document.getElementById("cancelBtn").style.display = "none";
          document.getElementById("validId").style.display = "none";
          document.getElementById("cedula").style.display = "none";
          document.getElementById("downloadBtnValidId").style.display = "block";
          document.getElementById("downloadBtnCedula").style.display = "block";
          
          var tenantApplicationForm = document.getElementById("tenantApplicationForm");
          tenantApplicationForm.reset();
          
          var inputFields = document.querySelectorAll(".input-field");
          inputFields.forEach(function(input) {
              input.disabled = true;
          });
      });

    </script>

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
        if(emailAddressStatus == true && addressStatus == true) {
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

        successModal.addEventListener('hidden.bs.modal', function() {
          window.location.href = "prospective-tenant-my-applications-view.php?application_id=<?php echo $applicationId; ?>";
        });

        continueButton.addEventListener('click', function() {
          window.location.href = "prospective-tenant-my-applications-view.php?application_id=<?php echo $applicationId; ?>";
        });
      });
    </script>

  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

          confirmDeleteBtn.addEventListener('click', function() {
              const applicationIdToDelete = confirmDeleteBtn.getAttribute('data-application-id');

              if (applicationIdToDelete) {
                  window.location.href = 'delete-prospective-tenant-application-form.php?application_id=' + applicationIdToDelete;
              }
          });
      });
  </script>

    <script>  
      document.getElementById("tenantApplicationForm").addEventListener("submit", function(event) {
        event.preventDefault();
    
        var formData = new FormData(this);
        formData.append("applicationId", "<?php echo $applicationId; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/update-tenant-application-submission-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successModal').modal('show');

                    } else {
                      if (response.errors.includes("Error: Email address and phone number already exists.")) {
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
