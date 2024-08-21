<?php
    $tenant_id = $_POST['tenant_id'];
    $lease = $_FILES['lease'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        $leaseTmpPath = $_FILES['lease']['tmp_name'];
        $leaseContent = file_get_contents($leaseTmpPath);
        $leaseFileType = $_FILES['lease']['type'];
        $leaseFileName = $_FILES['lease']['name'];

        $stmt_lease_submission = $conn->prepare("UPDATE user_registration SET tenantLeaseFile=?, tenantLeaseFileType=?, tenantLeaseFileName=? WHERE user_registration_id=?");
        $stmt_lease_submission->bind_param("sssi", $leaseContent ,$leaseFileType, $leaseFileName, $tenant_id);
        $stmt_lease_submission->execute();

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date('Y-m-d H:i:s');
        $action = 'submitLease';

        $stmt_notifications = $conn->prepare("INSERT INTO tbl_notifications (user_id, action_date_time, action) VALUES (?, ?, ?)");
        $stmt_notifications->bind_param("iss", $tenant_id, $currentDateTime, $action);  
        $stmt_notifications->execute();
        $stmt_notifications->close();

        echo json_encode(['success' => true]);

        $conn->close();
    }
?>