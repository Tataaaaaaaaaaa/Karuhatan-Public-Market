<?php
    $userID = $_POST['userID'];
    $receipt = $_FILES['receipt'];

    date_default_timezone_set('Asia/Manila');
    $payment_time = date("Y-m-d H:i:s");

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $errors = [];

        if(empty($receipt)) {
            $errors[] = "Error: No upload.";

        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {

            $receiptTmpPath = $_FILES['receipt']['tmp_name'];
            $receiptContent = file_get_contents($receiptTmpPath);
            $receiptContentFileType = $_FILES['receipt']['type'];
            $receiptContentFileName = $_FILES['receipt']['name'];

            $status = 'pending';

            $stmt_send_payment = $conn->prepare("INSERT INTO tbl_payment (tenant_id, datetime_sent, receiptFile, receiptFileType, receiptFileName, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_send_payment->bind_param("isssss", $userID, $payment_time, $receiptContent, $receiptContentFileType, $receiptContentFileName, $status);  
            $stmt_send_payment->execute();

            echo json_encode(['success' => true]);
        }

        $conn->close();
    }
?>