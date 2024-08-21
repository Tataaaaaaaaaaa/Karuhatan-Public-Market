<?php

    $userRegistrationId = $_GET['user_id'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {

            $stmt = $conn->prepare("DELETE FROM user_registration WHERE user_registration_id=?");
            $stmt->bind_param("i", $userRegistrationId);
            $stmt->execute();
            header("Location: ../owner-users-tenants.php");
            exit();

        }

        $conn->close();
    }
?>