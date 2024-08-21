<?php
    $tenant_id = $_POST['tenant_id'];
    $outstandingBalance = $_POST['outstandingBalance'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt = $conn->prepare("UPDATE user_registration SET outstandingBalance=? WHERE user_registration_id=?");
        $stmt->bind_param("di", $outstandingBalance, $tenant_id);
        $stmt->execute();

        echo json_encode(['success' => true]);

        $conn->close();
    }
?>