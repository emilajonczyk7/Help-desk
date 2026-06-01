<?php
session_start();
require_once '../config.php';

// Zabezpieczenie: tylko admin i pracownik mają tu dostęp
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu! Tylko obsługa Help Desku może przeglądać listę zgłoszeń.";
    exit;
}

// --- Stronicowanie (15 zgłoszeń na stronę) ---
$limit = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Zliczanie zgłoszeń w bazie potrzebne na przeliczenie stron
$zapytanie_count = "SELECT COUNT(id) AS total FROM tickets";
$wynik_count = mysqli_query($conn, $zapytanie_count);
$row_count = mysqli_fetch_assoc($wynik_count);
$total_tickets = $row_count['total'];

// Obliczanie liczby stron  
$total_pages = ceil($total_tickets / $limit);

// Wyciąganie danych z bazy z podmianą kategorii i użytkownika (z ID na nazwy)
$zapytanie_zdloszenia = "
    SELECT 
        t.id, 
        t.title, 
        t.status, 
        t.created_at, 
        c.name AS nazwa_kategorii, 
        u1.username AS zglaszajacy, 
        u2.username AS przypisany_pracownik
    FROM tickets t
    LEFT JOIN categories c ON t.category_id = c.id
    LEFT JOIN users u1 ON t.created_by = u1.id
    LEFT JOIN users u2 ON t.assigned_to = u2.id
    ORDER BY t.id DESC
    LIMIT $limit OFFSET $offset
";

$wynik = mysqli_query($conn, $zapytanie_zdloszenia);

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📋 Wszystkie zgłoszenia (Tickety)</h2>
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
                        <th>Zgłaszający</th>
                        <th>Przypisany pracownik</th>
                        <th>Status</th>
                        <th>Data zgłoszenia</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Sprawdzenie, czy są jakieś zgłoszenia
                    if (mysqli_num_rows($wynik) > 0) {
                        
                        while($wiersz = mysqli_fetch_assoc($wynik)) {
                            echo "<tr>";
                            
                            echo "<td><strong>#" . $wiersz['id'] . "</strong></td>";
                            
                            echo "<td class='text-start fw-bold'>" . htmlspecialchars($wiersz['title']) . "</td>";
                            
                            if ($wiersz['nazwa_kategorii'] != "") {
                                echo "<td>" . htmlspecialchars($wiersz['nazwa_kategorii']) . "</td>";
                            } else {
                                echo "<td class='text-muted'>Brak kategorii</td>";
                            }
                            
                            if ($wiersz['zglaszajacy'] != "") {
                                echo "<td>" . htmlspecialchars($wiersz['zglaszajacy']) . "</td>";
                            } else {
                                echo "<td class='text-muted'><i>Niezalogowany Gość</i></td>";
                            }

                            if ($wiersz['przypisany_pracownik'] != "") {
                                echo "<td>" . htmlspecialchars($wiersz['przypisany_pracownik']) . "</td>";
                            } else {
                                echo "<td><span class='badge bg-secondary'>Nieprzypisane</span></td>";
                            }

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
                            
                            echo "<td>";
                            echo "<a href='ticket_view.php?id=" . $wiersz['id'] . "' class='btn btn-primary btn-sm me-1'>🔍 Obsługa</a>";
                            
                            if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user') {
                                echo "<a href='ticket_delete.php?id=" . $wiersz['id'] . "' onclick='return confirm(\"Czy na pewno chcesz trwale usunąć to zgłoszenie? Tej operacji NIE MOŻNA cofnąć!\")' class='btn btn-outline-danger btn-sm'>🗑️ Usuń</a>";
                            }
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                    } else {
                        echo "<tr><td colspan='8' class='text-muted py-4'>Brak zgłoszeń w systemie.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($total_pages > 1): ?>
    <nav aria-label="Nawigacja po stronach">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <li class="page-item active" aria-current="page">
                        <span class="page-link"><?php echo $i; ?></span>
                    </li>
                <?php else: ?>
                    <li class="page-item">
                        <a class="page-link" href="tickets_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php 
include 'footer.php'; 
?>