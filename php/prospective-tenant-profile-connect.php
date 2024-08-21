<?php
    $userID = $_POST['userID'];
    $updateFirstName = $_POST['firstName'];
    $updateLastName = $_POST['lastName'];
    $updatedEmailAddress = $_POST['emailAddress'];
    $updatedPhoneNumber = $_POST['phoneNumber'];
    $updatedAvatar = $_FILES['avatar'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_email_check = $conn->prepare("SELECT * FROM user_registration WHERE emailAddress = ? AND user_registration_id !=?");
        $stmt_email_check->bind_param("si", $updatedEmailAddress, $userID);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        $stmt_phoneNumber_check = $conn->prepare("SELECT * FROM user_registration WHERE phoneNumber = ? AND user_registration_id !=?");
        $stmt_phoneNumber_check->bind_param("si", $updatedPhoneNumber, $userID);
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

            $stmt_profile_update = $conn->prepare("UPDATE user_registration SET firstName=?, lastName=?, phoneNumber=?, emailAddress=? WHERE user_registration_id=?");
            $stmt_profile_update->bind_param("ssssi", $updateFirstName, $updateLastName, $updatedPhoneNumber, $updatedEmailAddress, $userID);  
            $stmt_profile_update->execute();
    
            if (!empty($_FILES['avatar']['name'])) {
                $avatarTmpPath = $_FILES['avatar']['tmp_name'];
                
                $avatarContent = file_get_contents($avatarTmpPath);
            
                $updateAvatarQuery = $conn->prepare("UPDATE user_registration SET avatar=? WHERE user_registration_id=?");
                $updateAvatarQuery->bind_param("si", $avatarContent, $userID);
                $updateAvatarQuery->execute();
            }
    
            echo json_encode(['success' => true]);
        }

        $conn->close();
    }
?>