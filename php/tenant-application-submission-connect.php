<?php
    $userID = $_POST['userID'];
    $applicantFirstName = $_POST['firstName'];
    $applicantMiddleName = $_POST['middleName'];
    $applicantLastName = $_POST['lastName'];
    $applicantAddress = $_POST['address'];
    $applicantEmailAddress = $_POST['emailAddress'];
    $applicantPhoneNumber = $_POST['phoneNumber'];
    $applicantStallCategory = $_POST['stallCategory'];
    $applicantPreferredStall = $_POST['preferredStall'];
    $applicationStatus = "Pending";

    $validIdFile = $_FILES['validId'];
    $cedulaFile = $_FILES['cedula'];

    date_default_timezone_set('Asia/Manila');
    $submission_time = date("Y-m-d H:i:s");


    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {
        $stmt_user_id_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE user_registration_id = ? AND applicationStatus = 'pending'");
        $stmt_user_id_check->bind_param("i", $userID);
        $stmt_user_id_check->execute();
        $result_user_id_check = $stmt_user_id_check->get_result();

        $stmt_email_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE emailAddress = ?");
        $stmt_email_check->bind_param("s", $applicantEmailAddress);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        $stmt_phone_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE phoneNumber = ?");
        $stmt_phone_check->bind_param("s", $applicantPhoneNumber);
        $stmt_phone_check->execute();
        $result_phone_check = $stmt_phone_check->get_result();

        $errors = [];

        if ($result_user_id_check->num_rows > 0) {
            $errors[] = "Error: Already submitted a tenancy application form.";

        } else if($result_email_check->num_rows > 0 && $result_phone_check->num_rows > 0) {
            $errors[] = "Error: Email address and phone number already exists.";

        } else if ($result_phone_check->num_rows > 0) {
            $errors[] = "Error: Phone number already exists.";

        } else if ($result_email_check->num_rows > 0) {
            $errors[] = "Error: Email address already exists.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);

        } else {

            if (empty($applicantPreferredStall)) {
                $applicantPreferredStall = null;
            }

            $validIdTmpPath = $_FILES['validId']['tmp_name'];
            $cedulaTmpPath = $_FILES['cedula']['tmp_name'];

            $validIdContent = file_get_contents($validIdTmpPath);
            $validIdFileType = $_FILES['validId']['type'];
            $validIdFileName = $_FILES['validId']['name'];

            $cedulaContent = file_get_contents($cedulaTmpPath);
            $cedulaFileType = $_FILES['cedula']['type'];
            $cedulaFileName = $_FILES['cedula']['name'];

            $stmt_tenant_application_submission = $conn->prepare("INSERT INTO tenant_rental_application (user_registration_id, firstName, middleName, lastName, address, emailAddress, phoneNumber, stallCategory, preferredStall, submission_time, applicationStatus, validIdFile, validIdFileType, cedulaFile, cedulaFileType, validIdFileName, cedulaFileName) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)");
            $stmt_tenant_application_submission->bind_param("isssssssssssssss", $userID ,$applicantFirstName, $applicantMiddleName, $applicantLastName, $applicantAddress, $applicantEmailAddress, $applicantPhoneNumber, $applicantStallCategory, $applicantPreferredStall, $applicationStatus, $validIdContent, $validIdFileType, $cedulaContent, $cedulaFileType, $validIdFileName, $cedulaFileName);
            $stmt_tenant_application_submission->execute();

            date_default_timezone_set('Asia/Manila');
            $currentDateTime = date('Y-m-d H:i:s');
            $action = 'submitTRA';

            $stmt_notifications = $conn->prepare("INSERT INTO tbl_notifications (user_id, action_date_time, action) VALUES (?, ?, ?)");
            $stmt_notifications->bind_param("iss", $userID, $currentDateTime, $action);  
            $stmt_notifications->execute();
            $stmt_notifications->close();

            echo json_encode(['success' => true]);
        }

        $stmt_email_check->close();
        $stmt_phone_check->close();
        if (isset($stmt_tenant_application_submission)) {
            $stmt_tenant_application_submission->close();
        }
        $conn->close();
    }
?>