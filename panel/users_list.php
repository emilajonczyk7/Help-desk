<?php
session_start();

require_once '../config.php';

// czy zalogowany użytkownik to administrator
if ($_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może zarządzać użytkownikami.";
    exit;
}

// Pobieranie wszystkich użytkowników z bazy
$zapytanie = "SELECT id, username, email, role, active FROM users ORDER BY id DESC";
$result = $conn->query($zapytanie);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie użytkownikami</title>
</head>
<body>
    <h2>Zarządzanie użytkownikami</h2>
    
    <p>
        <a href="dashboard.php">Powrót do panelu</a> | 
        <a href="user_add.php">Dodaj nowego użytkownika</a>
    </p>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Login</th>
            <th>Email</th>
            <th>Rola</th>
            <th>Status konta</th>
            <th>Akcje</th>
        </tr>

        <?php 
        // czy w bazie są jacyś użytkownicy
        if ($result->num_rows > 0) {
            
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                
                // Sprawdzanie i wyświetlanie roli 
                echo "<td>";
                if ($row['role'] == 'admin') {
                    echo "Administrator";
                } else if ($row['role'] == 'user') {
                    echo "Pracownik (User)";
                } else {
                    echo "Klient (Guest)";
                }
                echo "</td>";
                
                // Sprawdzanie statusu konta 
                echo "<td>";
                if ($row['active'] == 1) {
                    echo "<span style='color:green;'>Aktywne</span>";
                } else {
                    echo "<span style='color:red;'>Zablokowane</span>";
                }
                echo "</td>";
                
                // Link do edycji z doklejonym ID użytkownika
                echo "<td><a href='user_edit.php?id=" . $row['id'] . "'>Edytuj</a></td>";
                echo "</tr>";
            }
            
        } else {
            echo "<tr><td colspan='6'>Brak użytkowników w systemie.</td></tr>";
        }
        ?>
    </table>
</body>
</html>