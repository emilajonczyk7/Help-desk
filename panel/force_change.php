<?php
session_start();
require_once '../config.php';

// Zabezpieczenie: tylko zalogowani
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['submit_password_change'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    if (strlen($new_password) < 5) {
        $error = "Hasło musi mieć co najmniej 5 znaków.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Podane hasła nie są identyczne.";
    } else {
        // Szyfrujemy nowe hasło
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        // Zmieniamy hasło i zdejmujemy flagę "force_password_change" (ustawiamy na 0)
        $zapytanie = "UPDATE users SET password = ?, force_password_change = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $zapytanie);
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Hasło zostało pomyślnie zmienione! Możesz teraz bezpiecznie korzystać z panelu.";
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Wystąpił błąd bazy danych.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wymagana zmiana hasła</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-warning text-dark text-center py-3 border-0">
                        <h4 class="mb-0 fw-bold">⚠️ Wymagana zmiana hasła</h4>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted text-center mb-4">Witaj, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>! Ze względów bezpieczeństwa, przed kontynuowaniem musisz zmienić swoje tymczasowe hasło na własne.</p>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger fw-bold">✖ <?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nowe hasło:</label>
                                <input type="password" name="new_password" class="form-control form-control-lg" required minlength="5">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Potwierdź nowe hasło:</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg" required minlength="5">
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="submit_password_change" class="btn btn-warning btn-lg fw-bold shadow-sm">
                                    🔒 Zapisz hasło i wejdź
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