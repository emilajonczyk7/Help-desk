<?php
session_start();
require_once 'config.php';

// Pobranie listy kategorii do formularza
$kategorie = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");

if (isset($_POST['submit_ticket'])) {
    
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    // Ustalenie autora (zalogowany użytkownik lub null dla gościa)
    $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; 
    $status = 'nowe'; 
    $attachment = null; 

    // --- LOGIKA OBSŁUGI PLIKÓW ---
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = basename($_FILES['attachment']['name']);
        $file_size = $_FILES['attachment']['size'];
        
        // Generowanie unikalnej nazwy pliku dla uniknięcia kolizji
        $unique_name = time() . "_" . $file_name;
        $upload_dir = 'uploads/';
        $target_file = $upload_dir . $unique_name;

        // Weryfikacja formatu i rozmiaru pliku
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'txt', 'zip'];
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $_SESSION['error_message'] = "Błąd: Niedozwolony format pliku. Można dodawać tylko obrazki, PDF, TXT lub ZIP.";
            header("Location: new_ticket.php");
            exit;
        } elseif ($file_size > 5 * 1024 * 1024) { // Maksymalnie 5 MB
            $_SESSION['error_message'] = "Błąd: Plik jest za duży (maksymalnie 5MB).";
            header("Location: new_ticket.php");
            exit;
        } else {
            // Tworzenie katalogu, jeśli nie istnieje
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Przeniesienie pliku do docelowej lokalizacji
            if (move_uploaded_file($file_tmp, $target_file)) {
                $attachment = $target_file;
            } else {
                $_SESSION['error_message'] = "Wystąpił błąd podczas zapisywania pliku na serwerze.";
                header("Location: new_ticket.php");
                exit;
            }
        }
    }

    // --- ZAPIS ZGŁOSZENIA DO BAZY ---
    if (empty($title) || empty($description) || empty($category_id)) {
        $_SESSION['error_message'] = "Wypełnij wszystkie wymagane pola (Temat, Kategoria, Opis).";
    } else {
        $zapytanie = "INSERT INTO tickets (title, description, category_id, created_by, status, attachment) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $zapytanie);
        
        mysqli_stmt_bind_param($stmt, "ssiiss", $title, $description, $category_id, $created_by, $status, $attachment);
        
        if (mysqli_stmt_execute($stmt)) {
            $new_ticket_id = mysqli_insert_id($conn); 
            $_SESSION['success_message'] = "Twoje zgłoszenie zostało wysłane! Numer Twojego zgłoszenia to: <span class='badge bg-dark fs-5'>#" . $new_ticket_id . "</span>.<br>Zapamiętaj ten numer, aby móc sprawdzać status zgłoszenia bez logowania!";
            header("Location: new_ticket.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Błąd bazy danych podczas zapisu zgłoszenia.";
        }
    }
    
    header("Location: new_ticket.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowe Zgłoszenie - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">

    <nav class="navbar navbar-dark bg-dark mb-5 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="new_ticket.php">🛠️ Help Desk</a>
            <div>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-white me-3 d-none d-sm-inline">Zalogowany jako <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
                    <a href="panel/dashboard.php" class="btn btn-outline-light btn-sm fw-bold">⬅ Panel</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm fw-bold">Zaloguj się</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0">Utwórz nowe zgłoszenie</h3>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="panel/dashboard.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Powrót do panelu</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Powrót do logowania</a>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success fw-bold shadow-sm p-4 text-center">
                        ✅ <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger fw-bold shadow-sm">
                        ✖ <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Temat problemu <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required maxlength="100" placeholder="np. Awaria drukarki na II piętrze">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategoria <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Wybierz kategorię --</option>
                                    <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
                                        <option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Dokładny opis sytuacji <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="5" required placeholder="Opisz dokładnie swój problem..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Załącz plik (Opcjonalnie)</label>
                                <input type="file" name="attachment" class="form-control">
                                <div class="form-text">Dopuszczalne formaty: JPG, PNG, PDF, TXT, ZIP. Maksymalny rozmiar to 5MB.</div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_ticket" class="btn btn-success btn-lg fw-bold shadow-sm">
                                    📨 Wyślij zgłoszenie
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>