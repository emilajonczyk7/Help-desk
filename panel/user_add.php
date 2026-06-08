<?php
session_start();
require_once '../config.php';

// dostęp tylko dla administratora
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może dodawać użytkowników.";
    exit;
}

// walidacja ID użytkownika przekazanego w URL
if (isset($_POST['submit_add_user'])) {
    
    // pobranie i oczyszczenie danych z formularza
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // walidacja danych wejściowych
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = "Błąd: Wypełnij wszystkie wymagane pola!";
    } else if (strlen($username) < 4) {
        $_SESSION['error_message'] = "Błąd: Login musi mieć co najmniej 4 znaki.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Błąd: Podano niepoprawny format adresu e-mail!";
    } else if (strlen($password) < 5) {
        $_SESSION['error_message'] = "Błąd: Hasło musi składać się z minimum 5 znaków.";
    } else {
        
        // sprawdzenie, czy użytkownik już istnieje w bazie
        $zapytanie_sprawdz = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt_sprawdz = mysqli_prepare($conn, $zapytanie_sprawdz);
        mysqli_stmt_bind_param($stmt_sprawdz, "ss", $username, $email);
        mysqli_stmt_execute($stmt_sprawdz);
        mysqli_stmt_store_result($stmt_sprawdz);
        
        if (mysqli_stmt_num_rows($stmt_sprawdz) > 0) {
            $_SESSION['error_message'] = "Błąd: Użytkownik o takim loginie lub e-mailu już istnieje w bazie!";
        } else {
            // szyfrowanie hasła i zapis nowego użytkownika
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $active = 1;
            
            $zapytanie = "INSERT INTO users (username, password, email, role, active) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $zapytanie);
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $hashed_password, $email, $role, $active);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Pomyślnie dodano nowego użytkownika: " . $username;
                header("Location: users_list.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Wystąpił błąd bazy danych podczas dodawania.";
            }
        }
        mysqli_stmt_close($stmt_sprawdz);
    }
}

include 'header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Dodaj użytkownika</h3>
            <a href="users_list.php" class="btn btn-secondary btn-sm">⬅ Powrót</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Wprowadź dane nowego konta</h5>
            </div>
            <div class="card-body p-4">
                
                <form method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Login:</label>
                        <input type="text" name="username" class="form-control" required minlength="4" maxlength="50" placeholder="np. jkowalski">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adres e-mail:</label>
                        <input type="email" name="email" class="form-control" required maxlength="100" placeholder="np. jan@example.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hasło początkowe:</label>
                        <input type="password" name="password" class="form-control" required minlength="5" placeholder="Minimum 5 znaków">
                        <div class="form-text">Przy pierwszym logowaniu użytkownik zostanie poproszony o jego zmianę.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rola w systemie:</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Wybierz rolę --</option>
                            <option value="guest">Klient (Guest)</option>
                            <option value="user">Pracownik (User)</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="submit_add_user" class="btn btn-success btn-lg fw-bold">
                            ➕ Dodaj użytkownika
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