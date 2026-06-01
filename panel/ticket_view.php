<?php
session_start();
require_once '../config.php';

// tylko admin i pracownik
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu!";
    exit;
}

$ticket_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// 1. Zmiana statusu i przypisania
if (isset($_POST['submit_update'])) {
    $new_status = $_POST['status'];
    $assigned_to = $_POST['assigned_to'];
    
    if ($assigned_to == "") {
        $assigned_to = null;
    }

    $zapytanie_update = "UPDATE tickets SET status = ?, assigned_to = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $zapytanie_update);
    mysqli_stmt_bind_param($stmt, "sii", $new_status, $assigned_to, $ticket_id);
    if (mysqli_stmt_execute($stmt)) {
        // Zapisujemy komunikat do sesji i przekierowujemy, by zapobiec ponownemu wysłaniu
        $_SESSION['success_message'] = "Zaktualizowano zgłoszenie!";
        header("Location: ticket_view.php?id=" . $ticket_id);
        exit;
    }
}

// 2. Dodawanie komentarza
if (isset($_POST['submit_comment'])) {
    $content = $_POST['content'];
    if ($content != "") {
        $zapytanie_kom = "INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)";
        $stmt_kom = mysqli_prepare($conn, $zapytanie_kom);
        mysqli_stmt_bind_param($stmt_kom, "iis", $ticket_id, $current_user_id, $content);
        if (mysqli_stmt_execute($stmt_kom)) {
            // PRG po udanym dodaniu komentarza
            $_SESSION['success_message'] = "Dodano odpowiedź!";
            header("Location: ticket_view.php?id=" . $ticket_id);
            exit;
        }
    }
}

// 3. Pobranie danych ticketa
$zapytanie_ticket = "SELECT t.*, c.name AS nazwa_kategorii, u.username AS zglaszajacy FROM tickets t LEFT JOIN categories c ON t.category_id = c.id LEFT JOIN users u ON t.created_by = u.id WHERE t.id = ?";
$stmt_ticket = mysqli_prepare($conn, $zapytanie_ticket);
mysqli_stmt_bind_param($stmt_ticket, "i", $ticket_id);
mysqli_stmt_execute($stmt_ticket);
$ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ticket));

// 4. Lista pracowników
$wynik_pracownicy = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'user' OR role = 'admin'");

// 5. Komentarze
$zapytanie_komentarze = "SELECT cm.content, cm.created_at, u.username FROM comments cm LEFT JOIN users u ON cm.user_id = u.id WHERE cm.ticket_id = ? ORDER BY cm.id ASC";
$stmt_comments = mysqli_prepare($conn, $zapytanie_komentarze);
mysqli_stmt_bind_param($stmt_comments, "i", $ticket_id);
mysqli_stmt_execute($stmt_comments);
$wynik_komentarze = mysqli_stmt_get_result($stmt_comments);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zgłoszenie <?php echo $ticket_id; ?></title>
</head>
<body>
    <h2>Zgłoszenie nr <?php echo $ticket_id; ?></h2>
    <a href="tickets_list.php">Wróć do listy</a><br><br>

    <?php include 'flash_messages.php'; ?>

    <table border="1" cellpadding="5">
        <tr><td><b>Tytuł:</b></td><td><?php echo $ticket['title']; ?></td></tr>
        <tr><td><b>Kategoria:</b></td><td><?php echo $ticket['nazwa_kategorii']; ?></td></tr>
        <tr><td><b>Zgłaszający:</b></td><td><?php echo $ticket['zglaszajacy']; ?></td></tr>
        <tr><td><b>Data:</b></td><td><?php echo $ticket['created_at']; ?></td></tr>
        <tr><td><b>Opis:</b></td><td><?php echo $ticket['description']; ?></td></tr>
    </table>

    <br>
    <div style="border: 1px solid black; padding: 10px; width: 400px; background-color: #eee;">
        <b>Załączony plik:</b><br>
        <?php 
        if ($ticket['attachment'] != "") {
            $sciezka = "../" . $ticket['attachment'];
            $nazwa = basename($ticket['attachment']); // Zwykłe wyciągnięcie nazwy pliku z adresu
            
            echo "<a href='$sciezka' target='_blank'>Pobierz plik ($nazwa)</a><br><br>";
            
            // Prosty if sprawdzający końcówkę pliku, bez skomplikowanych funkcji
            $ext = strtolower(pathinfo($sciezka, PATHINFO_EXTENSION));
            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                echo "<img src='$sciezka' width='300'>";
            }
        } else {
            echo "Brak załącznika.";
        }
        ?>
    </div>
    <br><hr>

    <h3>Zarządzaj zgłoszeniem</h3>
    <form method="POST">
        Status: 
        <select name="status">
            <option value="nowe" <?php if($ticket['status'] == 'nowe') echo 'selected'; ?>>Nowe</option>
            <option value="w trakcie" <?php if($ticket['status'] == 'w trakcie') echo 'selected'; ?>>W trakcie</option>
            <option value="zakończone" <?php if($ticket['status'] == 'zakończone') echo 'selected'; ?>>Zakończone</option>
        </select>
        <br><br>

        Przypisany pracownik:
        <select name="assigned_to">
            <option value="">Brak</option>
            <?php while ($p = mysqli_fetch_assoc($wynik_pracownicy)): ?>
                <option value="<?php echo $p['id']; ?>" <?php if($ticket['assigned_to'] == $p['id']) echo 'selected'; ?>>
                    <?php echo $p['username']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <input type="submit" name="submit_update" value="Zapisz zmiany">
    </form>
    <hr>

    <h3>Komentarze</h3>
    <?php while ($kom = mysqli_fetch_assoc($wynik_komentarze)): ?>
        <p><b><?php echo $kom['username']; ?></b> (<?php echo $kom['created_at']; ?>):<br>
        <?php echo $kom['content']; ?></p>
        <hr>
    <?php endwhile; ?>

    <form method="POST">
        Napisz odpowiedź:<br>
        <textarea name="content" rows="4" cols="50" required></textarea><br>
        <input type="submit" name="submit_comment" value="Wyślij">
    </form>

</body>
</html>