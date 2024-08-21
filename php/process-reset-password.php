<?php
    $token = $_POST["token"];
    $token_hash = hash("sha256", $token);

    $conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
    if ($conn->connect_error) {
        echo json_encode(array("success" => false, "message" => "Connection failed: " . $conn->connect_error));
    } else {
        $stmt = $conn->prepare("SELECT * FROM user_registration WHERE resetTokenHash = ?");
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $password_hash= password_hash($_POST["password"], PASSWORD_DEFAULT);

        if ($user === null) {
            header("Location: http://localhost/Developer%20things/token-not-found.html");

        } else if (strtotime($user['resetTokenExpiresAt']) <= time()) {
            header("Location: http://localhost/Developer%20things/token-expired.html");

        }

        $replacePassStmt = $conn->prepare("UPDATE user_registration SET password = ?, resetTokenHash = NULL, resetTokenExpiresAt = NULL WHERE user_registration_id = ?");
        $replacePassStmt->bind_param("ss", $password_hash, $user["user_registration_id"]);
        $replacePassStmt->execute();

        header("Location: http://localhost/Developer%20things/log-in.html");
    }