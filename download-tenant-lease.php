<?php
$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$tenant_id = $_GET['tenant_id'];

$stmt = $conn->prepare("SELECT tenantLeaseFile, tenantLeaseFileType, tenantLeaseFileName FROM user_registration WHERE user_registration_id = ?");
$stmt->bind_param("i", $tenant_id);
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
