<?php
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phoneNumber = $_POST['phoneNumber'];
    $emailAddress = $_POST['emailAddress'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $userRole = "prospective-tenant";

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {
        $stmt_email_check = $conn->prepare("SELECT * FROM user_registration WHERE emailAddress = ?");
        $stmt_email_check->bind_param("s", $emailAddress);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        $stmt_phone_check = $conn->prepare("SELECT * FROM user_registration WHERE phoneNumber = ?");
        $stmt_phone_check->bind_param("s", $phoneNumber);
        $stmt_phone_check->execute();
        $result_phone_check = $stmt_phone_check->get_result();

        $errors = [];

        if($result_email_check->num_rows > 0) {
            $errors[] = "Error: Email address already exists.";
        }

        if($result_phone_check->num_rows > 0) {
            $errors[] = "Error: Phone number already exists.";
        }

        if($result_email_check->num_rows > 0 && $result_phone_check->num_rows > 0) {
            $errors[] = "Error: Email address and phone number already exists.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {
            $stmt_user_registration_insert = $conn->prepare("INSERT INTO user_registration (firstName, lastName, phoneNumber, emailAddress, password, userRole) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_user_registration_insert->bind_param("ssssss", $firstName, $lastName, $phoneNumber, $emailAddress, $hashedPassword, $userRole);  
            $stmt_user_registration_insert->execute();
            
            echo json_encode(['success' => true]);
        }

        $stmt_email_check->close();
        $stmt_phone_check->close();
        if (isset($stmt_user_registration_insert)) {
            $stmt_user_registration_insert->close();
        }
        $conn->close();
    }
?>