<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    session_start();

    if (isset($_GET['user_registration_id'])) {
        $tenant_id = $_GET["user_registration_id"];

        $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
        if ($conn->connect_error) {
            echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
        } else {

            $stmt_collector_details = $conn->prepare("SELECT * FROM user_registration WHERE user_registration_id = ?");
            $stmt_collector_details->bind_param("i", $tenant_id);
            $stmt_collector_details->execute();
            $result_collector_details = $stmt_collector_details->get_result();

            $row = $result_collector_details->fetch_assoc();

            $firstName = $row['firstName'];
            $lastName = $row['lastName'];
            $emailAddress = $row['emailAddress'];
            $occupiedPhaseRaw = $row['occupiedPhase'];
            $occupiedPhase;

            switch ($occupiedPhaseRaw) {
                case 'phaseOne':
                    $occupiedPhase = 'Phase One';
                    break;
                case 'phaseTwo':
                    $occupiedPhase = 'Phase Two';
                    break;
                case 'phaseThree':
                    $occupiedPhase = 'Phase Three';
                    break;
                case 'phaseFour':
                    $occupiedPhase = 'Phase Four';
                    break;
            }

            if ($result_collector_details->num_rows == 0) {
                echo json_encode(array("success" => false, "message" => "Incorrect email."));
            }

            if ($result_collector_details->num_rows == 1) {

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
                $mail->Subject = "Reminder: Payment of Stall Rental";
                $mail->Body = <<<EOD
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <title>Stall Payment Reminder</title>
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
                        text-align: justify;
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
                        <h1>Reminder</h1>
                        <p id="sub-text">Hi $firstName $lastName, just a quick reminder to pay the stall payment. Please ensure timely payment to avoid any disruptions.</p>
                        <p id="sub-text-v2">Thank you.</p>

                        <hr>

                        <p id="sub-text-v3">The Karuhatan Public Market strives to cater local businesses and vendors that offers a unique array of essentials and goods.</p>

                    </div>
                </body>
                </html>

                EOD;
            
                $mail->send();
            }

            $stmt_collector_details ->close();
            $conn->close();
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Invalid request method."));
    }
?>