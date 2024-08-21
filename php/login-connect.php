<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailAddress = $_POST["emailAddress"];
    $password = $_POST["password"];

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if ($conn->connect_error) {
        echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
    } else {
        $stmt = $conn->prepare("SELECT * FROM user_registration WHERE emailAddress = ?");
        $stmt->bind_param("s", $emailAddress);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                //$_SESSION["emailAddress"] = $row["emailAddress"];
                $_SESSION["userId"] = $row["user_registration_id"];

                if($row["userRole"] == "prospective-tenant") {
                    echo json_encode(array("success" => true, "userType" => "prospective-tenant"));

                } else if ($row["userRole"] == "owner") {
                    echo json_encode(array("success" => true, "userType" => "owner"));

                } else if ($row["userRole"] == "tenant") {
                    echo json_encode(array("success" => true, "userType" => "tenant"));

                } else if ($row["userRole"] == "collector") {
                    echo json_encode(array("success" => true, "userType" => "collector"));
                }
                
                exit();
            } else {
                echo json_encode(array("success" => false, "message" => "Incorrect email or password."));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "User not found."));
        }

        $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request method."));
}
?>
