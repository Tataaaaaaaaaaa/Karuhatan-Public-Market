<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';

    session_start();

    if (isset($_GET['payment_id'])) {
        $payment_id = $_GET["payment_id"];

        $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
        if ($conn->connect_error) {
            echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
        } else {
            $stmt = $conn->prepare("UPDATE tbl_payment SET status='paid', paymentMethod='GCash' WHERE payment_id = ?");
            $stmt->bind_param("i", $payment_id);
            $stmt->execute();

            $stmt_tenant_details = $conn->prepare("SELECT tenant_id FROM tbl_payment WHERE payment_id = ?");
            $stmt_tenant_details->bind_param("i", $payment_id);
            $stmt_tenant_details->execute();
            $result_tenant_details = $stmt_tenant_details->get_result();

            $row = $result_tenant_details->fetch_assoc();

            $tenant_id = $row['tenant_id'];
            
            $stmt_tenant_details_2 = $conn->prepare("SELECT emailAddress FROM user_registration WHERE user_registration_id = ?");
            $stmt_tenant_details_2->bind_param("i", $tenant_id);
            $stmt_tenant_details_2->execute();
            $result_tenant_details_2 = $stmt_tenant_details_2->get_result();

            $row_2 = $result_tenant_details_2->fetch_assoc();

            $emailAddress = $row_2['emailAddress'];

            if ($result_tenant_details_2->num_rows == 0) {
                echo json_encode(array("success" => false, "message" => "Incorrect email."));
            }

            if ($result_tenant_details_2->num_rows == 1) {

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
                $mail->Subject = "Stall Rental Payment Confirmation";
                $mail->Body = <<<EOD
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <title>Payment Received</title>
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
                        <p id="sub-text">We are pleased to inform you that we have successfully received your rental payment for your stall at the Karuhatan Public Market. Thank you for your prompt payment and continued support.</p>
                        <p id="sub-text-v2">Thank You!</p>

                        <hr>

                        <p id="sub-text-v3">The Karuhatan Public Market strives to cater local businesses and vendors that offers a unique array of essentials and goods.</p>

                    </div>
                </body>
                </html>

                EOD;
            
                $mail->send();
            }

            $stmt_tenant_details_2 ->close();
            $conn->close();
            // header('Location: collector-report.php');
            header('Location: ../collector-report.php');
            exit;
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Invalid request method."));
    }
?>


