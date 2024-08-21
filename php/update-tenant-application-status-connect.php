<?php
    $applicationId = $_POST['application_id'];
    $action = $_POST['action'];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if($conn->connect_error) {
        die('Connection Failed : '.$conn->connect_error);
    } else {

        if ($action === 'reject') {
            $sql = "UPDATE tenant_rental_application SET applicationStatus = 'Rejected' WHERE application_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $applicationId);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } elseif ($action === 'approve') {
            $sql = "UPDATE tenant_rental_application SET applicationStatus = 'Approved' WHERE application_id = ?";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $applicationId);
                $stmt->execute();
        
                $sql = "SELECT user_registration_id FROM tenant_rental_application WHERE application_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $applicationId);
                    $stmt->execute();
                    $result = $stmt->get_result();
        
                    if ($row = $result->fetch_assoc()) {
                        $user_id = $row['user_registration_id'];
        
                        $sql = "UPDATE user_registration SET userRole = 'Tenant' WHERE user_registration_id = ?";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();

                            echo json_encode(['success' => true]);
                        } else {

                            echo "Prepare error: " . $conn->error;
                        }
                    } else {

                        echo "No result found for the application ID.";
                    }
                } else {

                    echo "Prepare error: " . $conn->error;
                }
            } else {

                echo "Prepare error: " . $conn->error;
            }
        }

        $stmt->close();
        $conn->close();
    }
?>