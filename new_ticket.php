<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$error_message = "";

// Pobranie kategorii do listy
$kategorie = mysqli_query($conn, "SELECT id, name FROM categories");

// Kiedy formularz zostanie wysłany
if (isset($_POST['submit_ticket'])) {
    
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $created_by = $_SESSION['user_id'];
    $status = 'nowe';

    $attachment_path = NULL; // Domyślnie brak załącznika

    // OBSŁUGA PLIKU (ZAŁĄCZNIKA)
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'txt']; // Dozwolone rozszerzenia
        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_tmp = $_FILES['attachment']['tmp_name'];
        
        // Pobieramy rozszerzenie pliku
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            if ($file_size < 5000000) { // Limit 5MB
                // Tworzymy unikalną nazwę pliku, żeby się nie nadpisywały (dodajemy czas)
                $new_file_name = time() . "_" . basename($file_name);
                $target_dir = "uploads/";
                $target_file = $target_dir . $new_file_name;

                // Fizyczne przeniesienie pliku do folderu uploads
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $attachment_path = $target_file; // Zapisujemy ścieżkę do bazy
                } else {
                    $error_message = "Błąd zapisu pliku na serwerze.";
                }
            } else {
                $error_message = "Plik jest za duży! Maksymalny rozmiar to 5MB.";
            }
        } else {
            $error_message = "Niedozwolony format pliku! Dozwolone to: JPG, PNG, PDF, TXT.";
        }
    }

    // Jeśli nie było błędu z plikiem, zapisujemy zgłoszenie w bazie
    if (empty($error_message)) {
        // Zapytanie z uwzględnieniem kolumny attachment
        $zapytanie = "INSERT INTO tickets (title, description, category_id, created_by, status, attachment) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $zapytanie);
        
        // "ssiiss" -> string, string, int, int, string, string
        mysqli_stmt_bind_param($stmt, "ssiiss", $title, $description, $category_id, $created_by, $status, $attachment_path);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Zgłoszenie zostało pomyślnie dodane!";
        } else {
            $error_message = "Wystąpił błąd bazy danych.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nowe zgłoszenie</title>
</head>
<body>
    <h2>Utwórz nowe zgłoszenie problemu</h2>
    <p><a href="panel/dashboard.php">⬅ Powrót do panelu</a></p>

    <p style="color: green;"><b><?php echo $message; ?></b></p>
    <p style="color: red;"><b><?php echo $error_message; ?></b></p>

    <form method="POST" enctype="multipart/form-data">
        Tytuł (krótko o problemie):<br>
        <input type="text" name="title" required style="width: 300px;"><br><br>

        Kategoria:<br>
        <select name="category_id" required style="width: 310px;">
            <option value="">-- Wybierz kategorię --</option>
            <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
                <option value="<?php echo $kat['id']; ?>"><?php echo $kat['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        Dokładny opis:<br>
        <textarea name="description" required rows="5" style="width: 300px;"></textarea><br><br>

        Załącznik (np. zrzut ekranu błędu) - <i>Opcjonalnie</i>:<br>
        <input type="file" name="attachment"><br><br>

        <input type="submit" name="submit_ticket" value="Wyślij zgłoszenie">
    </form>
</body>
</html>