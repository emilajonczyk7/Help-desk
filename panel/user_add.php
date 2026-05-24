<?php
session_start();

require_once '../config.php';

// czy użytkownik to administrator
if ($_SESSION['role'] != 'admin') {
    echo "Brak dostępu!";
    exit;
}

$message = "";

// wysłanie formularza
if (isset($_POST['submit_add'])) {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // sprawdzenie checkboxa
    if (isset($_POST['active'])) {
        $active = 1;
    } else {
        $active = 0;
    }

    // Szyfrowanie hasła 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Przygotowanie zapytania do bazy
    $zapytanie = "INSERT INTO users (username, password, email, role, active) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($zapytanie);
    $stmt->bind_param("ssssi", $username, $hashed_password, $email, $role, $active);

    // Wykonanie zapytania i prosty komunikat
    if ($stmt->execute()) {
        $message = "Udało się dodać nowego użytkownika!";
    } else {
        $message = "Wystąpił błąd podczas dodawania.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj Użytkownika</title>
</head>
<body>
    <h2>Dodawanie nowego użytkownika</h2>
    
    <p><b><?php echo $message; ?></b></p>

    <form method="POST">
        Login: <br>
        <input type="text" name="username" required><br><br>
        
        Adres e-mail: <br>
        <input type="email" name="email" required><br><br>
        
        Hasło: <br>
        <input type="password" name="password" required><br><br