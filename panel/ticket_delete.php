<?php
session_start();
require_once '../config.php';

// tylko admin i user ma dostęp
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'guest') {
    echo "Brak dostępu! Tylko obsługa może trwale usuwać zgłoszenia.";
    exit;
}

// czy mamy ID zgłoszenia do usunięcia
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // usuwamy komentarze
    $zapytanie_kom = "DELETE FROM comments WHERE ticket_id = ?";
    $stmt_kom = mysqli_prepare($conn, $zapytanie_kom);
    mysqli_stmt_bind_param($stmt_kom, "i", $ticket_id);
    mysqli_stmt_execute($stmt_kom);
    mysqli_stmt_close($stmt_kom);

    // usuwamy zgłoszenie z ticketów
    $zapytanie_ticket = "DELETE FROM tickets WHERE id = ?";
    $stmt_ticket = mysqli_prepare($conn, $zapytanie_ticket);
    mysqli_stmt_bind_param($stmt_ticket, "i", $ticket_id);
    
    if (mysqli_stmt_execute($stmt_ticket)) {
        $_SESSION['success_message'] = "Zgłoszenie nr " . $ticket_id . " zostało pomyślnie i trwale usunięte.";
    } else {
        $_SESSION['error_message'] = "Wystąpił błąd podczas usuwania zgłoszenia.";
    }
    mysqli_stmt_close($stmt_ticket);
}

// wracamy na listę zgłoszeń
header("Location: tickets_list.php");
exit;
?>