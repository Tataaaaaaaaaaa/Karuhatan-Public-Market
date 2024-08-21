<?php
    $stallid = $_POST['stallid'];
    $stallname = $_POST['stallname'];
    $stallCategory = $_POST['stallCategory'];
    $dailyRentalPayment = $_POST['dailyRentalPayment'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $stmt_stall_update = $conn->prepare("UPDATE tbl_stalls SET daily_rental_payment=?, stall_category=? WHERE stall_id=?");
        $stmt_stall_update->bind_param("dsi", $dailyRentalPayment, $stallCategory, $stallid);  
        $stmt_stall_update->execute();

        $stmt_stall_update_2 = $conn->prepare("SELECT * FROM user_registration WHERE occupiedStall=?");
        $stmt_stall_update_2->bind_param("s", $stallname);  
        $stmt_stall_update_2->execute();
        $result = $stmt_stall_update_2->get_result();

        if ($result->num_rows > 0) {
            $stmt_stall_update_3 = $conn->prepare("UPDATE user_registration SET stallCategory=? WHERE occupiedStall=?");
            $stmt_stall_update_3->bind_param("ss", $stallCategory, $stallname);  
            $stmt_stall_update_3->execute();
        }

        echo json_encode(['success' => true]);

        $conn->close();
    }
?>