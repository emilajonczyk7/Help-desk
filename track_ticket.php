<?php
session_start();
require_once 'config.php';

$ticket = null;
$wynik_komentarze = null;
$searched_id = '';

// 1. Obsługa wyszukiwania zgłoszenia
if (isset($_GET['ticket_id']) && is_numeric($_GET['ticket_id'])) {
    $searched_id = (int)$_GET['ticket_id'];

    // Pobranie danych ticketa
    $zapytanie_ticket = "
        SELECT t.*, c.name AS nazwa_kategorii 
        FROM tickets t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.id = ?
    ";
    $stmt_ticket = mysqli_prepare($conn, $zapytanie_ticket);
    mysqli_stmt_bind_param($stmt_ticket, "i", $searched_id);
    mysqli_stmt_execute($stmt_ticket);
    $ticket = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_ticket));

    if ($ticket) {
        // Pobieranie komentarzy dla tego ticketa
        $zapytanie_komentarze = "
            SELECT cm.content, cm.created_at, u.username, u.role 
            FROM comments cm 
            LEFT JOIN users u ON cm.user_id = u.id 
            WHERE cm.ticket_id = ? 
            ORDER BY cm.id ASC
        ";
        $stmt_comments = mysqli_prepare($conn, $zapytanie_komentarze);
        mysqli_stmt_bind_param($stmt_comments, "i", $searched_id);
        mysqli_stmt_execute($stmt_comments);
        $wynik_komentarze = mysqli_stmt_get_result($stmt_comments);
    } else {
        $_SESSION['error_message'] = "Nie znaleziono zgłoszenia o numerze #" . $searched_id;
    }
}

// 2. Obsługa dodawania komentarza przez GOŚCIA
if (isset($_POST['submit_guest_comment'])) {
    $t_id = (int)$_POST['t_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        $guest_user_id = null; 
        
        $zapytanie_kom = "INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)";
        $stmt_kom = mysqli_prepare($conn, $zapytanie_kom);
        mysqli_stmt_bind_param($stmt_kom, "iis", $t_id, $guest_user_id, $content);
        
        if (mysqli_stmt_execute($stmt_kom)) {
            $_SESSION['success_message'] = "Twoja odpowiedź została dodana!";
            header("Location: track_ticket.php?ticket_id=" . $t_id);
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Treść odpowiedzi nie może być pusta.";
        header("Location: track_ticket.php?ticket_id=" . $t_id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprawdź Status Zgłoszenia - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">

    <nav class="navbar navbar-dark bg-dark mb-5 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="new_ticket.php">🛠️ Help Desk</a>
            <div>
                <a href="new_ticket.php" class="btn btn-outline-light btn-sm fw-bold me-2">➕ Nowe zgłoszenie</a>
                <a href="login.php" class="btn btn-primary btn-sm fw-bold">Zaloguj się</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0 fw-bold text-dark">🔍 Śledzenie zgłoszenia</h3>
                    <a href="login.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Powrót do logowania</a>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger fw-bold shadow-sm mb-4">
                        ✖ <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success fw-bold shadow-sm mb-4">
                        ✅ <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Wprowadź numer swojego zgłoszenia</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3">
                            <div class="col-sm-9">
                                <input type="number" name="ticket_id" class="form-control form-control-lg" required placeholder="np. 12" value="<?php echo htmlspecialchars($searched_id); ?>">
                            </div>
                            <div class="col-sm-3 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Sprawdź</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($ticket): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 mb-3">
                                <div>
                                    <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ticket['title']); ?></h3>
                                    <span class="text-muted small">Numer zgłoszenia: <b>#<?php echo $ticket['id']; ?></b> | Data: <?php echo $ticket['created_at']; ?></span>
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    <?php 
                                    if ($ticket['status'] == 'nowe') echo "<span class='badge bg-danger p-2 fs-6 shadow-sm'>🔴 NOWE / OCZEKUJĄCE</span>";
                                    elseif ($ticket['status'] == 'w trakcie') echo "<span class='badge bg-warning text-dark p-2 fs-6 shadow-sm'>🟡 W TRAKCIE NAPRAWY</span>";
                                    else echo "<span class='badge bg-success p-2 fs-6 shadow-sm'>🟢 ZAKOŃCZONE</span>";
                                    ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold text-muted">Opis problemu:</h6>
                                <div class="p-3 bg-light rounded border text-secondary" style="white-space: pre-wrap;"><?php echo htmlspecialchars($ticket['description']); ?></div>
                            </div>

                            <?php if (!empty($ticket['attachment'])): ?>
                                <div class="alert alert-secondary">
                                    <span class="fw-bold">📎 Załączony plik: </span> 
                                    <a href="<?php echo $ticket['attachment']; ?>" target="_blank" class="btn btn-sm btn-dark ms-2">Pobierz załącznik</a>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <h4 class="fw-bold mb-3 text-dark">💬 Historia korespondencji</h4>
                    
                    <?php if (mysqli_num_rows($wynik_komentarze) > 0): ?>
                        <div class="mb-4">
                            <?php while ($kom = mysqli_fetch_assoc($wynik_komentarze)): 
                                $is_staff = ($kom['role'] == 'admin' || $kom['role'] == 'user');
                                $bg_color = $is_staff ? 'bg-primary text-white shadow-sm' : 'bg-white border';
                                $text_color = $is_staff ? 'text-white' : 'text-dark';
                                $align = $is_staff ? 'margin-left: auto;' : '';
                                $author_name = $is_staff ? "💻 Pomoc Techniczna (" . htmlspecialchars($kom['username']) . ")" : "👤 Ty (Gość)";
                            ?>
                                <div class="card mb-3 <?php echo $bg_color; ?>" style="max-width: 85%; <?php echo $align; ?>">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between mb-1 <?php echo $text_color; ?> small">
                                            <strong><?php echo $author_name; ?></strong>
                                            <small style="opacity: 0.8;"><?php echo $kom['created_at']; ?></small>
                                        </div>
                                        <div class="mb-0 <?php echo $text_color; ?>" style="white-space: pre-wrap;"><?php echo htmlspecialchars($kom['content']); ?></div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border mb-4 text-center text-muted">
                            Brak dodatkowych odpowiedzi od serwisu. Twoje zgłoszenie czeka w kolejce.
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="t_id" value="<?php echo $ticket['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Napisz wiadomość do serwisu:</label>
                                    <textarea name="content" class="form-control" rows="3" required placeholder="Jeśli chcesz przekazać dodatkowe informacje, wpisz je tutaj..."></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit_guest_comment" class="btn btn-dark fw-bold px-4">
                                        ✉️ Wyślij wiadomość
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

</body>
</html>