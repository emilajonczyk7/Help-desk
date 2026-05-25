<?php
require_once 'config.php';

$message = "";
$error_message = "";

// użytkownik wciska przycisk "Zarejestruj się"
if (isset($_POST['submit_register'])) {
    
    // pobranie danych z formularza
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // stałe wartości klienta
    $role = 'guest'; // dostaje rolę Guest
    $active = 1;     // konto jest aktywne

    // czy ktoś nie próbuje zająć istniejącego loginu
    $zapytanie_sprawdz = "SELECT id FROM users WHERE username = ?";
    $stmt_sprawdz = mysqli_prepare($conn, $zapytanie_sprawdz);
    mysqli_stmt_bind_param($stmt_sprawdz, "s", $username);
    mysqli_stmt_execute($stmt_sprawdz);
    $wynik_sprawdz = mysqli_stmt_get_result($stmt_sprawdz);

    if (mysqli_num_rows($wynik_sprawdz) > 0) {
        // jeżeli login jest zajęty
        $error_message = "Podany login jest już zajęty! Wybierz inną nazwę.";
    } else {
        // jeżeli login jest wolny 
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $zapytanie_dodaj = "INSERT INTO users (username, password, email, role, active) VALUES (?, ?, ?, ?, ?)";
        $stmt_dodaj = mysqli_prepare($conn, $zapytanie_dodaj);
        
        mysqli_stmt_bind_param($stmt_dodaj, "ssssi", $username, $hashed_password, $email, $role, $active);

        if (mysqli_stmt_execute($stmt_dodaj)) {
            $message = "Konto zostało założone pomyślnie! Możesz się teraz zalogować.";
        } else {
            $error_message = "Wystąpił błąd techniczny podczas rejestracji.";
        }
        
        mysqli_stmt_close($stmt_dodaj);
    }
    
    mysqli_stmt_close($stmt_sprawdz);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rejestracja - Help Desk</title>
</head>
<body>
    <h2>Załóż konto w systemie Help Desk</h2>
    
    <p><a href="login.php">⬅ Powrót do strony logowania</a></p>

    <p style="color: green;"><b><?php echo $message; ?></b></p>
    
    <p style="color: red;"><b><?php echo $error_message; ?></b></p>

    <form method="POST">
        Wybierz swój Login: <br>
        <input type="text" name="username" required><br><br>
        
        Twój adres e-mail: <br>
        <input type="email" name="email" required><br><br>
        
        Wpisz bezpieczne Hasło: <br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" name="submit_register" value="Zarejestruj się">
    </form>
</body>
</html>