<?php
session_start();
require_once 'config.php';

// Jeśli użytkownik jest już zalogowany, od razu rzucamy go do panelu
if (isset($_SESSION['user_id'])) {
    header("Location: panel/dashboard.php");
    exit;
}

if (isset($_POST['submit_login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Wprowadź login i hasło.";
    } else {
        $zapytanie = "SELECT id, username, password, role, active, force_password_change FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $zapytanie);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $wynik = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($wynik)) {
            // Sprawdzamy, czy konto nie jest zablokowane
            if ($user['active'] == 0) {
                $_SESSION['error_message'] = "Twoje konto zostało zablokowane. Skontaktuj się z administratorem.";
            } 
            // Weryfikacja hasła
            else if (password_verify($password, $user['password'])) {
                
                // Zapisujemy dane do sesji
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Sprawdzamy, czy admin wymusił zmianę hasła
                if ($user['force_password_change'] == 1) {
                    header("Location: panel/force_change.php");
                } else {
                    $_SESSION['success_message'] = "Zalogowano pomyślnie!";
                    header("Location: panel/dashboard.php");
                }
                exit;
            } else {
                $_SESSION['error_message'] = "Nieprawidłowe hasło.";
            }
        } else {
            $_SESSION['error_message'] = "Użytkownik o podanym loginie nie istnieje.";
        }
        
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">🛠️ Help Desk</h2>
                    <p class="text-muted">Zaloguj się do systemu wsparcia</p>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger fw-bold shadow-sm">
                        ✖ <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Login:</label>
                                <input type="text" name="username" class="form-control form-control-lg" required placeholder="Wpisz swój login">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Hasło:</label>
                                <input type="password" name="password" class="form-control form-control-lg" required placeholder="••••••••">
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" name="submit_login" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                    🔑 Zaloguj się
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="d-flex flex-column align-items-center gap-2 mt-4">
                    <a href="new_ticket.php" class="text-decoration-none fw-bold">➕ Utwórz zgłoszenie (Gość)</a>
                    <a href="track_ticket.php" class="text-decoration-none fw-bold text-success">🔍 Sprawdź status zgłoszenia</a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>