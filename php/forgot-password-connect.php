<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $emailAddress = $_POST["emailAddress"];
        $token = bin2hex((random_bytes(16)));
        $token_hash = hash("sha256", $token);
        date_default_timezone_set('Asia/Manila');
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

        $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
        if ($conn->connect_error) {
            echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
        } else {
            $selectStmt = $conn->prepare("SELECT * FROM user_registration WHERE emailAddress = ?");
            $selectStmt ->bind_param("s", $emailAddress);
            $selectStmt ->execute();
            $result = $selectStmt ->get_result();

            if ($result->num_rows == 0) {
                echo json_encode(array("success" => false, "message" => "Incorrect email."));
            }

            if ($result->num_rows == 1) {
                $updateStmt  = $conn->prepare("UPDATE user_registration SET resetTokenHash = ?, resetTokenExpiresAt = ? WHERE emailAddress = ?");
                $updateStmt ->bind_param("sss", $token_hash, $expiry, $emailAddress);
                $updateStmt ->execute();

                if ($updateStmt->affected_rows > 0) {
                    echo json_encode(array("success" => true));

                    $mail = new PHPMailer(true);

                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = "karuhatanpublicmarket@gmail.com";
                    $mail->Password = "yhff fupp cjme vijq";
                    $mail->SMTPSecure = "ssl";
                    $mail->Port = 465;

                    $mail->setFrom("karuhatanpublicmarket@gmail.com");
                    $mail->addAddress($emailAddress);
                    $imgurImageUrl = "https://i.imgur.com/s7M3qti.png";
                    $mail->isHtml(true);
                    $mail->Subject = "Forgot your password?";
                    $mail->Body = <<<EOD
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                        <title>Reset Password</title>
                        <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                        }

                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #fff;
                            border-radius: 10px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            text-align: center;
                        }

                        h1 {
                            color: #333;
                            text-align: center;
                        }

                        #sub-text {
                            line-height: 1.6;
                            margin-bottom: 20px;
                            text-align: center;
                            color: #333;
                            font-size: 20px;
                        }

                        #sub-text-v2 {
                            line-height: 1.6;
                            margin-bottom: 20px;
                            text-align: center;
                            color: #333;
                            font-size: 16px;
                        }

                        #sub-text-v3 {
                            line-height: 1.6;
                            margin-bottom: 20px;
                            text-align: center;
                            color: #666;
                            font-size: 16px;
                        }

                        a {
                            display: inline-block;
                            padding: 20px 30px;
                            border-color: #283891;
                            background-color: #283891;        
                            color: #fff !important;
                            text-decoration: none;
                            border-radius: 15px;
                            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
                            margin-top: 1.5rem;
                            margin-bottom: 1.5rem;          
                            font-weight: bold;  
                            font-size: 1.2rem;
                        }
                        a:hover {
                            background-color: #212E7A;
                            border-color: #212E7A;
                            color: #fff !important;
                        }

                        a:link {
                            text-decoration: none;
                        }
                        
                        a:visited {
                            text-decoration: none;
                        }
                        
                        a:hover {
                            text-decoration: none;
                        }
                        
                        a:active {
                            text-decoration: none;
                        }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <img src="$imgurImageUrl" alt="">
                            <h1>Forgot Your Password?</h1>
                            <p id="sub-text">We received a request to reset your password. To create a new password, click the button below. The link will expire after 30 minutes.</p>
                            <a href="http://localhost/Developer%20things/reset-password.php?token=$token">Reset Password</a>
                            <p id="sub-text-v2">If you do not want to change your password or didn't request a reset, you can ignore and delete this email.</p>

                            <hr>

                            <p id="sub-text-v3">The Karuhatan Public Market strives to cater local businesses and vendors that offers a unique array of essentials and goods.</p>

                        </div>
                    </body>
                    </html>

                    EOD;
                
                    $mail->send();

                } else {
                    echo json_encode(array("success" => false, "message" => "Incorrect email."));
                }
            }

            $selectStmt ->close();
            $conn->close();
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Invalid request method."));
    }
?>