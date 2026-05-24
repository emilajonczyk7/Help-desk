<?php
session_start();

require_once '../config.php';

// tylko admin ma dostęp do zarządzania kategoriami
if ($_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może zarządzać kategoriami.";
    exit;
}

$message = "";

// Jeśli wciśnięto przycisk "Dodaj kategorię"
if (isset($_POST['submit_add'])) {
    
    $name = $_POST['name']; 
    
    $zapytanie_dodaj = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($zapytanie_dodaj);
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) {
        $message = "Udało się dodać nową kategorię!";
    } else {
        $message = "Wystąpił błąd podczas dodawania.";
    }
}

// pobranie kategorii z bazy
$zapytanie_lista = "SELECT id, name FROM categories ORDER BY id DESC";
$result = mysqli_query($conn, $zapytanie_lista);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kategorie zgłoszeń</title>
</head>
<body>
    <h2>Kategorie zgłoszeń</h2>
    
    <p><a href="dashboard.php">Powrót do panelu</a></p>
    
    <p style="color: green;"><b><?php echo $message; ?></b></p>

    <h3>Dodaj nową kategorię</h3>
    <form method="POST">
        Nazwa kategorii: <br>
        <input type="text" name="name" required>
        <input type="submit" name="submit_add" value="Dodaj kategorię">
    </form>

    <hr>

    <h3>Lista zapisanych kategorii</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nazwa kategorii</th>
        </tr>

        <?php 
        // sprawdzenie czy są jakieś kategorie w bazie
        if (mysqli_num_rows($result) > 0) {
            
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "</tr>";
            }
            
        } else {
            echo "<tr><td colspan='2'>Brak kategorii w bazie. Dodaj pierwszą!</td></tr>";
        }
        ?>
    </table>
</body>
</html>