    <?php
        session_start();

        include '../Developer things/php/connect.php';

        // if (isset($_SESSION['emailAddress'])) {
        //     $emailAddress = $_SESSION['emailAddress'];

        // } else {
        //     header("Location: log-in.html");
        //     exit();
        // }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstName = $_POST['firstName'];
            $middleName = $_POST['middleName'];
            $lastName = $_POST['lastName'];
            $address = $_POST['address'];
            $emailAddress = $_POST['emailAddress'];
            $phoneNumber = $_POST['phoneNumber'];

            $stmt_email_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE emailAddress = ?");
            $stmt_email_check->bind_param("s", $emailAddress);
            $stmt_email_check->execute();
            $result_email_check = $stmt_email_check->get_result();

            $stmt_phone_check = $conn->prepare("SELECT * FROM tenant_rental_application WHERE phoneNumber = ?");
            $stmt_phone_check->bind_param("s", $phoneNumber);
            $stmt_phone_check->execute();
            $result_phone_check = $stmt_phone_check->get_result();

            $errors = [];

            if($result_email_check->num_rows > 0) {
                $errors[] = "Error: Email address already exists.";
            }
            
            if($result_phone_check->num_rows > 0) {
                $errors[] = "Error: Phone number already exists.";
            }

            if($result_email_check->num_rows > 0 && $result_phone_check->num_rows > 0) {
                $errors[] = "Error: Email address and phone number already exists.";
            }   

            if (empty($errors)) {
                // $uploadDir = "../uploads/";
                // $validIdPath = '';
                // $cedulaPath = '';
        
                // function handleFileUpload($fieldName, &$filePath) {
                //     global $uploadDir;
                //     if ($_FILES[$fieldName]['size'] > 0) {
                //         $fileName = uniqid($fieldName . '_') . '_' . basename($_FILES[$fieldName]['name']);
                //         $filePath = $uploadDir . $fileName;
                //         $fileTempName = $_FILES[$fieldName]['tmp_name'];

                //         if (!move_uploaded_file($fileTempName, $filePath)) {
                //             echo "Error: Failed to upload $fieldName file.";
                //             exit();
                //         }
                //     }
                // }
        
                // handleFileUpload('validId', $validIdPath);
                // handleFileUpload('cedula', $cedulaPath);

                $stmt = $conn->prepare("INSERT INTO tenant_rental_application (firstName, middleName, lastName, address, emailAddress, phoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $firstName, $middleName, $lastName, $address, $emailAddress, $phoneNumber);
                $stmt->execute();

                // $stmt = $conn->prepare("INSERT INTO tenant_rental_application (firstName, middleName, lastName, address, emailAddress, phoneNumber, stallCategory, preferredStall, valid_id_path, cedula_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                // $stmt->bind_param("ssssssssss", $firstName, $middleName, $lastName, $address, $emailAddress, $phoneNumber, $stallCategory, $preferredStall, $validIdPath, $cedulaPath);
                // $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true]);

                } else {
                    echo json_encode(['success' => false, 'errors' => ['Error: Failed to insert data into the database.']]);

                }

                $stmt->close();

            } else {
                echo json_encode(['success' => false, 'errors' => $errors]);

            }

            $result_email_check->close();
            $result_phone_check->close();

            $conn->close();
        }

    ?>