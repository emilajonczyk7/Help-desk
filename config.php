<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dane do logowania do bazy
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "helpdesk";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    echo "Błąd połączenia z bazą danych: " . mysqli_connect_error();
    exit;
}

mysqli_set_charset($conn, "utf8mb4");
?>