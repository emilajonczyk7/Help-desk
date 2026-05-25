<?php
// Pobranie konfiguracji
require_once 'config.php';

$error_msg = ""; 

if (isset($_POST['submit_login'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Przygotowanie zapytania chroniącego przed SQL Injection (pobieramy też force_password_change)
    $zapytanie = "SELECT id, username, password, role, force_password_change FROM users WHERE username = ? AND active = 1";
    $stmt = mysqli_prepare($conn, $zapytanie);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Sprawdzenie, czy znaleziono dokładnie jednego takiego użytkownika
    if (mysqli_num_rows($result) == 1) {
        
        $user = mysqli_fetch_assoc($result);
        
        // Sprawdzenie poprawności zaszyfrowanego hasła
        if (password_verify($password, $user['password'])) {
            
            // Hasło poprawne - zapisujemy dane do sesji
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 
            $_SESSION['force_password_change'] = $user['force_password_change'];

            // Sprawdzamy, gdzie pokierować użytkownika
            if ($_SESSION['force_password_change'] == 1) {
                header("Location: panel/force_change.php");
            } else {
                header("Location: panel/dashboard.php");
            }
            exit;
            
        } else {
            $error_msg = "Wpisano nieprawidłowe hasło.";
        }
        
    } else {
        $error_msg = "Nie znaleziono użytkownika lub konto jest zablokowane.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
    <h2>Logowanie do Systemu Help Desk</h2>
    
    <p style="color: red;"><b><?php echo $error_msg; ?></b></p>

    <form method="POST">
        Login: <br>
        <input type="text" name="username" required><br><br>
        
        Hasło: <br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" name="submit_login" value="Zaloguj">
    </form>

    <br>
    <hr style="width: 300px; margin-left: 0;">
    
    <p>
        Nie masz jeszcze konta w systemie? <br>
        <a href="register.php">Kliknij tutaj, aby się zarejestrować (Klient)</a><br><br>
        
        Chcesz tylko sprawdzić status zgłoszenia? <br>
        <a href="ticket_status.php">Sprawdź status jako Gość</a>
    </p>

</body>
</html>