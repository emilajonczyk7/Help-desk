<?php
session_start();

require_once '../config.php';

// tylko admin i pracownik mają tu dostęp
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu! Tylko obsługa Help Desku może przeglądać listę zgłoszeń.";
    exit;
}

// stronicowanie (15 na str)

$limit = 15;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

// zliczanie zgłoszeń w bazie potrzebne na przeliczenie str
$zapytanie_count = "SELECT COUNT(id) AS total FROM tickets";
$wynik_count = mysqli_query($conn, $zapytanie_count);
$row_count = mysqli_fetch_assoc($wynik_count);
$total_tickets = $row_count['total'];

// Obliczanie liczby stron  
$total_pages = ceil($total_tickets / $limit);

// wyciąganie danych z bazy z podmianą kategorii i użytkownika (z id na nazwy)
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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista zgłoszeń - Panel Help Desk</title>
</head>
<body>
    <h2>Wszystkie zgłoszenia (Tickety)</h2>
    
    <?php
    // Wyświetlanie komunikatów z sesji (np. po pomyślnym usunięciu zgłoszenia)
    if (isset($_SESSION['success_message'])) {
        echo "<p style='color: green; font-weight: bold; background-color: #e6f4ea; padding: 10px; border: 1px solid green; display: inline-block;'>" . $_SESSION['success_message'] . "</p>";
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red; font-weight: bold;'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']);
    }
    ?>
    
    <p><a href="dashboard.php">Powrót do panelu</a></p>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Temat problemu</th>
            <th>Kategoria</th>
            <th>Zgłaszający</th>
            <th>Przypisany pracownik</th>
            <th>Status</th>
            <th>Data zgłoszenia</th>
            <th>Akcje</th>
        </tr>

        <?php 
        // Sprawdzenie, czy są jakieś zgłoszenia
        if (mysqli_num_rows($wynik) > 0) {
            
            while($wiersz = mysqli_fetch_assoc($wynik)) {
                echo "<tr>";
                echo "<td>" . $wiersz['id'] . "</td>";
                echo "<td><b>" . $wiersz['title'] . "</b></td>";
                
                if ($wiersz['nazwa_kategorii'] != "") {
                    echo "<td>" . $wiersz['nazwa_kategorii'] . "</td>";
                } else {
                    echo "<td>Brak kategorii</td>";
                }
                
                // zgłaszający = NULL to gość
                if ($wiersz['zglaszajacy'] != "") {
                    echo "<td>" . $wiersz['zglaszajacy'] . "</td>";
                } else {
                    echo "<td><i>Niezalogowany Gość</i></td>";
                }

                // Kto się tym zajmuje
                if ($wiersz['przypisany_pracownik'] != "") {
                    echo "<td>" . $wiersz['przypisany_pracownik'] . "</td>";
                } else {
                    echo "<td><i>Brak (Nieprzypisane)</i></td>";
                }

                // Kolorowanie statusów
                echo "<td>";
                if ($wiersz['status'] == 'nowe') {
                    echo "<span style='color: red; font-weight: bold;'>NOWE</span>";
                } else if ($wiersz['status'] == 'w trakcie') {
                    echo "<span style='color: orange; font-weight: bold;'>W TRAKCIE</span>";
                } else if ($wiersz['status'] == 'zakończone') {
                    echo "<span style='color: green; font-weight: bold;'>ZAKOŃCZONE</span>";
                }
                echo "</td>";
                
                echo "<td>" . $wiersz['created_at'] . "</td>";
                
                // Kolumna z akcjami - szczegóły oraz trwałe usunięcie
                echo "<td>";
                echo "<a href='ticket_view.php?id=" . $wiersz['id'] . "'>Obsługa (Szczegóły)</a>";
                
                // Tylko admin i user mają prawo trwale usuwać
                if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user') {
                    echo " | <a href='ticket_delete.php?id=" . $wiersz['id'] . "' onclick='return confirm(\"Czy na pewno chcesz trwale usunąć to zgłoszenie? Tej operacji NIE MOŻNA cofnąć!\")' style='color: red; font-weight: bold;'>Usuń</a>";
                }
                
                echo "</td>";
                echo "</tr>";
            }
            
        } else {
            echo "<tr><td colspan='8'>Brak zgłoszeń w systemie.</td></tr>";
        }
        ?>
    </table>
    <?php if ($total_pages > 1): ?>
        <div style="margin-top: 20px;">
            <b>Strony: </b>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span style="padding: 5px 10px; background-color: #ddd; border: 1px solid #999; font-weight: bold;"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="tickets_list.php?page=<?php echo $i; ?>" style="padding: 5px 10px; border: 1px solid #ccc; text-decoration: none; color: black;"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</body>
</html>