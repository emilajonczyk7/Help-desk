<?php
require_once 'config.php';

$message = "";
$ticket = null; // Zmienna na dane zgłoszenia
$wynik_komentarze = null; // Zmienna na komentarze

// Kiedy Klient wpisze numer i wciśnie przycisk "Sprawdź status"
if (isset($_POST['submit_check'])) {
    
    $ticket_id = $_POST['ticket_id']; // Pobranie wpisanego numeru ID

    // Zapytanie wyszukujące zgłoszenie o podanym ID
    $zapytanie = "
        SELECT t.id, t.title, t.description, t.status, t.created_at, c.name AS nazwa_kategorii 
        FROM tickets t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.id = ?
    ";
    
    $stmt = mysqli_prepare($conn, $zapytanie);
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // czy takie zgłoszenie istnieje w bazie
    if (mysqli_num_rows($result) == 1) {
        
        // wyciągamy dane do zmiennej $ticket
        $ticket = mysqli_fetch_assoc($result);

        // pobieramy wszystkie komentarze do tego zgłoszenia
        $zapytanie_kom = "
            SELECT cm.content, cm.created_at, u.username 
            FROM comments cm
            LEFT JOIN users u ON cm.user_id = u.id
            WHERE cm.ticket_id = ?
            ORDER BY cm.id ASC
        ";
        $stmt_kom = mysqli_prepare($conn, $zapytanie_kom);
        mysqli_stmt_bind_param($stmt_kom, "i", $ticket_id);
        mysqli_stmt_execute($stmt_kom);
        
        $wynik_komentarze = mysqli_stmt_get_result($stmt_kom);
        
    } else {
        $message = "Nie znaleziono zgłoszenia o podanym numerze ID.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sprawdź status zgłoszenia</title>
</head>
<body>
    <h2>Śledzenie zgłoszenia</h2>
    
    <p>
        <?php 
        // Mały dodatek ułatwiający nawigację:
        // Jeśli ktoś jest zalogowany, dajemy link do panelu. Jeśli to gość, link do strony głównej/logowania.
        if (isset($_SESSION['user_id'])) {
            echo '<a href="panel/dashboard.php">⬅ Powrót do panelu</a>';
        } else {
            echo '<a href="login.php">⬅ Zaloguj się (dla pracowników)</a> | <a href="new_ticket.php">➕ Zgłoś nową awarię</a>';
        }
        ?>
    </p>

    <form method="POST">
        Wpisz numer (ID) zgłoszenia: <br>
        <input type="number" name="ticket_id" required>
        <input type="submit" name="submit_check" value="Sprawdź status">
    </form>

    <br>
    
    <p style="color: red;"><b><?php echo $message; ?></b></p>

    <?php if ($ticket != null) { ?>
        
        <hr>
        <h3>Szczegóły Twojego zgłoszenia:</h3>
        
        <table border="1" cellpadding="8" cellspacing="0" style="width: 600px;">
            <tr>
                <td><b>Numer (ID):</b></td>
                <td><?php echo $ticket['id']; ?></td>
            </tr>
            <tr>
                <td><b>Tytuł:</b></td>
                <td><?php echo $ticket['title']; ?></td>
            </tr>
            <tr>
                <td><b>Kategoria:</b></td>
                <td>
                    <?php 
                    if ($ticket['nazwa_kategorii'] != "") {
                        echo $ticket['nazwa_kategorii'];
                    } else {
                        echo "Brak kategorii";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><b>Status problemu:</b></td>
                <td>
                    <?php 
                    // Kolorowanie statusów
                    if ($ticket['status'] == 'nowe') {
                        echo "<span style='color: red; font-weight: bold;'>NOWE</span>";
                    } else if ($ticket['status'] == 'w trakcie') {
                        echo "<span style='color: orange; font-weight: bold;'>W TRAKCIE</span>";
                    } else if ($ticket['status'] == 'zakończone') {
                        echo "<span style='color: green; font-weight: bold;'>ZAKOŃCZONE</span>";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><b>Data zgłoszenia:</b></td>
                <td><?php echo $ticket['created_at']; ?></td>
            </tr>
            <tr>
                <td><b>Twój opis:</b></td>
                <td><?php echo $ticket['description']; ?></td>
            </tr>
        </table>

        <br>
        
        <h3>Odpowiedzi od Help Desku:</h3>
        <div style="background-color: #f9f9f9; padding: 15px; width: 570px; border: 1px solid #ccc;">
            <?php 
            // Sprawdzamy, czy są w ogóle jakieś komentarze
            if (mysqli_num_rows($wynik_komentarze) > 0) {
                // Wypisujemy komentarze jeden pod drugim w pętli
                while ($komentarz = mysqli_fetch_assoc($wynik_komentarze)) {
                    
                    // Jeśli komentarz dodał niezalogowany użytkownik, wpisujemy "Klient"
                    $autor = $komentarz['username'];
                    if ($autor == "") {
                        $autor = "Ty (Klient)";
                    }

                    echo "<p><b>" . $autor . "</b> [" . $komentarz['created_at'] . "]:<br>";
                    echo $komentarz['content'] . "</p><hr>";
                }
            } else {
                echo "<p>Jeszcze nikt nie odpowiedział na to zgłoszenie. Prosimy o cierpliwość!</p>";
            }
            ?>
        </div>

    <?php } ?>

</body>
</html>