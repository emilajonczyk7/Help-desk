<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="pl">
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
        <?php if ($role === 'admin'): ?>
            <li><a href="users_list.php">Zarządzanie użytkownikami</a></li>
            <li><a href="categories.php">Kategorie zgłoszeń</a></li>
        <?php endif; ?>
        
        <?php if ($role === 'admin' || $role === 'user'): ?>
            <li><a href="tickets_list.php">Lista wszystkich zgłoszeń</a></li>
        <?php endif; ?>
        
        <li><a href="../new_ticket.php">Nowe zgłoszenie</a></li>
    </ul>

    <br>
    <a href="../logout.php">Wyloguj się</a>
</body>
</html>