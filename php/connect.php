<?php
$host = "localhost"; 
$username = "root"; 
$password = "";
$database = "kpm_tenants_management_db";

// Attempt to connect to the database
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
