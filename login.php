<?php
require_once 'config.php';

// Sprawdzamy, czy formularz został wysłany
if (isset($_POST['submit_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Zabezpieczenie przed SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Weryfikacja hasła (zakładając, że hasła w bazie są szyfrowane np. przez password_hash()) 
        // UWAGA: Na etapie testów, jeśli hasła wpiszecie do bazy ręcznie, poniższa funkcja wyrzuci błąd. 
        // Docelowo musimy używać password_verify, ale o tym za chwilę.
        if (password_verify($password, $user['password'])) {
            // Logowanie udane - zapisujemy dane do sesji
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // admin, user, guest

            // Przekierowanie do panelu
            header("Location: panel/dashboard.php");
            exit;
        } else {
            $error_msg = "Nieprawidłowe hasło.";
        }
    } else {
        $error_msg = "Nie znaleziono użytkownika lub konto jest nieaktywne.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - Help Desk</title>
</head>
<body>
    <h2>Logowanie do Systemu Help Desk</h2>
    <form action="login.php" method="POST">
        <label for="username">Login:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Hasło:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" name="submit_login" value="Zaloguj">
    </form>
</body>
</html>