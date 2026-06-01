<?php
session_start();

require_once '../config.php';

// czy zalogowany użytkownik to administrator
if ($_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może zarządzać użytkownikami.";
    exit;
}

// pobieranie użytkowników z bazy
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
    
    <?php include 'flash_messages.php'; ?>

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
            
            // PĘTLA GENERUJĄCA WIERSZE W TABELI
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                
                echo "<td>" . $row['id'] . "</td>";
                
                echo "<td>" . $row['username'] . "</td>";
                
                echo "<td>" . $row['email'] . "</td>";
                
                echo "<td>";
                if ($row['role'] == 'admin') echo "Administrator";
                elseif ($row['role'] == 'user') echo "Pracownik (User)";
                else echo "Klient (Guest)";
                echo "</td>";
                
                echo "<td>";
                if ($row['active'] == 1) {
                    echo "<span style='color: green;'>Aktywne</span>";
                } else {
                    echo "<span style='color: gray;'>Zablokowane</span>";
                }
                echo "</td>";
                
                echo "<td>";
                echo "<a href='user_edit.php?id=" . $row['id'] . "'>Edytuj</a> | ";
                echo "<a href='user_reset_password.php?id=" . $row['id'] . "' onclick='return confirm(\"Czy na pewno chcesz zresetować hasło temu użytkownikowi na tymczasowe: Start123!\")' style='color: red;'>Reset hasła</a>";
                echo "</td>";
                
                echo "</tr>";
            }
            
        } else {
            echo "<tr><td colspan='6'>Brak użytkowników w systemie.</td></tr>";
        }
        ?>
    </table>
</body>
</html>