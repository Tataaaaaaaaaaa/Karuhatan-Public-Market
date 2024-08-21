<?php
    $occupiedPhase = $_POST['occupiedPhase'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $address = $_POST['address'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];
    $avatar = $_FILES['avatar'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_email_check = $conn->prepare("SELECT * FROM user_registration WHERE emailAddress = ?");
        $stmt_email_check->bind_param("s", $emailAddress);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        $stmt_phoneNumber_check = $conn->prepare("SELECT * FROM user_registration WHERE phoneNumber = ?");
        $stmt_phoneNumber_check->bind_param("s", $phoneNumber);
        $stmt_phoneNumber_check->execute();
        $result_phoneNumber_check = $stmt_phoneNumber_check->get_result();

        $errors = [];

        if($result_email_check->num_rows > 0 && $result_phoneNumber_check->num_rows > 0) {
            $errors[] = "Error: Email address and phone number already exists.";

        } else if ($result_email_check->num_rows > 0) {
            $errors[] = "Error: Email address already exists.";

        } else if ($result_phoneNumber_check->num_rows > 0) {
            $errors[] = "Error: Phone number already exists.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {

            $userRole = 'collector';

            $stmt_add_collector = $conn->prepare("INSERT INTO user_registration (firstName, middleName, lastName, address, phoneNumber, emailAddress, password, userRole, occupiedPhase) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_add_collector->bind_param("sssssssss", $firstName, $middleName, $lastName, $address , $phoneNumber, $emailAddress, $hashedPassword, $userRole, $occupiedPhase);  
            $stmt_add_collector->execute();
    
            if (!empty($_FILES['avatar']['name'])) {
                $avatarTmpPath = $_FILES['avatar']['tmp_name'];
                
                $avatarContent = file_get_contents($avatarTmpPath);
            
                $updateAvatarQuery = $conn->prepare("INSERT INTO user_registration (avatar) VALUES (?)");
                $updateAvatarQuery->bind_param("s", $avatarContent);
                $updateAvatarQuery->execute();
            }
    
            echo json_encode(['success' => true]);
        }

        $conn->close();
    }
?>