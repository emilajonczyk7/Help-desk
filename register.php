<?php
session_start();
require_once 'config.php';

$message = "";
$error_message = "";

if (isset($_POST['submit_register'])) {
    
    // oczyszczanie danych wejściowych
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; 

    // walidacja danych wejściowych
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "Błąd: Wszystkie pola są wymagane!";
    } else if (strlen($username) < 4) {
        $error_message = "Błąd: Login musi mieć co najmniej 4 znaki.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Błąd: Podano niepoprawny format adresu e-mail!";
    } else if (strlen($password) < 5) {
        $error_message = "Błąd: Hasło musi składać się z minimum 5 znaków.";
    } else {
        
        //Sprawdzanie unikalności loginu i e-maila w bazie
        $zapytanie_sprawdz = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt_sprawdz = mysqli_prepare($conn, $zapytanie_sprawdz);
        mysqli_stmt_bind_param($stmt_sprawdz, "ss", $username, $email);
        mysqli_stmt_execute($stmt_sprawdz);
        mysqli_stmt_store_result($stmt_sprawdz);
        
        if (mysqli_stmt_num_rows($stmt_sprawdz) > 0) {
            $error_message = "Błąd: Użytkownik o takim loginie lub e-mailu już istnieje!";
        } else {
            // hashowanie hasła i zapis nowego użytkownika do bazy
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = 'guest'; 
            $active = 1;  
            
            $zapytanie = "INSERT INTO users (username, password, email, role, active) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $zapytanie);
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $hashed_password, $email, $role, $active);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Rejestracja zakończona sukcesem! Możesz się teraz zalogować.";
            } else {
                $error_message = "Krytyczny błąd bazy danych.";
            }
        }
        mysqli_stmt_close($stmt_sprawdz);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
</head>
<body>
    <h2>Rejestracja nowego Klienta</h2>
    
    <p style="color: green;"><b><?php echo $message; ?></b></p>
    <p style="color: red;"><b><?php echo $error_message; ?></b></p>

    <form method="POST">
        Login:<br>
        <input type="text" name="username" required minlength="4" maxlength="50"><br><br>
        
        Adres e-mail:<br>
        <input type="email" name="email" required maxlength="100"><br><br>
        
        Hasło:<br>
        <input type="password" name="password" required minlength="5"><br><br>
        
        <input type="submit" name="submit_register" value="Zarejestruj się">
    </form>

    <br>
    <hr style="width: 300px; margin-left: 0;">
    <p><a href="login.php">⬅ Masz już konto? Zaloguj się</a></p>

</body>
</html>