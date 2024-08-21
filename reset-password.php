<?php
    $token = $_GET["token"];
    $token_hash = hash("sha256", $token);

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if ($conn->connect_error) {
        echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
    } else {
        $stmt = $conn->prepare("SELECT * FROM user_registration WHERE resetTokenHash = ?");
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user === null) {
            header("Location: http://localhost/Developer%20things/token-not-found.html");

        } else if (strtotime($user['resetTokenExpiresAt']) <= time()) {
            header("Location: http://localhost/Developer%20things/token-expired.html");

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

    <link rel="stylesheet" href="css/forgot-password-style.css">
    <link rel="stylesheet" href="bootstrap-5.3.2-dist/css/bootstrap.css">
</head>
<body style="background-image: url('assets/img/gradient-bg-sm.png'); background-position: center; background-size:cover; background-repeat:no-repeat">
  <div class="container-fluid ps-md-0">
    <div class="row g-0">
      <div class="d-none d-md-flex col-md-3 custom-col-6-2 bg-image" style="background-image: url('assets/img/get-started-bg.png')"></div>
        <div class="col-md-9 custom-col-6-1">
          <div class="login d-flex align-items-center">
            <div class="container custom-mt-7">
              <div class="row">
                <div class="col-md-9 col-lg-8 mx-auto">
                  <div class="mb-4">
                      <a href="home.html"><img src="assets/img/Karuhatan Public Market Logo.png" alt="Logo" style="max-width: 250px;"></a>
                  </div>
                  
                  <h3 class="display-4 mb-1 switzer-bold-text col-12">Reset Password</h3>
                  <div class="mb-4">
                    <p class="display-sm switzer-semibold-text col-12">Let's get started by setting up a new password</p>
                  </div>

                  <form method="POST" id="resetPasswordForm" onsubmit="return validateResetPasswordForm()" autocomplete="off">

                    <input type="hidden" name="token" value="<?= htmlspecialchars($token)?>">

                    <div class="form-floating mb-3">
                        <input name="password" type="password" class="form-control form-input-style" id="floatingPassword" placeholder="" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\!\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\]\^\_\`\{\|\}\~])[A-Za-z\d\!\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\]\^\_\`\{\|\}\~]{8,}$" title="Password must be at least 8 characters long, contains an uppercase and lowercase letter, a number, and a special character." required>
                        <label for="floatingPassword" class="form-label-style">Password</label>
                        <span class="toggle-password position-absolute me-3 end-0 top-50 translate-middle-y">
                          <i class="fa-regular fa-eye-slash" id="togglePassword" style="cursor:pointer; color: #283891"></i>
                        </span>

                    </div>

                    <div class="form-floating mb-3">
                        <input name="retypePassword" type="password" class="form-control form-input-style" id="floatingRetypePassword" placeholder="" title="Please re-enter your password." required>
                        <label for="floatingRetypePassword" class="form-label-style">Retype Password</label>
                        <span class="toggle-password position-absolute me-3 end-0 top-50 translate-middle-y">
                          <i class="fa-regular fa-eye-slash" id="toggleRetypePassword" style="cursor:pointer; color: #283891"></i>
                        </span>
                    </div>

                    <div class="d-grid">
                      <button class="btn btn-lg btn-signup btn-login text-uppercase fw-bold mb-2" type="submit">Continue</button> 
                    </div>
                  </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" tabindex="-1" role="dialog" id="successModal">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body text-center">
          <div>
            <i class="fa-solid fa-circle-check" style="margin-top: 40px; background:#fff; border-radius:50%; display:inline-block; width:172px; height:172px; font-size: 172px; box-shadow: 3px 5px 26px rgba(76,217,100,0.5); color: #4CD964"></i>
          </div>
          <div class="mt-5 py-2">
            <p class="custom-px-4 pb-0 mb-1 switzer-bold-text display-6">Password Reset!</p>
            <h4 class="switzer-medium-text display-sm custom-px-4">Your password has been successfully reset. You can now log in with your new password.</h4>
          </div>
          <div class="py-1"><button type="button" class="btn btn-lg btn-outline-success rounded-pill px-5" data-bs-dismiss="modal" id="continueButton" style="font-family: 'Switzer-Semibold'">Continue</button></div>
        </div>
      </div>
    </div>
  </div>

  <script src="bootstrap-5.3.2-dist/js/bootstrap.bundle.js "></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#floatingPassword');

    togglePassword.addEventListener("click", function(){
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);

      this.classList.toggle("fa-eye");
    });

    const toggleRetypePassword = document.querySelector('#toggleRetypePassword');
    const retypePassword = document.querySelector('#floatingRetypePassword');

    toggleRetypePassword.addEventListener("click", function(){
      const type = retypePassword.getAttribute("type") === "password" ? "text" : "password";
      retypePassword.setAttribute("type", type);

      this.classList.toggle("fa-eye");
    });

    var retypePasswordStatus = false;
    var passwordStatus = false;

    var retypePasswordInput = document.getElementById("floatingRetypePassword");
    var passwordInput = document.getElementById("floatingPassword");

    function validatePasswordIdentical() {
      var passwordValue = passwordInput.value;
      var retypePasswordValue = retypePasswordInput.value;

        if (passwordValue !== retypePasswordValue) {
          retypePasswordInput.setCustomValidity("Passwords do not match.");
          retypePasswordStatus = false;
      } else {
          retypePasswordInput.setCustomValidity("");
          retypePasswordStatus = true;
      }
    }

    passwordInput.addEventListener("input", validatePasswordIdentical);
    retypePasswordInput.addEventListener("input", validatePasswordIdentical);

    passwordInput.addEventListener("input", function() {
      var passwordValue = passwordInput.value;
      var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\!\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\]\^\_\`\{\|\}\~])[A-Za-z\d\!\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\]\^\_\`\{\|\}\~]{8,}$/;
      if (!passwordPattern.test(passwordValue)) {
          passwordInput.setCustomValidity("Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.");
          passwordStatus = false;
      } else {
          passwordInput.setCustomValidity("");
          passwordStatus = true;
      }
    });

    function validateResetPasswordForm(){
      if(passwordStatus == true && retypePasswordStatus == true) {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        return false;
      } else {
        return false;
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      var continueButton = document.getElementById('continueButton');
      continueButton.addEventListener('click', function() {
        var resetPasswordForm = document.getElementById('resetPasswordForm');
        resetPasswordForm.setAttribute('action', '../Developer things/php/process-reset-password.php')
        resetPasswordForm.submit();
      });
    });
  </script>
</body>
</html>
