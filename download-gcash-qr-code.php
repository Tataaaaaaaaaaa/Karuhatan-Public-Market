<?php
$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$occupiedPhase = $_GET['occupiedPhase'];

$stmt = $conn->prepare("SELECT gcashFile, gcashFileType, gcashFileName FROM user_registration WHERE occupiedPhase = ?");
$stmt->bind_param("s", $occupiedPhase);
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
