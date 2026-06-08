<?php
session_start();
require_once '../config.php';

// dostęp do raportów tylko dla admina
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może przeglądać raporty.";
    exit;
}

// --- POBIERANIE DANYCH STATYSTYCZNYCH Z BAZY ---

// Liczba wszystkich użytkowników
$wynik_users = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users");
$total_users = mysqli_fetch_assoc($wynik_users)['total'];

// Całkowita liczba zgłoszeń
$wynik_tickets = mysqli_query($conn, "SELECT COUNT(id) AS total FROM tickets");
$total_tickets = mysqli_fetch_assoc($wynik_tickets)['total'];

// Liczba zgłoszeń w podziale na statusy
$status_counts = ['nowe' => 0, 'w trakcie' => 0, 'zakończone' => 0];
$wynik_statusy = mysqli_query($conn, "SELECT status, COUNT(id) AS cnt FROM tickets GROUP BY status");
while ($wiersz = mysqli_fetch_assoc($wynik_statusy)) {
    $status_counts[$wiersz['status']] = $wiersz['cnt'];
}

// Zgłoszenia pogrupowane według kategorii
$zapytanie_kategorie = "
    SELECT c.name AS nazwa_kategorii, COUNT(t.id) AS liczba_zgloszen 
    FROM categories c 
    LEFT JOIN tickets t ON c.id = t.category_id 
    GROUP BY c.id 
    ORDER BY liczba_zgloszen DESC
";
$wynik_kat = mysqli_query($conn, $zapytanie_kategorie);

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📊 Raporty i statystyki</h2>
    <a href="dashboard.php" class="btn btn-secondary btn-sm shadow-sm">⬅ Powrót do panelu</a>
</div>

<div class="row mb-4">
    
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white shadow border-0 h-100">
            <div class="card-body text-center">
                <h5 class="card-title">👥 Użytkownicy</h5>
                <h1 class="display-4 fw-bold mb-0"><?php echo $total_users; ?></h1>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-dark text-white shadow border-0 h-100">
            <div class="card-body text-center">
                <h5 class="card-title">📋 Wszystkie Tickety</h5>
                <h1 class="display-4 fw-bold mb-0"><?php echo $total_tickets; ?></h1>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white shadow border-0 h-100">
            <div class="card-body text-center">
                <h5 class="card-title">🔴 Nowe zgłoszenia</h5>
                <h1 class="display-4 fw-bold mb-0"><?php echo $status_counts['nowe']; ?></h1>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white shadow border-0 h-100">
            <div class="card-body text-center">
                <h5 class="card-title">✅ Zakończone</h5>
                <h1 class="display-4 fw-bold mb-0"><?php echo $status_counts['zakończone']; ?></h1>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold">
                📈 Podział według statusu
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-4"><b>Nowe</b></td>
                            <td class="text-end pe-4"><span class="badge bg-danger fs-6"><?php echo $status_counts['nowe']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><b>W trakcie</b></td>
                            <td class="text-end pe-4"><span class="badge bg-warning text-dark fs-6"><?php echo $status_counts['w trakcie']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ps-4"><b>Zakończone</b></td>
                            <td class="text-end pe-4"><span class="badge bg-success fs-6"><?php echo $status_counts['zakończone']; ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Prawa kolumna: Zgłoszenia wg kategorii -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold">
                📁 Obciążenie według kategorii
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kategoria</th>
                            <th class="text-end pe-4">Liczba zgłoszeń</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($wynik_kat) > 0) {
                            while ($kat = mysqli_fetch_assoc($wynik_kat)) {
                                echo "<tr>";
                                echo "<td class='ps-4'>" . htmlspecialchars($kat['nazwa_kategorii']) . "</td>";
                                echo "<td class='text-end pe-4 fw-bold'>" . $kat['liczba_zgloszen'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' class='text-center text-muted py-3'>Brak danych o kategoriach.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php 
include 'footer.php'; 
?>