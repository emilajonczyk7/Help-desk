<?php
require_once 'config.php';

$error_msg = "";

// Kiedy wciśnięto przycisk logowania
if (isset($_POST['submit_login'])) {
    
    // pobranie danych z formularza
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Przygotowanie zapytania chroniącego przed SQL Injection
    $zapytanie = "SELECT id, username, password, role FROM users WHERE username = ? AND active = 1";
    $stmt = $conn->prepare($zapytanie);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // czy znaleziono dokładnie jednego takiego użytkownika
    if ($result->num_rows == 1) {
        
        // Wyciągnięcie danych użytkownika z bazy
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 

            header("Location: panel/dashboard.php");
            exit;
            
        } else {
            $error_msg = "Wpisano nieprawidłowe hasło.";
        }
        
    } else {
        // Nie ma takiego loginu w bazie lub konto ma active = 0
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
</body>
</html>