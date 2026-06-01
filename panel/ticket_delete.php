<?php
session_start();
require_once '../config.php';

// Zabezpieczenie: tylko admin i pracownik
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    echo "Brak dostępu!";
    exit;
}

// Sprawdzenie, czy przesłano poprawne ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Pobranie nazwy załącznika
    $zapytanie_plik = "SELECT attachment FROM tickets WHERE id = ?";
    $stmt_plik = mysqli_prepare($conn, $zapytanie_plik);
    mysqli_stmt_bind_param($stmt_plik, "i", $ticket_id);
    mysqli_stmt_execute($stmt_plik);
    $wynik_plik = mysqli_stmt_get_result($stmt_plik);
    
    if ($wiersz = mysqli_fetch_assoc($wynik_plik)) {
        if (!empty($wiersz['attachment']) && file_exists("../" . $wiersz['attachment'])) {
            unlink("../" . $wiersz['attachment']); // Funkcja usuwająca plik
        }
    }
    mysqli_stmt_close($stmt_plik);

    //Usunięcie wszystkich komentarzy powiązanych z tym zgłoszeniem
    $zapytanie_komentarze = "DELETE FROM comments WHERE ticket_id = ?";
    $stmt_kom = mysqli_prepare($conn, $zapytanie_komentarze);
    mysqli_stmt_bind_param($stmt_kom, "i", $ticket_id);
    mysqli_stmt_execute($stmt_kom);
    mysqli_stmt_close($stmt_kom);

    //Usunięcie głównego zgłoszenia
    $zapytanie_usun = "DELETE FROM tickets WHERE id = ?";
    $stmt_usun = mysqli_prepare($conn, $zapytanie_usun);
    mysqli_stmt_bind_param($stmt_usun, "i", $ticket_id);

    if (mysqli_stmt_execute($stmt_usun)) {
        $_SESSION['success_message'] = "Zgłoszenie #" . $ticket_id . " oraz jego pliki zostały trwale usunięte z systemu.";
    } else {
        $_SESSION['error_message'] = "Błąd bazy danych: Nie udało się usunąć zgłoszenia.";
    }
    mysqli_stmt_close($stmt_usun);

} else {
    $_SESSION['error_message'] = "Nieprawidłowe ID zgłoszenia.";
}

header("Location: tickets_list.php");
exit;
?>