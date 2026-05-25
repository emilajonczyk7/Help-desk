<?php
session_start();

require_once '../config.php';

// czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    echo "Brak dostępu! Najpierw się zaloguj.";
    exit;
}

$message = "";
$current_user_id = $_SESSION['user_id']; // Pobieramy ID zalogowanej osoby z sesji

// obsługa zmainy danych na formularzu
if (isset($_POST['submit_profile'])) {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // czy pole hasła jest wypełnione
    if ($password != "") {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // aktualizujemy w bazie e-mail oraz nowe hasło
        $zapytanie_update = "UPDATE users SET email = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $zapytanie_update);
        mysqli_stmt_bind_param($stmt, "ssi", $email, $hashed_password, $current_user_id);
    } else {
        $zapytanie_update = "UPDATE users SET email = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $zapytanie_update);
        mysqli_stmt_bind_param($stmt, "si", $email, $current_user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $message = "Twój profil został pomyślnie zaktualizowany!";
    } else {
        $message = "Wystąpił błąd podczas zapisywania zmian.";
    }
    mysqli_stmt_close($stmt);
}

// pobranie aktualnych danych użytkownika do wyświetlenia w formularzu
$zapytanie_dane = "SELECT username, email, role FROM users WHERE id = ?";
$stmt_dane = mysqli_prepare($conn, $zapytanie_dane);
mysqli_stmt_bind_param($stmt_dane, "i", $current_user_id);
mysqli_stmt_execute($stmt_dane);
$result = mysqli_stmt_get_result($stmt_dane);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_dane);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mój Profil - Help Desk</title>
</head>
<body>
    <h2>Ustawienia mojego profilu</h2>
    <p><a href="dashboard.php">⬅ Powrót do panelu</a></p>

    <p style="color: green;"><b><?php echo $message; ?></b></p>

    <form method="POST">
        
        Twój login (brak możliwości zmiany): <br>
        <input type="text" value="<?php echo $user['username']; ?>" disabled><br><br>
        
        Twoja rola w systemie: <br>
        <input type="text" value="<?php echo $user['role']; ?>" disabled><br><br>

        <hr>

        Twój adres e-mail: <br>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>

        Nowe hasło: <br>
        <input type="password" name="password"> (zostaw puste, jeśli nie chcesz zmieniać obecnego)<br><br>

        <input type="submit" name="submit_profile" value="Zapisz zmiany profilu">
    </form>
</body>
</html>