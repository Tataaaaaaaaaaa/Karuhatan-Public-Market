<?php

    $userID = $_POST['userID'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['password'];
    $retypePassword = $_POST['retypePassword'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_password_check = $conn->prepare("SELECT password FROM user_registration WHERE user_registration_id = ?");
        $stmt_password_check->bind_param("i", $userID);
        $stmt_password_check->execute();
        $stmt_password_check->bind_result($dbPassword);
        $stmt_password_check->fetch();
        $stmt_password_check->close();

        $errors = [];

        if (!password_verify($oldPassword, $dbPassword)) {
            $errors[] = "Error: Password does not match.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {

            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user_registration SET password=? WHERE user_registration_id = ?");
            $stmt->bind_param("si", $newHashedPassword, $userID);
            $stmt->execute();

            echo json_encode(['success' => true]);
        }

        $conn->close();
    }
?>