<?php
$conn = new mysqli('localhost', 'root', '', 'kpm_tenants_management_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

$paymentid = $_GET['paymentid'];

$stmt = $conn->prepare("SELECT receiptFile, receiptFileType, receiptFileName FROM tbl_payment WHERE payment_id = ?");

$stmt->bind_param("i", $paymentid);
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
