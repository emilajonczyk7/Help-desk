<?php
session_start();

require_once '../config.php';

// tylko admin i pracownik mają tu dostęp
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu! Tylko obsługa Help Desku może przeglądać listę zgłoszeń.";
    exit;
}

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
    
    <p><a href="dashboard.php">Powrót do panelu</a></p>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Tytuł problemu</th>
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
                
                // Link do szczegółów zgłoszenia do obsługi przez pracownika
                echo "<td><a href='ticket_view.php?id=" . $wiersz['id'] . "'>Obsługa (Szczegóły)</a></td>";
                echo "</tr>";
            }
            
        } else {
            echo "<tr><td colspan='8'>Brak zgłoszeń w systemie.</td></tr>";
        }
        ?>
    </table>
</body>
</html>