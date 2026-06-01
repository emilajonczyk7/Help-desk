<?php
session_start();
require_once '../config.php';

// Zabezpieczenie: dla każdego zalogowanego
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obsługa zapisu danych
if (isset($_POST['submit_profile'])) {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password']; // Opcjonalne

    if (empty($email)) {
        $_SESSION['error_message'] = "Adres e-mail nie może być pusty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Podano nieprawidłowy format e-mail.";
    } else {
        
        // Sprawdzamy czy mail nie należy już do kogoś innego
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $email, $user_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        
        if (mysqli_stmt_num_rows($check) > 0) {
            $_SESSION['error_message'] = "Ten adres e-mail jest już zajęty przez innego użytkownika.";
        } else {
            // Czy użytkownik wpisał nowe hasło?
            if (!empty($new_password)) {
                if (strlen($new_password) < 5) {
                    $_SESSION['error_message'] = "Nowe hasło musi mieć co najmniej 5 znaków.";
                } else {
                    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = mysqli_prepare($conn, "UPDATE users SET email = ?, password = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "ssi", $email, $hashed, $user_id);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['success_message'] = "Pomyślnie zaktualizowano e-mail oraz zmieniono hasło.";
                    }
                }
            } else {
                // Tylko aktualizacja e-maila
                $stmt = mysqli_prepare($conn, "UPDATE users SET email = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "Pomyślnie zaktualizowano adres e-mail.";
                }
            }
        }
        // Wzorzec PRG
        header("Location: user_profile.php");
        exit;
    }
}

// Pobranie danych do wyświetlenia
$stmt = mysqli_prepare($conn, "SELECT username, email, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">👤 Mój profil</h2>
    <a href="dashboard.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Powrót do panelu</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Moje dane konta</h5>
            </div>
            <div class="card-body p-4">
                
                <form method="POST">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">Login:</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label fw-bold text-muted">Rola w systemie:</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['role']); ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adres e-mail:</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-primary mb-3">Zmiana hasła (Opcjonalnie)</h6>
                    <div class="mb-4">
                        <label class="form-label text-muted">Wpisz nowe hasło <b>tylko</b> wtedy, gdy chcesz je zmienić.</label>
                        <input type="password" name="new_password" class="form-control" minlength="5" placeholder="Minimum 5 znaków">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="submit_profile" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            💾 Zapisz zmiany profilu
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>