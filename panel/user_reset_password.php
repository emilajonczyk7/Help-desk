<?php
session_start();

require_once '../config.php';

// tylko Admin może tu wejść
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może dodawać użytkowników.";
    exit;
}

// pobranie ID użytkownika któremu resetujemy hasło
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // uniwersalne hasło tymczasowe
    $temporary_password = "Start123!";
    
    $hashed_password = password_hash($temporary_password, PASSWORD_BCRYPT);
    
    $zapytanie = "UPDATE users SET password = ?, force_password_change = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $zapytanie);
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
    $stmt = mysqli_prepare($conn, $zapytanie);
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Pomyślnie zresetowano hasło! Nowe hasło tymczasowe to: <b>" . $temporary_password . "</b>";
    } else {
        $_SESSION['error_message'] = "Wystąpił błąd podczas resetowania hasła.";
    }
    
    mysqli_stmt_close($stmt);
}

header("Location: users_list.php");
exit;
?>