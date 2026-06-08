<?php
session_start();
require_once '../config.php';

// dostęp tylko dla administratora
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może edytować użytkowników.";
    exit;
}

// walidacja ID użytkownika przekazanego w URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Nieprawidłowe ID użytkownika.";
    header("Location: users_list.php");
    exit;
}

$edit_user_id = $_GET['id'];

// obsługa zapisu zmian w danych użytkownika
if (isset($_POST['submit_edit_user'])) {
    
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $active = (int)$_POST['active'];

    // Walidacja pól formularza
    if (empty($email) || empty($role)) {
        $_SESSION['error_message'] = "Błąd: E-mail i Rola to pola wymagane!";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Błąd: Podano niepoprawny format adresu e-mail!";
    } else {
        
        // Sprawdzenie, czy nowy e-mail nie jest już zajęty przez inne konto
        $zapytanie_sprawdz = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt_sprawdz = mysqli_prepare($conn, $zapytanie_sprawdz);
        mysqli_stmt_bind_param($stmt_sprawdz, "si", $email, $edit_user_id);
        mysqli_stmt_execute($stmt_sprawdz);
        mysqli_stmt_store_result($stmt_sprawdz);
        
        if (mysqli_stmt_num_rows($stmt_sprawdz) > 0) {
            $_SESSION['error_message'] = "Błąd: Ten adres e-mail jest już przypisany do innego konta!";
        } else {
            // Aktualizacja danych użytkownika w bazie
            $zapytanie_update = "UPDATE users SET email = ?, role = ?, active = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $zapytanie_update);
            mysqli_stmt_bind_param($stmt, "ssii", $email, $role, $active, $edit_user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Pomyślnie zaktualizowano dane użytkownika!";
                header("Location: users_list.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Wystąpił błąd bazy danych podczas edycji.";
            }
        }
        mysqli_stmt_close($stmt_sprawdz);
    }
}

// Pobranie bieżących danych użytkownika do wypełnienia formularza
$zapytanie_dane = "SELECT username, email, role, active FROM users WHERE id = ?";
$stmt_dane = mysqli_prepare($conn, $zapytanie_dane);
mysqli_stmt_bind_param($stmt_dane, "i", $edit_user_id);
mysqli_stmt_execute($stmt_dane);
$wynik_dane = mysqli_stmt_get_result($stmt_dane);
$user_data = mysqli_fetch_assoc($wynik_dane);

if (!$user_data) {
    $_SESSION['error_message'] = "Użytkownik o podanym ID nie istnieje.";
    header("Location: users_list.php");
    exit;
}

include 'header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Edycja użytkownika</h3>
            <a href="users_list.php" class="btn btn-secondary btn-sm">⬅ Powrót</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Modyfikuj dane konta</h5>
            </div>
            <div class="card-body p-4">
                
                <form method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Login (Zablokowany do edycji):</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adres e-mail:</label>
                        <input type="email" name="email" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rola w systemie:</label>
                        <select name="role" class="form-select" required>
                            <option value="guest" <?php if($user_data['role'] == 'guest') echo 'selected'; ?>>Klient (Guest)</option>
                            <option value="user" <?php if($user_data['role'] == 'user') echo 'selected'; ?>>Pracownik (User)</option>
                            <option value="admin" <?php if($user_data['role'] == 'admin') echo 'selected'; ?>>Administrator</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status konta:</label>
                        <select name="active" class="form-select" required>
                            <option value="1" <?php if($user_data['active'] == 1) echo 'selected'; ?>>Aktywne (Zezwól na logowanie)</option>
                            <option value="0" <?php if($user_data['active'] == 0) echo 'selected'; ?>>Zablokowane (Odmowa dostępu)</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="submit_edit_user" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            💾 Zapisz zmiany
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php 
include 'footer.php'; 
?>