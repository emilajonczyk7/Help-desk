<?php
session_start();
require_once '../config.php';

//tylko administrator ma dostęp
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może dodawać użytkowników.";
    exit;
}

$message = "";
$error_message = "";

if (isset($_POST['submit_add_user'])) {
    
    //Oczyszczanie danych
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // właściwa walidacja 
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = "Błąd: Wypełnij wszystkie wymagane pola!";
    } else if (strlen($username) < 4) {
        $error_message = "Błąd: Login musi mieć co najmniej 4 znaki.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Błąd: Podano niepoprawny format adresu e-mail!";
    } else if (strlen($password) < 5) {
        $error_message = "Błąd: Hasło musi składać się z minimum 5 znaków.";
    } else {
        
        $zapytanie_sprawdz = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt_sprawdz = mysqli_prepare($conn, $zapytanie_sprawdz);
        mysqli_stmt_bind_param($stmt_sprawdz, "ss", $username, $email);
        mysqli_stmt_execute($stmt_sprawdz);
        mysqli_stmt_store_result($stmt_sprawdz);
        
        if (mysqli_stmt_num_rows($stmt_sprawdz) > 0) {
            $error_message = "Błąd: Użytkownik o takim loginie lub e-mailu już istnieje w bazie!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $active = 1;
            
            $zapytanie = "INSERT INTO users (username, password, email, role, active) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $zapytanie);
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $hashed_password, $email, $role, $active);
            
            if (mysqli_stmt_execute($stmt)) {
                // Zapisujemy komunikat w sesji
                $_SESSION['success_message'] = "Sukces! Dodano nowego użytkownika: " . $username;
                
                header("Location: users_list.php");
                exit;
            } else {
                $error_message = "Wystąpił błąd bazy danych podczas dodawania.";
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
    <title>Dodaj użytkownika</title>
</head>
<body>
    <h2>Dodaj nowego użytkownika (Panel Administratora)</h2>
    <p><a href="users_list.php">⬅ Powrót do listy użytkowników</a></p>

    <p style="color: green;"><b><?php echo $message; ?></b></p>
    <p style="color: red;"><b><?php echo $error_message; ?></b></p>

    <form method="POST">
        Login:<br>
        <input type="text" name="username" required minlength="4" maxlength="50"><br><br>
        
        Adres e-mail:<br>
        <input type="email" name="email" required maxlength="100"><br><br>
        
        Hasło początkowe:<br>
        <input type="password" name="password" required minlength="5"><br><br>
        
        Rola w systemie:<br>
        <select name="role" required>
            <option value="">-- Wybierz rolę --</option>
            <option value="guest">Klient (Guest)</option>
            <option value="user">Pracownik (User)</option>
            <option value="admin">Administrator</option>
        </select><br><br>
        
        <input type="submit" name="submit_add_user" value="Dodaj użytkownika">
    </form>
</body>
</html>