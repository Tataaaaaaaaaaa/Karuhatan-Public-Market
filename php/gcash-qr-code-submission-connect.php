<?php
    $occupiedPhase = $_POST['occupiedPhase'];
    $gcashQRCode = $_FILES['gcashQRCode'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $gcashQRCodeTmpPath = $_FILES['gcashQRCode']['tmp_name'];
        $gcashQRCodeContent = file_get_contents($gcashQRCodeTmpPath);
        $gcashQRCodeContentFileType = $_FILES['gcashQRCode']['type'];
        $gcashQRCodeContentFileName = $_FILES['gcashQRCode']['name'];

        $stmt_gcash_submission = $conn->prepare("UPDATE user_registration SET gcashFile=?, gcashFileType=?, gcashFileName=? WHERE occupiedPhase=?");
        $stmt_gcash_submission->bind_param("ssss", $gcashQRCodeContent ,$gcashQRCodeContentFileType, $gcashQRCodeContentFileName, $occupiedPhase);
        $stmt_gcash_submission->execute();

        echo json_encode(['success' => true]);

        $conn->close();
    }
?>