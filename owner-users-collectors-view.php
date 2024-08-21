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

      $collector_id = $_GET['user_registration_id'];

      $stmt_collector_details = $conn->prepare("SELECT * FROM user_registration WHERE user_registration_id = ?");
      $stmt_collector_details->bind_param("i", $collector_id);
      $stmt_collector_details->execute();
      $result_collector_details = $stmt_collector_details->get_result();

      $row = $result_collector_details->fetch_assoc();

      $firstName = $row['firstName'];
      $middleName = $row['middleName'];
      $lastName = $row['lastName'];
      $address = $row['address'];
      $phoneNumber = $row['phoneNumber'];
      $avatar = $row['avatar'];
      $emailAddress_2 = $row['emailAddress'];
          
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

    <link rel="stylesheet" href="css/owner-users-collector-view-style.css">
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
            <h2 class="display-3 switzer-bold-text-white">Collector Details</h2>
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
            <input name="middleName" type="text" class="form-control form-input-style input-field" id="middleName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid middle name." value="<?php echo $middleName; ?>" autocomplete="off" disabled>
            <label for="middleName" class="form-label-style">Middle Name</label>
          </div>
        </div>
        <div class="col-md-3-1">
          <div class="form-floating mb-4">
            <input name="lastName" type="text" class="form-control form-input-style input-field" id="lastName" placeholder="" pattern="[\sA-Za-z]+" title="Please enter a valid last name." value="<?php echo $lastName; ?>" required autocomplete="off" disabled>
            <label for="lastName" class="form-label-style">Last Name</label>
          </div>
        </div>
        <div class="col-md-10 first-col">
          <div class="form-floating mb-4">
            <input name="address" type="text" class="form-control form-input-style input-field" id="address" placeholder="" pattern="^[a-zA-Z0-9\s,.'-]{5,}$" title="Please enter your address." value="<?php echo $address; ?>" autocomplete="off" disabled>
            <label for="address" class="form-label-style">Address</label>
          </div>
        </div>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">The collector's details has been updated successfully.</h4>
            </div>
            <div class="d-flex justify-content-center py-1">
              <button type="button" class="btn btn-lg btn-outline-success rounded-pill me-2 px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button>
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
              <h4 class="switzer-medium-text display-sm custom-px-4">Unfortunately we encountered an issue with updating the the collector's details. Please try again later.</h4>
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

        window.location.href = "owner-users-collectors-view.php?user_registration_id=<?php echo $collector_id; ?>";
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
        formData.append("collector_id", "<?php echo $collector_id; ?>");
    
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../Developer things/php/owner-users-collectors-edit-connect.php", true);
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

    <script>  
      document.addEventListener("DOMContentLoaded", function() {
        var successModal = document.getElementById('successModal');
        var continueButton = document.getElementById('continueButton');
        var profileForm = document.getElementById('profileForm');

        successModal.addEventListener('hidden.bs.modal', function() {
          window.location.href = "owner-users-collectors-view.php?user_registration_id=<?php echo $collector_id; ?>";
        });

        continueButton.addEventListener('click', function() {
          window.location.href = "owner-users-collectors-view.php?user_registration_id=<?php echo $collector_id; ?>";
        });
      });
    </script>                                  
</body>
</html>
