<?php
    $occupiedPhase = $_POST['occupiedPhase'];
    $gcashPhoneNumber = $_POST['gcashPhoneNumber'];
    $merchantName = $_POST['merchantName'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_gcash_phone_number_submission = $conn->prepare("UPDATE user_registration SET gcashPhoneNumber=?, merchantName=? WHERE occupiedPhase=?");
        $stmt_gcash_phone_number_submission->bind_param("sss", $gcashPhoneNumber, $merchantName, $occupiedPhase);
        $stmt_gcash_phone_number_submission->execute();

        echo json_encode(['success' => true]);

        $conn->close();
    }
?>