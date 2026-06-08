<?php
session_start();
require_once '../config.php';

// dostęp tylko dla administratora
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu!";
    exit;
}

// walidacja ID użytkownika z URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Nieprawidłowe ID użytkownika.";
    header("Location: users_list.php");
    exit;
}

$reset_user_id = $_GET['id'];
$tymczasowe_haslo = "Start123!";

// Szyfrujemy nowe, tymczasowe hasło
$hashed_password = password_hash($tymczasowe_haslo, PASSWORD_BCRYPT);
$wymus_zmiane = 1;

// Aktualizacja hasła i flagi w bazie danych
$zapytanie = "UPDATE users SET password = ?, force_password_change = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $zapytanie);
mysqli_stmt_bind_param($stmt, "sii", $hashed_password, $wymus_zmiane, $reset_user_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Hasło użytkownika (ID: $reset_user_id) zostało zresetowane na: <b>$tymczasowe_haslo</b>";
} else {
    $_SESSION['error_message'] = "Wystąpił błąd podczas resetowania hasła.";
}

mysqli_stmt_close($stmt);

header("Location: users_list.php");
exit;
?>