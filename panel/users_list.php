<?php
session_start();
require_once '../config.php';

// dostęp tylko dla administratora
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może zarządzać użytkownikami.";
    exit;
}

// Pobieranie listy wszystkich użytkowników
$zapytanie = "SELECT id, username, email, role, active FROM users ORDER BY id DESC";
$result = $conn->query($zapytanie);

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">👥 Zarządzanie użytkownikami</h2>
    <a href="user_add.php" class="btn btn-success fw-bold shadow-sm">➕ Dodaj użytkownika</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Rola</th>
                        <th>Status konta</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Wyświetlanie listy użytkowników
                    if ($result->num_rows > 0) {
                        
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td class='fw-bold'>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            
                            // Renderowanie badge'a roli
                            echo "<td>";
                            if ($row['role'] == 'admin') {
                                echo "<span class='badge bg-danger'>Administrator</span>";
                            } elseif ($row['role'] == 'user') {
                                echo "<span class='badge bg-primary'>Pracownik (User)</span>";
                            } else {
                                echo "<span class='badge bg-info text-dark'>Klient (Guest)</span>";
                            }
                            echo "</td>";
                            
                            // Renderowanie statusu konta
                            echo "<td>";
                            if ($row['active'] == 1) {
                                echo "<span class='badge bg-success'>Aktywne</span>";
                            } else {
                                echo "<span class='badge bg-secondary'>Zablokowane</span>";
                            }
                            echo "</td>";
                            
                            // Przyciski akcji
                            echo "<td>";
                            echo "<a href='user_edit.php?id=" . $row['id'] . "' class='btn btn-outline-primary btn-sm me-2'>✏️ Edytuj</a>";
                            echo "<a href='user_reset_password.php?id=" . $row['id'] . "' onclick='return confirm(\"Czy na pewno chcesz zresetować hasło temu użytkownikowi na tymczasowe: Start123!\")' class='btn btn-outline-danger btn-sm'>🔄 Reset hasła</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                    } else {
                        echo "<tr><td colspan='6' class='text-muted py-4'>Brak użytkowników w systemie.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="dashboard.php" class="btn btn-secondary btn-sm">⬅ Powrót do panelu</a>
</div>

<?php 
include 'footer.php'; 
?>