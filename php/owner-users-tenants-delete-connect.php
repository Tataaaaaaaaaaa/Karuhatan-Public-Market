<?php

$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
    echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
} else {
    if(isset($_POST['user_registration_id']) && !empty($_POST['user_registration_id'])) {
        $userRegistrationId = $_POST['user_registration_id'];
    
        $stmt = $conn->prepare("DELETE FROM user_registration WHERE user_registration_id = ?");
        $stmt->bind_param("i", $userRegistrationId);
        if($stmt->execute()) {
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    
        $stmt->close();
    } else {
        echo "Error: User registration ID is missing.";
    }
}
?>