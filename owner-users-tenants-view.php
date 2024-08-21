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

      $tenant_id = $_GET['user_registration_id'];

      $stmt_tenant_details = $conn->prepare("SELECT * FROM user_registration WHERE user_registration_id = ?");
      $stmt_tenant_details->bind_param("i", $tenant_id);
      $stmt_tenant_details->execute();
      $result_tenant_details = $stmt_tenant_details->get_result();

      $row = $result_tenant_details->fetch_assoc();

      $firstName = $row['firstName'];
      $middleName = $row['middleName'];
      $lastName = $row['lastName'];
      $address = $row['address'];
      $phoneNumber = $row['phoneNumber'];
      $avatar = $row['avatar'];
      $emailAddress_2 = $row['emailAddress'];
      $occupiedStall = $row['occupiedStall'];
      $stallCategory = $row['stallCategory'];

      $stmt_profile_additional = $conn->prepare("SELECT middleName, address FROM tenant_rental_application WHERE user_registration_id = ? AND applicationStatus = 'Approved'");
      $stmt_profile_additional->bind_param("i", $tenant_id);
      $stmt_profile_additional->execute();
      $result_profile_additional = $stmt_profile_additional->get_result();

      $row_additional = $result_profile_additional->fetch_assoc();

      $middleName_backup = $row_additional['middleName'];
      $address_backup = $row_additional['address'];
          
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

    <link rel="stylesheet" href="css/owner-profile-style.css">
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
        <div class="container-fluid px-4 py-0 my-4 col-8 custom-text-center">
            <h2 class="display-3 switzer-bold-text-white">Tenant Details</h2>
        </div>
    </div>

    <div class="container py-3 px-5">
    <form method="POST" action="" id="profileForm" onsubmit="validateProfileForm()" autocomplete="off" enctype="multipart/form-data">
    <div class="content-md-lg py-3">
          <div class="container-lg">
              <div class="row py-3">
                  <div class="col-lg-4 col-md-5 col-sm-10 col-12 mx-auto">
                      <div class="rounded-0">
                          <div class="card-body rounded-0">
                                <div id="AvatarFileUpload">
                                    <div class="selected-image-holder">
                                    <?php
                                      if(!empty($avatar)) {
                                        $avatarData = base64_encode($avatar);
                                        echo '<img src="data:image/jpeg;base64,'.$avatarData.'" alt="Avatar">';
                                      } else {
                                        echo '<img src="../Developer things/assets/img/default-avatar.png" alt="Avatar">';
                                      }
                                    ?>
                                    </div>
                                    <div class="avatar-selector" id="imgUploadBtn" style="display:none; cursor: pointer">
                                        <div class="container d-flex justify-content-center">
                                          <a class="avatar-selector-btn">
                                            <i class="fa-solid fa-circle-plus" style="color: #283891; font-size: 25px; background-color: white; border-radius: 50% 50%"></i>
                                          </a>
                                          <input type="file" accept="images/jpg, images/png" name="avatar">
                                        </div>
                                    </div>
                                </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div class="row d-flex justify-content-center">
        <div class="col-md-3-1 first-col">
          <div class="form-floating mb-4">
              <input name="firstName" type="text" class="form-control form-input-style input-field" id="firstName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid first name." value="<?php echo $firstName; ?>" required autocomplete="off" disabled>
              <label for="firstName" class="form-label-style">First Name</label>
          </div>
        </div>
        <div class="col-md-3-1 first-col">
          <div class="form-floating mb-4">
            <?php
              if(!empty($middleName)) {
                echo '<input name="middleName" type="text" class="form-control form-input-style input-field" id="middleName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid middle name." value="'.$middleName.'" required autocomplete="off" disabled>';
              } else {
                echo '<input name="middleName" type="text" class="form-control form-input-style input-field" id="middleName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid middle name." value="'.$middleName_backup.'" required autocomplete="off" disabled>';
              }
            ?>
            <label for="middleName" class="form-label-style">Middle Name</label>
          </div>
        </div>
        <!-- <div class="col-md-3-1 first-col">
          <div class="form-floating mb-4">
            <input name="middleName" type="text" class="form-control form-input-style input-field" id="middleName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid middle name." value="<?php echo $middleName; ?>" autocomplete="off" disabled>
            <label for="middleName" class="form-label-style">Middle Name</label>
          </div>
        </div> -->
        <div class="col-md-3-1">
          <div class="form-floating mb-4">
            <input name="lastName" type="text" class="form-control form-input-style input-field" id="lastName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid last name." value="<?php echo $lastName; ?>" required autocomplete="off" disabled>
            <label for="lastName" class="form-label-style">Last Name</label>
          </div>
        </div>
        <div class="col-md-10 first-col">
          <div class="form-floating mb-4">
            <?php
              if(!empty($address)) {
                echo "<input name='address' type='text' class='form-control form-input-style input-field' id='address' placeholder='' pattern='^[a-zA-Z0-9\s,.'-]{5,}$' title='Please enter your address.' value='".$address."' required autocomplete='off' disabled>";
              } else {
                echo "<input name='address' type='text' class='form-control form-input-style input-field' id='address' placeholder='' pattern='^[a-zA-Z0-9\s,.'-]{5,}$' title='Please enter your address.' value='".$address_backup."' required autocomplete='off' disabled>";
              }
            ?>
            <label for="address" class="form-label-style">Address</label>
          </div>
        </div>
        <!-- <div class="col-md-10 first-col">
          <div class="form-floating mb-4">
            <input name="address" type="text" class="form-control form-input-style input-field" id="address" placeholder="" pattern="^[a-zA-Z0-9\s,.'-]{5,}$" title="Please enter your address." value="<?php echo $address; ?>" autocomplete="off" disabled>
            <label for="address" class="form-label-style">Address</label>
          </div>
        </div> -->
        <div class="col-md-5 first-col">
          <div class="form-floating mb-4">
            <input name="emailAddress" type="text" class="form-control form-input-style input-field" id="emailAddress" placeholder="" pattern="/^[^\.\s][\w\-\.{2,}]+@([\w-]+\.)+[\w-]{2,}$/gm" title="Please enter a valid email address." value="<?php echo $emailAddress_2; ?>" required autocomplete="off" disabled>
            <label for="emailAddress" class="form-label-style">Email Address</label>
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-floating mb-4">
            <input name="phoneNumber" type="tel" class="form-control form-input-style input-field" id="phoneNumber" placeholder="" pattern="09[0-9]{9}" title="Please enter a valid 11-digit phone number." value="<?php echo $phoneNumber; ?>" required autocomplete="off" disabled>
            <label for="phoneNumber" class="form-label-style">Phone Number</label>
          </div>
        </div>
        <div class="col-md-5 first-col">
          <div class="form-floating mb-4">
          <select name="occupiedStall" class="form-select form-input-style input-field" id="occupiedStall" style="height: 4rem;" required disabled>
            <option value="" selected>Set Occupied Stall</option>
            <?php
              function generateOptions($phase, $occupiedStall, $stalls) {
                echo "<optgroup label='$phase'>";
                foreach ($stalls as $stallValue => $stallText) {
                  $selected = ($stallValue === $occupiedStall) ? 'selected' : '';
                  echo "<option value='$stallValue' $selected>$stallText</option>";
                }
                echo "</optgroup>";
              }

              switch($occupiedPhase) {
                case 'phaseOne':
                  generateOptions('Phase 1', $occupiedStall, [
                    'P1-1' => 'P1-1', 'P1-2' => 'P1-2', 'P1-3' => 'P1-3', 'P1-4' => 'P1-4', 'P1-5' => 'P1-5', 
                    'P1-6' => 'P1-6', 'P1-7' => 'P1-7', 'P1-8' => 'P1-8', 'P1-9' => 'P1-9', 'P1-10' => 'P1-10', 
                    'P1-11' => 'P1-11', 'P1-12' => 'P1-12', 'P1-13' => 'P1-13', 'P1-14' => 'P1-14', 'P1-15' => 'P1-15'
                  ]);
                  break;

                case 'phaseTwo':
                  generateOptions('Phase 2', $occupiedStall, [
                    'P2-1' => 'P2-1', 'P2-2' => 'P2-2', 'P2-3' => 'P2-3', 'P2-4' => 'P2-4', 'P2-5' => 'P2-5', 
                    'P2-6' => 'P2-6', 'P2-7' => 'P2-7', 'P2-8' => 'P2-8', 'P2-9' => 'P2-9', 'P2-10' => 'P2-10',
                    'P2-11' => 'P2-11', 'P2-12' => 'P2-12', 'P2-13' => 'P2-13', 'P2-14' => 'P2-14', 'P2-15' => 'P2-15',
                    'P2-16' => 'P2-16', 'P2-17' => 'P2-17', 'P2-18' => 'P2-18', 'P2-19' => 'P2-19', 'P2-20' => 'P2-20',
                    'P2-21' => 'P2-21', 'P2-22' => 'P2-22', 'P2-23' => 'P2-23', 'P2-24' => 'P2-24', 'P2-25' => 'P2-25',
                    'P2-26' => 'P2-26', 'P2-27' => 'P2-27', 'P2-28' => 'P2-28', 'P2-29' => 'P2-29', 'P2-30' => 'P2-30',
                    'P2-31' => 'P2-31', 'P2-32' => 'P2-32', 'P2-33' => 'P2-33', 'P2-34' => 'P2-34', 'P2-35' => 'P2-35',
                    'P2-36' => 'P2-36', 'P2-37' => 'P2-37', 'P2-38' => 'P2-38', 'P2-39' => 'P2-39'
                  ]);
                  break;

                case 'phaseThree':
                  generateOptions('Phase 3', $occupiedStall, [
                    'P3-1' => 'P3-1', 'P3-2' => 'P3-2', 'P3-3' => 'P3-3', 'P3-4' => 'P3-4', 'P3-5' => 'P3-5',
                    'P3-6' => 'P3-6', 'P3-7' => 'P3-7', 'P3-8' => 'P3-8', 'P3-9' => 'P3-9', 'P3-10' => 'P3-10',
                    'P3-11' => 'P3-11', 'P3-12' => 'P3-12', 'P3-13' => 'P3-13', 'P3-14' => 'P3-14', 'P3-15' => 'P3-15',
                    'P3-16' => 'P3-16', 'P3-17' => 'P3-17', 'P3-18' => 'P3-18', 'P3-19' => 'P3-19', 'P3-20' => 'P3-20',
                    'P3-21' => 'P3-21', 'P3-22' => 'P3-22', 'P3-23' => 'P3-23', 'P3-24' => 'P3-24', 'P3-25' => 'P3-25',
                    'P3-26' => 'P3-26', 'P3-27' => 'P3-27', 'P3-28' => 'P3-28', 'P3-29' => 'P3-29', 'P3-30' => 'P3-30',
                    'P3-31' => 'P3-31', 'P3-32' => 'P3-32', 'P3-33' => 'P3-33', 'P3-34' => 'P3-34', 'P3-35' => 'P3-35'
                  ]);
                  break;

                case 'phaseFour':
                  generateOptions('Phase 4', $occupiedStall, [
                    'P4-1' => 'P4-1', 'P4-2' => 'P4-2', 'P4-3' => 'P4-3', 'P4-4' => 'P4-4', 'P4-5' => 'P4-5',
                    'P4-6' => 'P4-6', 'P4-7' => 'P4-7', 'P4-8' => 'P4-8', 'P4-9' => 'P4-9', 'P4-10' => 'P4-10',
                    'P4-11' => 'P4-11', 'P4-12' => 'P4-12', 'P4-13' => 'P4-13', 'P4-14' => 'P4-14', 'P4-15' => 'P4-15',
                    'P4-16' => 'P4-16', 'P4-17' => 'P4-17', 'P4-18' => 'P4-18', 'P4-19' => 'P4-19', 'P4-20' => 'P4-20',
                    'P4-21' => 'P4-21', 'P4-22' => 'P4-22', 'P4-23' => 'P4-23', 'P4-24' => 'P4-24', 'P4-25' => 'P4-25',
                    'P4-26' => 'P4-26', 'P4-27' => 'P4-27', 'P4-28' => 'P4-28', 'P4-29' => 'P4-29', 'P4-30' => 'P4-30',
                    'P4-31' => 'P4-31', 'P4-32' => 'P4-32'
                  ]);
                  break;
              }
            ?>
          </select>

            <label for="occupiedStall" class="form-label-style">Occupied Stall</label>
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-floating mb-4">
            <select name="stallCategory" class="form-select form-input-style input-field" id="floatingStallCategory" aria-label="Stall Category" style="height: 4rem;" required disabled>
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
      </div>

      <div class="col-lg-12 d-flex res-justify-content px-3">
          <a class="px-5 btn btn-lg btn-signup mb-2 mx-2" id="editBtn" style="display: block">Edit</a>
        </div>

        <div class="col-lg-12 d-flex res-justify-content px-3">
          <button class="px-5 btn btn-lg btn-green mb-2 btn-widen mx-2" id="saveBtn" type="submit" style="display: none">Save</button> 
          <a class="px-5 btn btn-lg btn-red mb-2 btn-widen mx-2" id="cancelBtn" style="display: none">Cancel</a> 
        </div>
    </form>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">The tenant's details has been updated successfully.</h4>
            </div>
            <div class="d-flex justify-content-center py-1">
              <button type="button" class="btn btn-lg btn-outline-success rounded-pill me-2 px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failEmailAddressPhoneNumberOccupiedStallModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The email address, phone number and stall has already been taken. Please try another.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
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
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The email address and the phone number has already been taken. Please try another.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failEmailAddressOccupiedStallModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The email address and the stall has already been taken. Please try another.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failPhoneNumberOccupiedStallModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The phone number and the stall has already been taken. Please try another.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="failOccupiedStallModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The stall has already been occupied by another tenant. Please try another.</h4>
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
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The email address has already been taken. Please try another.</h4>
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
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Update Unsuccessful!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">The phone number has already been taken. Please try another.</h4>
            </div>
            <div class="py-1">
              <button type="button" class="try-again-button btn btn-lg btn-outline-fail rounded-pill px-5" data-bs-dismiss="modal" style="font-family: 'Switzer-Semibold'">Try Again</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="invalidAvatarModal">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-body text-center">
            <div>
              <i class="fa-solid fa-circle-xmark" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(252,14,85,0.5); color: #FC0E55"></i>
            </div>
            <div class="mt-5 py-2">
              <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Invalid Upload!</p>
              <h4 class="switzer-medium-text display-sm custom-px-4">Please select a JPG or PNG file for the avatar.</h4>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">Unfortunately we encountered an issue with updating the tenant's details. Please try again later.</h4>
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

  <script>
      document.getElementById("editBtn").addEventListener("click", function() {
        document.getElementById("editBtn").style.display = "none";
        document.getElementById("saveBtn").style.display = "block";
        document.getElementById("cancelBtn").style.display = "block";
        document.getElementById("imgUploadBtn").style.display = "inline-block";

        var inputFields = document.querySelectorAll(".input-field");
          inputFields.forEach(function(input) {
              input.disabled = false;
        });
      });

      document.getElementById("cancelBtn").addEventListener("click", function() {
        document.getElementById("editBtn").style.display = "block";
        document.getElementById("saveBtn").style.display = "none";
        document.getElementById("cancelBtn").style.display = "none";
        document.getElementById("imgUploadBtn").style.display = "none";

        var inputFields = document.querySelectorAll(".input-field");
          inputFields.forEach(function(input) {
              input.disabled = true;
        });

        window.location.href = "owner-users-tenants-view.php?user_registration_id=<?php echo $tenant_id; ?>";
      });
    </script>

    <script>

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

      var avatarFileUpload = document.getElementById('AvatarFileUpload');
      var imageViewer = avatarFileUpload.querySelector('.selected-image-holder>img');
      var imageSelector = avatarFileUpload.querySelector('.avatar-selector-btn');
      var imageInput = avatarFileUpload.querySelector('input[name="avatar"]');

      imageSelector.addEventListener('click', e => {
          e.preventDefault();
          imageInput.click();
      });

      imageInput.addEventListener('change', e => {
          const file = e.target.files[0];
          if (file) {
              const fileType = file.type;
              if (fileType === 'image/jpeg' || fileType === 'image/png') {
                  var reader = new FileReader();
                  reader.onload = function() {
                      imageViewer.src = reader.result;
                  };
                  reader.readAsDataURL(file);
              } else {
                  $('#invalidAvatarModal').modal('show');
                  imageInput.value = '';
                  imageViewer.src = '../Developer things/assets/img/default-avatar.png';
              }
          }
      });

      function validateProfileForm(){
        if(emailAddressStatus == true && addressStatus == true) {
            return true;
            
        } else {
        return false;
        
        }
      }
    </script>

    <script>
      profileForm.addEventListener("submit", function(event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append("tenant_id", "<?php echo $tenant_id; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/owner-users-tenants-edit-connect.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if(response.success) {
                      $('#successModal').modal('show');
                      
                    } else {
                      if (response.errors.includes("Error: Email address, phone number and occupied stall already exists.")) {
                        $('#failEmailAddressPhoneNumberOccupiedStallModal').modal('show');
                      } else if (response.errors.includes("Error: Email address and occupied stall already exists.")) {
                        $('#failEmailAddressOccupiedStallModal').modal('show');
                      } else if (response.errors.includes("Error: Phone number and occupied stall already exists.")) {
                        $('#failPhoneNumberOccupiedStallModal').modal('show');
                      } else if (response.errors.includes("Error: Email address and phone number already exists.")) {
                        $('#failEmailAddressPhoneNumberModal').modal('show');
                      } else if (response.errors.includes("Error: Email address already exists.")) {
                        $('#failEmailAddressModal').modal('show');
                      } else if (response.errors.includes("Error: Phone number already exists.")) {
                        $('#failPhoneNumberModal').modal('show');
                      } else if (response.errors.includes("Error: Stall already occupied.")) {
                        $('#failOccupiedStallModal').modal('show');
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
        var profileForm = document.getElementById('profileForm');

        successModal.addEventListener('hidden.bs.modal', function() {
          window.location.href = "owner-users-tenants-view.php?user_registration_id=<?php echo $tenant_id; ?>";
        });

        continueButton.addEventListener('click', function() {
          window.location.href = "owner-users-tenants-view.php?user_registration_id=<?php echo $tenant_id; ?>";
        });
      });
    </script>                                  
</body>
</html>
