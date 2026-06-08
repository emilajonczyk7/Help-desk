<?php
session_start();
require_once '../config.php';

// sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// pobranie listy zgłoszeń użytkownika z przypisanymi nazwami kategorii
$zapytanie = "
    SELECT t.id, t.title, t.status, t.created_at, c.name AS nazwa_kategorii 
    FROM tickets t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.created_by = ?
    ORDER BY t.id DESC
";
$stmt = mysqli_prepare($conn, $zapytanie);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$wynik = mysqli_stmt_get_result($stmt);

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📋 Moje zgłoszenia</h2>
    <a href="dashboard.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Powrót do panelu</a>
</div>
    
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th class="text-start">Temat problemu</th>
                        <th>Kategoria</th>
                        <th>Status</th>
                        <th>Data zgłoszenia</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // wyświetlanie listy zgłoszeń lub komunikatu o ich braku
                    if (mysqli_num_rows($wynik) > 0) {
                        
                        while($wiersz = mysqli_fetch_assoc($wynik)) {
                            echo "<tr>";
                            
                            echo "<td><strong>#" . $wiersz['id'] . "</strong></td>";
                            echo "<td class='text-start fw-bold'>" . htmlspecialchars($wiersz['title']) . "</td>";
                            
                            // obsługa wyświetlania kategorii - jeśli brak, pokazujemy "Brak kategorii"
                            if ($wiersz['nazwa_kategorii'] != "") {
                                echo "<td>" . htmlspecialchars($wiersz['nazwa_kategorii']) . "</td>";
                            } else {
                                echo "<td class='text-muted'>Brak kategorii</td>";
                            }
                            
                            // renderowanie badge'a zależnie od statusu zgłoszenia
                            echo "<td>";
                            if ($wiersz['status'] == 'nowe') {
                                echo "<span class='badge bg-danger'>NOWE</span>";
                            } else if ($wiersz['status'] == 'w trakcie') {
                                echo "<span class='badge bg-warning text-dark'>W TRAKCIE</span>";
                            } else if ($wiersz['status'] == 'zakończone') {
                                echo "<span class='badge bg-success'>ZAKOŃCZONE</span>";
                            }
                            echo "</td>";
                            
                            echo "<td><small>" . $wiersz['created_at'] . "</small></td>";
                            
                            // przekierowanie do widoku śledzenia zgłoszenia
                            echo "<td><a href='../track_ticket.php?ticket_id=" . $wiersz['id'] . "' class='btn btn-info text-white btn-sm fw-bold shadow-sm'>🔍 Sprawdź / Odpisz</a></td>";
                            
                            echo "</tr>";
                        }
                        
                    } else {
                        echo "<tr><td colspan='6' class='text-muted py-5'>Nie masz jeszcze żadnych zgłoszonych problemów.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>