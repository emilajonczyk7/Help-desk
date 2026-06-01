<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$role = $_SESSION['role'];

include 'header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Wybierz akcję z menu</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <?php 
                    // Linki widoczne tylko dla administratora
                    if ($role == 'admin') {
                        echo '<a href="users_list.php" class="btn btn-outline-primary text-start p-3 fw-bold">👥 Zarządzanie użytkownikami</a>';
                        echo '<a href="categories.php" class="btn btn-outline-primary text-start p-3 fw-bold">📁 Kategorie zgłoszeń</a>';
                        echo '<a href="reports.php" class="btn btn-outline-primary text-start p-3 fw-bold">📊 Raporty i statystyki systemu</a>';
                    }
                    
                    // Linki widoczne dla pracownika i administratora
                    if ($role == 'admin' || $role == 'user') {
                        echo '<a href="tickets_list.php" class="btn btn-outline-success text-start p-3 fw-bold">📋 Lista wszystkich zgłoszeń</a>';
                    }
                    
                    // Link widoczny dla każdego
                    echo '<a href="../new_ticket.php" class="btn btn-primary text-start p-3 fw-bold shadow-sm">➕ Nowe zgłoszenie (Stwórz Ticketa)</a>';
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php 
include 'footer.php'; 
?>