<?php
    $applicationId = $_POST['applicationId'];
    $applicantFirstName = $_POST['firstName'];
    $applicantMiddleName = $_POST['middleName'];
    $applicantLastName = $_POST['lastName'];
    $applicantAddress = $_POST['address'];
    $applicantEmailAddress = $_POST['emailAddress'];
    $applicantPhoneNumber = $_POST['phoneNumber'];
    $applicantStallCategory = $_POST['stallCategory'];
    $applicantPreferredStall = $_POST['preferredStall'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_email_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE emailAddress = ? AND application_id !=?");
        $stmt_email_check->bind_param("si", $applicantEmailAddress, $applicationId);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        $stmt_phone_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE phoneNumber = ? AND application_id !=?");
        $stmt_phone_check->bind_param("si", $applicantPhoneNumber, $applicationId);
        $stmt_phone_check->execute();
        $result_phone_check = $stmt_phone_check->get_result();

        $errors = [];

        if($result_email_check->num_rows > 0 && $result_phone_check->num_rows > 0) {
            $errors[] = "Error: Email address and phone number already exists.";

        }
        
        if ($result_phone_check->num_rows > 0) {
            $errors[] = "Error: Phone number already exists.";

        }
        
        if ($result_email_check->num_rows > 0) {
            $errors[] = "Error: Email address already exists.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);

        } else {

            if (empty($applicantPreferredStall)) {
                $applicantPreferredStall = null;
            }

            $stmt_tenant_application_update = $conn->prepare("UPDATE tenant_rental_application SET firstName=?, middleName=?, lastName=?, address=?, emailAddress=?, phoneNumber=?, stallCategory=?, preferredStall=? WHERE application_id=?");
            $stmt_tenant_application_update->bind_param("ssssssssi" ,$applicantFirstName, $applicantMiddleName, $applicantLastName, $applicantAddress, $applicantEmailAddress, $applicantPhoneNumber, $applicantStallCategory, $applicantPreferredStall, $applicationId);
            $stmt_tenant_application_update->execute();

            $validIdTmpPath = $_FILES['validId']['tmp_name'];
            $cedulaTmpPath = $_FILES['cedula']['tmp_name'];

            if ($_FILES['validId']['size'] == 0) {
                
            } else {
                $validIdContent = file_get_contents($validIdTmpPath);
                $validIdFileType = $_FILES['validId']['type'];
                $validIdFileName = $_FILES['validId']['name'];

                $stmt_valid_id_update = $conn->prepare("UPDATE tenant_rental_application SET validIdFile=?, validIdFileType=?, validIdFileName=? WHERE application_id=?");
                $stmt_valid_id_update->bind_param("sssi" ,$validIdContent, $validIdFileType, $validIdFileName, $applicationId);
                $stmt_valid_id_update->execute();
            }

            if ($_FILES['cedula']['size'] == 0) {
                
            } else {
                $cedulaContent = file_get_contents($cedulaTmpPath);
                $cedulaFileType = $_FILES['cedula']['type'];
                $cedulaFileName = $_FILES['cedula']['name'];

                $stmt_cedula_update = $conn->prepare("UPDATE tenant_rental_application SET cedulaFile=?, cedulaFileType=?, cedulaFileName=? WHERE application_id=?");
                $stmt_cedula_update->bind_param("sssi", $cedulaContent, $cedulaFileType, $cedulaFileName, $applicationId);
                $stmt_cedula_update->execute();
            }

            echo json_encode(['success' => true]);
            
        }

        $stmt_email_check->close();
        $stmt_phone_check->close();
        if (isset($stmt_tenant_application_update)) {
            $stmt_tenant_application_update->close();
        }

        if (isset($stmt_valid_id_update)) {
            $stmt_valid_id_update->close();
        }

        if (isset($stmt_cedula_update)) {
            $stmt_cedula_update->close();
        }
        $conn->close();
    }
?>