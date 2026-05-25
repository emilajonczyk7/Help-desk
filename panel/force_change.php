<?php
session_start();
require_once '../config.php';

// czy ktoś jest zalogowany?
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['force_password_change'] == 0) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

// Obsługa zapisu nowego hasła
if (isset($_POST['submit_new_password'])) {
    $new_password = $_POST['new_password'];

    if (strlen($new_password) < 5) {
        $message = "Nowe hasło musi mieć co najmniej 5 znaków!";
    } else {
        // szyfrowanie nowego hasła
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $user_id = $_SESSION['user_id'];

        // aktualizujemy hasło w bazie i wyłączamy flagę wymuszenia 
        $zapytanie = "UPDATE users SET password = ?, force_password_change = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $zapytanie);
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['force_password_change'] = 0;
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Wystąpił krytyczny błąd bazy danych.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wymagana zmiana hasła</title>
</head>
<body style="background-color: #fce4e4; font-family: Arial, sans-serif; text-align: center; padding-top: 50px;">

    <div style="background-color: white; width: 400px; margin: 0 auto; padding: 30px; border: 2px solid red; border-radius: 8px;">
        <h2 style="color: red;">Wymagana zmiana hasła!</h2>
        <p>Ze względów bezpieczeństwa, po zresetowaniu hasła przez Administratora, musisz ustawić swoje własne, prywatne hasło, aby uzyskać dostęp do systemu.</p>
        
        <p style="color: red; font-weight: bold;"><?php echo $message; ?></p>

        <form method="POST">
            Wpisz nowe hasło: <br><br>
            <input type="password" name="new_password" required style="padding: 5px; width: 80%;"><br><br>
            <input type="submit" name="submit_new_password" value="Zmień hasło i wejdź" style="padding: 10px; background-color: red; color: white; border: none; font-weight: bold; cursor: pointer;">
        </form>
    </div>

</body>
</html>