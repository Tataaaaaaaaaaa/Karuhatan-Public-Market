<?php

    $applicationId = $_GET['application_id'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {

            $stmt = $conn->prepare("DELETE FROM tenant_rental_application WHERE application_id=?");
            $stmt->bind_param("i", $applicationId);
            $stmt->execute();
            header("Location: prospective-tenant-my-applications.php");
            exit();

        }

        $conn->close();
    }
?>