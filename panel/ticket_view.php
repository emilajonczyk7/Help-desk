<?php
session_start();
require_once '../config.php';

// dostęp tylko dla admina i pracownika
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu!";
    exit;
}

// walidacja ID zgłoszenia z URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Nieprawidłowe ID zgłoszenia.";
    header("Location: tickets_list.php");
    exit;
}

$ticket_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// Logika aktualizacji statusu i przypisanego pracownika
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
        $_SESSION['success_message'] = "Zaktualizowano status i przypisanie zgłoszenia!";
        header("Location: ticket_view.php?id=" . $ticket_id);
        exit;
    }
}

// Logika dodawania komentarza
if (isset($_POST['submit_comment'])) {
    $content = trim($_POST['content']);
    if ($content != "") {
        $zapytanie_kom = "INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)";
        $stmt_kom = mysqli_prepare($conn, $zapytanie_kom);
        mysqli_stmt_bind_param($stmt_kom, "iis", $ticket_id, $current_user_id, $content);
        if (mysqli_stmt_execute($stmt_kom)) {
            $_SESSION['success_message'] = "Dodano odpowiedź do zgłoszenia!";
            header("Location: ticket_view.php?id=" . $ticket_id);
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Komentarz nie może być pusty!";
        header("Location: ticket_view.php?id=" . $ticket_id);
        exit;
    }
}

// Pobranie danych zgłoszenia wraz z kategorią i autorem
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
$ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ticket));

// weryfikacja czy zgłoszenie istnieje
if (!$ticket) {
    $_SESSION['error_message'] = "Zgłoszenie nie istnieje.";
    header("Location: tickets_list.php");
    exit;
}

// Pobranie listy użytkowników do pola wyboru "przypisz do"
$wynik_pracownicy = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'user' OR role = 'admin'");

// Pobranie historii komentarzy
$zapytanie_komentarze = "
    SELECT cm.content, cm.created_at, u.username, u.role 
    FROM comments cm 
    LEFT JOIN users u ON cm.user_id = u.id 
    WHERE cm.ticket_id = ? 
    ORDER BY cm.id ASC
";
$stmt_comments = mysqli_prepare($conn, $zapytanie_komentarze);
mysqli_stmt_bind_param($stmt_comments, "i", $ticket_id);
mysqli_stmt_execute($stmt_comments);
$wynik_komentarze = mysqli_stmt_get_result($stmt_comments);

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">🔍 Zgłoszenie #<?php echo $ticket_id; ?></h2>
    <a href="tickets_list.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Wróć do listy</a>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h3 class="card-title fw-bold text-primary mb-1"><?php echo htmlspecialchars($ticket['title']); ?></h3>
                <div class="text-muted small mb-3">
                    📅 Dodano: <?php echo $ticket['created_at']; ?> | 
                    📁 Kategoria: <?php echo $ticket['nazwa_kategorii'] ? htmlspecialchars($ticket['nazwa_kategorii']) : 'Brak'; ?> | 
                    👤 Zgłaszający: <b><?php echo $ticket['zglaszajacy'] ? htmlspecialchars($ticket['zglaszajacy']) : 'Niezalogowany Gość'; ?></b>
                </div>
            </div>
            <div class="card-body bg-light rounded m-3 p-4 border">
                <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($ticket['description']); ?></p>
            </div>
            
            <?php if ($ticket['attachment'] != ""): ?>
                <div class="card-footer bg-white border-top-0 pb-4">
                    <div class="alert alert-secondary mb-0">
                        <h6 class="fw-bold mb-2">📎 Załączony plik:</h6>
                        <?php 
                        $sciezka = "../" . $ticket['attachment'];
                        $nazwa = basename($ticket['attachment']);
                        
                        echo "<a href='$sciezka' target='_blank' class='btn btn-sm btn-outline-dark mb-2'>📥 Pobierz plik ($nazwa)</a><br>";
                        
                        $ext = strtolower(pathinfo($sciezka, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='$sciezka' class='img-fluid rounded shadow-sm' style='max-width: 100%; height: auto; max-height: 300px;' alt='Załącznik'>";
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <h4 class="mb-3">💬 Konwersacja</h4>
        
        <?php if (mysqli_num_rows($wynik_komentarze) > 0): ?>
            <div class="mb-4">
                <?php while ($kom = mysqli_fetch_assoc($wynik_komentarze)): 
                    $bg_color = ($kom['role'] == 'guest' || $kom['role'] == null) ? 'bg-white border' : 'bg-primary text-white shadow-sm';
                    $text_color = ($kom['role'] == 'guest' || $kom['role'] == null) ? 'text-dark' : 'text-white';
                    $align = ($kom['role'] == 'guest' || $kom['role'] == null) ? 'text-start' : 'text-end';
                ?>
                    <div class="card mb-3 <?php echo $bg_color; ?>" style="max-width: 85%; <?php echo ($align == 'text-end') ? 'margin-left: auto;' : ''; ?>">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-1 <?php echo $text_color; ?>">
                                <strong><?php echo $kom['username'] ? htmlspecialchars($kom['username']) : 'Gość'; ?></strong>
                                <small style="opacity: 0.8;"><?php echo $kom['created_at']; ?></small>
                            </div>
                            <div class="mb-0 <?php echo $text_color; ?>" style="white-space: pre-wrap;"><?php echo htmlspecialchars($kom['content']); ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-light border mb-4 text-center text-muted">
                Brak odpowiedzi. Rozpocznij konwersację.
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Napisz odpowiedź:</label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="Wpisz treść wiadomości..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" name="submit_comment" class="btn btn-primary fw-bold px-4">
                            ✉️ Wyślij odpowiedź
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">⚙️ Zarządzaj zgłoszeniem</h5>
            </div>
            <div class="card-body bg-light">
                <form method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status:</label>
                        <select name="status" class="form-select fw-bold <?php 
                            if($ticket['status'] == 'nowe') echo 'text-danger';
                            elseif($ticket['status'] == 'w trakcie') echo 'text-warning';
                            else echo 'text-success';
                        ?>">
                            <option value="nowe" <?php if($ticket['status'] == 'nowe') echo 'selected'; ?>>🔴 Nowe</option>
                            <option value="w trakcie" <?php if($ticket['status'] == 'w trakcie') echo 'selected'; ?>>🟡 W trakcie</option>
                            <option value="zakończone" <?php if($ticket['status'] == 'zakończone') echo 'selected'; ?>>🟢 Zakończone</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Przypisany pracownik:</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">-- Brak (Nieprzypisane) --</option>
                            <?php while ($p = mysqli_fetch_assoc($wynik_pracownicy)): ?>
                                <option value="<?php echo $p['id']; ?>" <?php if($ticket['assigned_to'] == $p['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($p['username']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="submit_update" class="btn btn-dark fw-bold">
                            💾 Zapisz zmiany
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>