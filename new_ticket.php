<?php
require_once 'config.php';

$message = "";

// Kiedy formularz zostanie wysłany
if (isset($_POST['submit_ticket'])) {
    
    // Pobranie danych z formularza
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    // czy zgłoszenie wysyła ktoś zalogowany czy anonimowy gość
    if (isset($_SESSION['user_id'])) {
        $created_by = $_SESSION['user_id'];
    } else {
        $created_by = null;
    }

    $zapytanie_dodaj = "INSERT INTO tickets (title, description, category_id, created_by, status) VALUES (?, ?, ?, ?, 'nowe')";
    $stmt = mysqli_prepare($conn, $zapytanie_dodaj);
    
    mysqli_stmt_bind_param($stmt, "ssii", $title, $description, $category_id, $created_by);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Zgłoszenie zostało wysłane pomyślnie! Pracownicy Help Desk zajmą się nim najszybciej jak to możliwe.";
    } else {
        $message = "Wystąpił błąd podczas wysyłania zgłoszenia. Spróbuj ponownie.";
    }
    
    mysqli_stmt_close($stmt);
}

// pobranie wszystkich kategorii do listy rozwijalnej
$zapytanie_kat = "SELECT id, name FROM categories";
$wynik_kat = mysqli_query($conn, $zapytanie_kat);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nowe zgłoszenie - Help Desk</title>
</head>
<body>
    <h2>Zgłoś awarię / problem techniczny</h2>
    
    <p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="panel/dashboard.php">⬅ Powrót do panelu</a>
        <?php else: ?>
            <a href="login.php">⬅ Strona logowania (dla pracowników)</a>
        <?php endif; ?>
    </p>
    
    <p style="color: green;"><b><?php echo $message; ?></b></p>

    <form method="POST">
        Tytuł zgłoszenia (np. Niedziałająca drukarka): <br>
        <input type="text" name="title" style="width: 300px;" required><br><br>
        
        Kategoria problemu: <br>
        <select name="category_id" required>
            <option value="">-- Wybierz kategorię --</option>
            <?php 
            while ($kategoria = mysqli_fetch_assoc($wynik_kat)) {
                echo "<option value='" . $kategoria['id'] . "'>" . $kategoria['name'] . "</option>";
            }
            ?>
        </select><br><br>
        
        Dokładny opis problemu: <br>
        <textarea name="description" rows="6" cols="40" required></textarea><br><br>
        
        <input type="submit" name="submit_ticket" value="Wyślij zgłoszenie">
    </form>
</body>
</html>