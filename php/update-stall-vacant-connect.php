<?php
    $stallid = $_POST['stallid'];
    $dailyRentalPayment = $_POST['dailyRentalPayment_2'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_stall_update = $conn->prepare("UPDATE tbl_stalls SET daily_rental_payment=? WHERE stall_id=?");
        $stmt_stall_update->bind_param("di", $dailyRentalPayment, $stallid);  
        $stmt_stall_update->execute();

        echo json_encode(['success' => true]);

        $conn->close();
    }
?>