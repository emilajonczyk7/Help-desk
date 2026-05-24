<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Pobranie danych zalogowanego użytkownika z sesji
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel - Help Desk</title>
</head>
<body>
    <h2>Witaj w panelu, <?php echo $username; ?>!</h2>
    <p>Twoja rola w systemie to: <strong><?php echo $role; ?></strong></p>
    
    <hr>
    
    <h3>Menu:</h3>
    <ul>
        <?php 

        // Linki widoczne tylko dla administratora
        if ($role == 'admin') {
            echo '<li><a href="users_list.php">Zarządzanie użytkownikami</a></li>';
            echo '<li><a href="categories.php">Kategorie zgłoszeń</a></li>';
        }
        
        // Linki widoczne dla pracownika (user) oraz administratora (admin)
        if ($role == 'admin' || $role == 'user') {
            echo '<li><a href="tickets_list.php">Lista wszystkich zgłoszeń</a></li>';
        }
        
        // Ten link ma być widoczny dla każdego zalogowanego (admin, user, guest)
        echo '<li><a href="../new_ticket.php">Nowe zgłoszenie</a></li>';
        ?>
    </ul>

    <br>
    <a href="../logout.php">Wyloguj się</a>
</body>
</html>