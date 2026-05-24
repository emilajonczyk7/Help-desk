<?php
session_start();

require_once '../config.php';

// tylko admin i pracownik mają dostęp
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu! Tylko obsługa Help Desku może przeglądać to zgłoszenie.";
    exit;
}

$message = "";
$ticket_id = $_GET['id']; // Pobranie ID zgłoszenia z adresu URL
$current_user_id = $_SESSION['user_id']; // ID aktualnie zalogowanego pracownika

// zmiana statusu i przypisanie pracownika
if (isset($_POST['submit_update'])) {
    $new_status = $_POST['status'];
    $assigned_to = $_POST['assigned_to'];
    
    if ($assigned_to == "") {
        $assigned_to = null;
    }

    // Zapytanie aktualizujące status i przypisanego pracownika
    $zapytanie_update = "UPDATE tickets SET status = ?, assigned_to = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $zapytanie_update);
    mysqli_stmt_bind_param($stmt, "sii", $new_status, $assigned_to, $ticket_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = "Pomyślnie zaktualizowano zgłoszenie!";
    } else {
        $message = "Wystąpił błąd podczas aktualizacji.";
    }
    mysqli_stmt_close($stmt);
}

// dodawanie nowego komentarza
if (isset($_POST['submit_comment'])) {
    $content = $_POST['content'];

    if ($content != "") {
        $zapytanie_komentarz = "INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)";
        $stmt_kom = mysqli_prepare($conn, $zapytanie_komentarz);
        mysqli_stmt_bind_param($stmt_kom, "iis", $ticket_id, $current_user_id, $content);
        
        if (mysqli_stmt_execute($stmt_kom)) {
            $message = "Dodano komentarz!";
        } else {
            $message = "Wystąpił błąd podczas dodawania komentarza.";
        }
        mysqli_stmt_close($stmt_kom);
    }
}

// pełne dane zgłoszenia
$zapytanie_ticket = "
    SELECT t.*, c.name AS nazwa_kategorii, u.username AS zglaszajacy
    FROM tickets t
    LEFT JOIN categories c ON t.category_id = c.id
    LEFT JOIN users u ON t.created_by = u.id
    WHERE t.id = ?
";
$stmt_ticket = mysqli_prepare($conn, $zapytanie_ticket);
mysqli_stmt_bind_param($stmt_ticket, "i", $ticket_id);
mysqli_stmt_execute($stmt_ticket);
$result_ticket = mysqli_stmt_get_result($stmt_ticket);
$ticket = mysqli_fetch_assoc($result_ticket);
mysqli_stmt_close($stmt_ticket);

if (!$ticket) {
    echo "Zgłoszenie o podanym ID nie istnieje.";
    exit;
}

// pobranie listy pracowników do przypisania go do zgłoszenia
$zapytanie_pracownicy = "SELECT id, username FROM users WHERE role = 'user' OR role = 'admin'";
$wynik_pracownicy = mysqli_query($conn, $zapytanie_pracownicy);

// historia komentarzy konkretnego zgłoszenia
$zapytanie_komentarze = "
    SELECT cm.content, cm.created_at, u.username 
    FROM comments cm
    LEFT JOIN users u ON cm.user_id = u.id
    WHERE cm.ticket_id = ?
    ORDER BY cm.id ASC
";
$stmt_comments = mysqli_prepare($conn, $zapytanie_komentarze);
mysqli_stmt_bind_param($stmt_comments, "i", $ticket_id);
mysqli_stmt_execute($stmt_comments);
$wynik_komentarze = mysqli_stmt_get_result($stmt_comments);
mysqli_stmt_close($stmt_comments);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Obsługa zgłoszenia #<?php echo $ticket_id; ?></title>
</head>
<body>
    <h2>Szczegóły zgłoszenia #<?php echo $ticket_id; ?></h2>
    <p><a href="tickets_list.php">⬅ Powrót do listy zgłoszeń</a></p>

    <p style="color: green;"><b><?php echo $message; ?></b></p>

    <table border="1" cellpadding="8" cellspacing="0" style="width: 600px;">
        <tr>
            <td><b>Tytuł:</b></td>
            <td><?php echo $ticket['title']; ?></td>
        </tr>
        <tr>
            <td><b>Kategoria:</b></td>
            <td><?php echo $ticket['nazwa_kategorii'] ? $ticket['nazwa_kategorii'] : 'Brak'; ?></td>
        </tr>
        <tr>
            <td><b>Zgłaszający:</b></td>
            <td><?php echo $ticket['zglaszajacy'] ? $ticket['zglaszajacy'] : '<i>Niezalogowany Gość</i>'; ?></td>
        </tr>
        <tr>
            <td><b>Data utworzenia:</b></td>
            <td><?php echo $ticket['created_at']; ?></td>
        </tr>
        <tr>
            <td><b>Opis problemu:</b></td>
            <td><?php echo $ticket['description']; ?></td>
        </tr>
    </table>

    <hr>

    <h3>Zarządzanie zgłoszeniem</h3>
    <form method="POST">
        Zmień status: <br>
        <select name="status">
            <option value="nowe" <?php if($ticket['status'] == 'nowe') echo 'selected'; ?>>NOWE</option>
            <option value="w trakcie" <?php if($ticket['status'] == 'w trakcie') echo 'selected'; ?>>W TRAKCIE</option>
            <option value="zakończone" <?php if($ticket['status'] == 'zakończone') echo 'selected'; ?>>ZAKOŃCZONE</option>
        </select>
        <br><br>

        Przypisz do pracownika: <br>
        <select name="assigned_to">
            <option value="">-- Brak przypisania --</option>
            <?php 
            while ($pracownik = mysqli_fetch_assoc($wynik_pracownicy)) {
                $selected = ($ticket['assigned_to'] == $pracownik['id']) ? 'selected' : '';
                echo "<option value='" . $pracownik['id'] . "' " . $selected . ">" . $pracownik['username'] . "</option>";
            }
            ?>
        </select>
        <br><br>
        <input type="submit" name="submit_update" value="Zapisz zmiany">
    </form>

    <hr>

    <h3>Historia komunikacji (Komentarze)</h3>
    <div style="background-color: #f9f9f9; padding: 15px; width: 570px; border: 1px solid #ccc;">
        <?php 
        if (mysqli_num_rows($wynik_komentarze) > 0) {
            while ($komentarz = mysqli_fetch_assoc($wynik_komentarze)) {
                echo "<p><b>" . $komentarz['username'] . "</b> [" . $komentarz['created_at'] . "]:<br>";
                echo $komentarz['content'] . "</p><hr>";
            }
        } else {
            echo "<p>Brak komentarzy. Bądź pierwszym, który odpowie!</p>";
        }
        ?>
    </div>

    <h3>Dodaj nową odpowiedź</h3>
    <form method="POST">
        <textarea name="content" rows="5" cols="60" required></textarea><br><br>
        <input type="submit" name="submit_comment" value="Wyślij odpowiedź">
    </form>

</body>
</html>