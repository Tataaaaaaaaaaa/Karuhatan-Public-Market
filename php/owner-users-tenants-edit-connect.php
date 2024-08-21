<?php
    $tenant_id = $_POST['tenant_id'];
    $updateFirstName = $_POST['firstName'];
    $updateMiddleName = $_POST['middleName'];
    $updateLastName = $_POST['lastName'];
    $updateAddress = $_POST['address'];
    $updatedEmailAddress = $_POST['emailAddress'];
    $updatedPhoneNumber = $_POST['phoneNumber'];
    $updatedOccupiedStall = $_POST['occupiedStall'];
    $updatedStallCategory = $_POST['stallCategory'];
    $updatedAvatar = $_FILES['avatar'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_email_check = $conn->prepare("SELECT * FROM user_registration WHERE emailAddress = ? AND user_registration_id !=?");
        $stmt_email_check->bind_param("si", $updatedEmailAddress, $tenant_id);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        $stmt_phoneNumber_check = $conn->prepare("SELECT * FROM user_registration WHERE phoneNumber = ? AND user_registration_id !=?");
        $stmt_phoneNumber_check->bind_param("si", $updatedPhoneNumber, $tenant_id);
        $stmt_phoneNumber_check->execute();
        $result_phoneNumber_check = $stmt_phoneNumber_check->get_result();

        $stmt_occupiedStall_check = $conn->prepare("SELECT * FROM user_registration WHERE occupiedStall = ? AND user_registration_id !=?");
        $stmt_occupiedStall_check->bind_param("si", $updatedOccupiedStall, $tenant_id);
        $stmt_occupiedStall_check->execute();
        $result_occupiedStall_check = $stmt_occupiedStall_check->get_result();

        $errors = [];

        if($result_email_check->num_rows > 0 && $result_phoneNumber_check->num_rows > 0 && $result_occupiedStall_check->num_rows > 0) {
            $errors[] = "Error: Email address, phone number and occupied stall already exists.";

        } else if($result_email_check->num_rows > 0 && $result_occupiedStall_check->num_rows > 0) {
            $errors[] = "Error: Email address and occupied stall already exists.";

        } else if($result_phoneNumber_check->num_rows > 0 && $result_occupiedStall_check->num_rows > 0) {
            $errors[] = "Error: Phone number and occupied stall already exists.";

        } else if($result_email_check->num_rows > 0 && $result_phoneNumber_check->num_rows > 0) {
            $errors[] = "Error: Email address and phone number already exists.";

        } else if ($result_email_check->num_rows > 0) {
            $errors[] = "Error: Email address already exists.";

        } else if ($result_phoneNumber_check->num_rows > 0) {
            $errors[] = "Error: Phone number already exists.";
        }

        else if ($result_occupiedStall_check->num_rows > 0) {
            $errors[] = "Error: Stall already occupied.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {

            if(empty($updatedOccupiedStall)){
                $updatedOccupiedStall = null;
            }

            $stmt_profile_update = $conn->prepare("UPDATE user_registration SET firstName=?, middleName=?, lastName=?, address=?, phoneNumber=?, emailAddress=?, occupiedStall=?, stallCategory=? WHERE user_registration_id=?");
            $stmt_profile_update->bind_param("ssssssssi", $updateFirstName, $updateMiddleName, $updateLastName, $updateAddress, $updatedPhoneNumber, $updatedEmailAddress, $updatedOccupiedStall, $updatedStallCategory, $tenant_id);  
            $stmt_profile_update->execute();

            $stmt_stall_reset = $conn->prepare("SELECT * FROM tbl_stalls WHERE tenant_id = ?");
            $stmt_stall_reset->bind_param("i", $tenant_id);
            $stmt_stall_reset->execute();
            $result_stall_reset = $stmt_stall_reset->get_result();

            if ($result_stall_reset->num_rows > 0) {
                $update_stmt = $conn->prepare("UPDATE tbl_stalls SET tenant_id = NULL, stall_category = NULL WHERE tenant_id = ?");
                $update_stmt->bind_param("i", $tenant_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $stmt_stall_update = $conn->prepare("UPDATE tbl_stalls SET tenant_id=?, stall_category=?  WHERE stall_name=?");
            $stmt_stall_update->bind_param("iss", $tenant_id, $updatedStallCategory, $updatedOccupiedStall);  
            $stmt_stall_update->execute();

            if (!empty($_FILES['avatar']['name'])) {
                $avatarTmpPath = $_FILES['avatar']['tmp_name'];
                
                $avatarContent = file_get_contents($avatarTmpPath);
            
                $updateAvatarQuery = $conn->prepare("UPDATE user_registration SET avatar=? WHERE user_registration_id=?");
                $updateAvatarQuery->bind_param("si", $avatarContent, $tenant_id);
                $updateAvatarQuery->execute();
            }
    
            echo json_encode(['success' => true]);
        }

        $conn->close();
    }
?>