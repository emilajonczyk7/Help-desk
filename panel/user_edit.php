<?php
session_start();

require_once '../config.php';

// Sprawdzenie czy użytkownik to admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może dodawać użytkowników.";
    exit;
}

$message = "";

// Pobranie ID użytkownika z paska adresu
$user_id = $_GET['id'];

// wysłanie formularza
if (isset($_POST['submit_edit'])) {
    
    // przypisanie zmiennych z formularza
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // czy checkbox jest zaznaczony
    if (isset($_POST['active'])) {
        $active = 1;
    } else {
        $active = 0;
    }

    // Zapisywanie zmian w bazie
    if ($password != "") {
        // Jeśli wpisano nowe hasło, musimy je zaszyfrować 
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $zapytanie = "UPDATE users SET username = ?, email = ?, role = ?, active = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($zapytanie);
        $stmt->bind_param("sssssi", $username, $email, $role, $active, $hashed_password, $user_id);
    } else {
        // Jeśli hasło zostawiono puste, robimy update bez zmiany hasła
        $zapytanie = "UPDATE users SET username = ?, email = ?, role = ?, active = ? WHERE id = ?";
        $stmt = $conn->prepare($zapytanie);
        $stmt->bind_param("ssssi", $username, $email, $role, $active, $user_id);
    }

    // Wykonanie zapytania i komunikat
    if ($stmt->execute()) {
        $message = "Udało się zapisać zmiany!";
    } else {
        $message = "Wystąpił błąd podczas zapisu.";
    }
}

// pobranie danych do formularza
$zapytanie_dane = "SELECT username, email, role, active FROM users WHERE id = ?";
$stmt2 = $conn->prepare($zapytanie_dane);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result = $stmt2->get_result();

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edycja Użytkownika</title>
</head>
<body>
    <h2>Edycja użytkownika o ID: <?php echo $user_id; ?></h2>
    
    <p><b><?php echo $message; ?></b></p>

    <form method="POST">
        Login: <br>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br><br>
        
        Adres e-mail: <br>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>
        
        Nowe hasło: <br>
        <input type="password" name="password"> (zostaw puste, jeśli nie zmieniasz)<br><br>
        
        Rola w systemie: <br>
        <select name="role">
            <option value="guest">Klient (Guest)</option>
            <option value="user">Pracownik (User)</option>
            <option value="admin">Administrator (Admin)</option>
        </select><br><br>
        
        <input type="checkbox" name="active" value="1" <?php if($user['active'] == 1) { echo "checked"; } ?>> Konto aktywne <br><br>
        
        <input type="submit" name="submit_edit" value="Zapisz zmiany">
    </form>
    
    <br>
    <a href="users_list.php">Powrót do listy</a>

</body>
</html>