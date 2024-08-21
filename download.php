<?php
$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$applicationId = $_GET['application_id'];
$fileType = $_GET['file_type']; // 'validId' or 'cedula'

if ($fileType === 'validId') {
    $stmt = $conn->prepare("SELECT validIdFile, validIdFileType, validIdFileName FROM tenant_rental_application WHERE application_id = ?");
} else if ($fileType === 'cedula') {
    $stmt = $conn->prepare("SELECT cedulaFile, cedulaFileType, cedulaFileName FROM tenant_rental_application WHERE application_id = ?");
} else {
    die('Invalid file type specified.');
}

$stmt->bind_param("i", $applicationId);
$stmt->execute();
$stmt->bind_result($fileContent, $fileMimeType, $fileName);
$stmt->fetch();
$stmt->close();
$conn->close();

if ($fileContent) {
    header("Content-Type: " . $fileMimeType);
    header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
    echo $fileContent;
    exit;
} else {
    die('File not found.');
}
?>
