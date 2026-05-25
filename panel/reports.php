<?php
session_start();

require_once '../config.php';

// tylko administrator ma dostęp do raportów
if ($_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może przeglądać statystyki i raporty.";
    exit;
}

// ogólne statystyki zgłoszeń

// ile zgłoszeń w bazie
$wynik_all = mysqli_query($conn, "SELECT COUNT(*) AS ile FROM tickets");
$row_all = mysqli_fetch_assoc($wynik_all);
$ogolem = $row_all['ile'];

// ile nowych zgłoszeń
$wynik_nowe = mysqli_query($conn, "SELECT COUNT(*) AS ile FROM tickets WHERE status = 'nowe'");
$row_nowe = mysqli_fetch_assoc($wynik_nowe);
$nowe = $row_nowe['ile'];

// ile zgłoszeń w trakcie realizacji
$wynik_w_trakcie = mysqli_query($conn, "SELECT COUNT(*) AS ile FROM tickets WHERE status = 'w trakcie'");
$row_w_trakcie = mysqli_fetch_assoc($wynik_w_trakcie);
$w_trakcie = $row_w_trakcie['ile'];

// ile zgłoszeń zakończonych
$wynik_zakonczone = mysqli_query($conn, "SELECT COUNT(*) AS ile FROM tickets WHERE status = 'zakończone'");
$row_zakonczone = mysqli_fetch_assoc($wynik_zakonczone);
$zakonczone = $row_zakonczone['ile'];


// statystyki użytkowników
// Ilu użytkowników zarejestrowanych w sys
$wynik_users = mysqli_query($conn, "SELECT COUNT(*) AS ile FROM users");
$row_users = mysqli_fetch_assoc($wynik_users);
$ilu_uzytkownikow = $row_users['ile'];


// zgłoszenia wg kategorii
$zapytanie_kategorie = "
    SELECT c.name AS nazwa_kategorii, COUNT(t.id) AS ilosc_zgloszen
    FROM categories c
    LEFT JOIN tickets t ON c.id = t.category_id
    GROUP BY c.id
    ORDER BY ilosc_zgloszen DESC
";
$wynik_kategorie = mysqli_query($conn, $zapytanie_kategorie);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raporty i Statystyki - Admin Panel</title>
</head>
<body>
    <h2>Raporty i statystyki systemu Help Desk</h2>
    
    <p><a href="dashboard.php">⬅ Powrót do panelu</a></p>
    
    <hr>

    <h3>Podsumowanie liczbowe</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 400px;">
        <tr>
            <td><b>Wszystkie zgłoszenia:</b></td>
            <td>Głównie: <b><?php echo $ogolem; ?></b></td>
        </tr>
        <tr>
            <td><span style="color: red; font-weight: bold;">Status: NOWE</span></td>
            <td><?php echo $nowe; ?></td>
        </tr>
        <tr>
            <td><span style="color: orange; font-weight: bold;">Status: W TRAKCIE</span></td>
            <td><?php echo $w_w_trakcie = $w_trakcie; ?></td>
        </tr>
        <tr>
            <td><span style="color: green; font-weight: bold;">Status: ZAKOŃCZONE</span></td>
            <td><?php echo $zakonczone; ?></td>
        </tr>
        <tr>
            <td><b>Zarejestrowani użytkownicy:</b></td>
            <td><?php echo $ilu_uzytkownikow; ?></td>
        </tr>
    </table>

    <br><br>

    <h3>Ilość zgłoszeń w podziale na kategorie</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 400px;">
        <tr>
            <th>Nazwa kategorii</th>
            <th>Liczba zgłoszeń</th>
        </tr>

        <?php 
        // Pętla wypisująca statystyki dla każdej kategorii
        if (mysqli_num_rows($wynik_kategorie) > 0) {
            while ($row_kat = mysqli_fetch_assoc($wynik_kategorie)) {
                echo "<tr>";
                echo "<td>" . $row_kat['nazwa_kategorii'] . "</td>";
                echo "<td><b>" . $row_kat['ilosc_zgloszen'] . "</b></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Brak kategorii w bazie danych.</td></tr>";
        }
        ?>
    </table>

</body>
</html>