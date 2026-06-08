<?php
session_start();
require_once '../config.php';

// dostęp tylko dla admina
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Brak dostępu! Tylko administrator może zarządzać kategoriami.";
    exit;
}

// obsługa usuwania kategorii ale najpierw sprawdzamy czy nie jest używana przez jakieś zgłoszenia
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = $_GET['delete'];
    
    // Sprawdzamy, czy kategoria nie jest używana przez jakieś zgłoszenia
    $zapytanie_sprawdz = "SELECT id FROM tickets WHERE category_id = ?";
    $stmt_sprawdz = mysqli_prepare($conn, $zapytanie_sprawdz);
    mysqli_stmt_bind_param($stmt_sprawdz, "i", $del_id);
    mysqli_stmt_execute($stmt_sprawdz);
    mysqli_stmt_store_result($stmt_sprawdz);
    
    if (mysqli_stmt_num_rows($stmt_sprawdz) > 0) {
        $_SESSION['error_message'] = "Nie można usunąć tej kategorii, ponieważ są do niej przypisane zgłoszenia!";
    } else {
        // Jeśli jest pusta, usuwamy
        $zapytanie_usun = "DELETE FROM categories WHERE id = ?";
        $stmt_usun = mysqli_prepare($conn, $zapytanie_usun);
        mysqli_stmt_bind_param($stmt_usun, "i", $del_id);
        if (mysqli_stmt_execute($stmt_usun)) {
            $_SESSION['success_message'] = "Kategoria została usunięta.";
        } else {
            $_SESSION['error_message'] = "Błąd podczas usuwania kategorii.";
        }
    }
    header("Location: categories.php");
    exit;
}

// obsługa dodawania nowej kategorii
if (isset($_POST['submit_add_category'])) {
    $name = trim($_POST['name']);
    
    if (empty($name)) {
        $_SESSION['error_message'] = "Błąd: Nazwa kategorii nie może być pusta!";
    } else {
        $zapytanie_dodaj = "INSERT INTO categories (name) VALUES (?)";
        $stmt_dodaj = mysqli_prepare($conn, $zapytanie_dodaj);
        mysqli_stmt_bind_param($stmt_dodaj, "s", $name);
        
        if (mysqli_stmt_execute($stmt_dodaj)) {
            $_SESSION['success_message'] = "Pomyślnie dodano nową kategorię: " . $name;
        } else {
            $_SESSION['error_message'] = "Wystąpił błąd bazy danych.";
        }
    }
    header("Location: categories.php");
    exit;
}

// Pobranie listy kategorii do tabeli 
$wynik_kategorie = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📁 Kategorie zgłoszeń</h2>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">⬅ Powrót do panelu</a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">➕ Dodaj nową kategorię</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nazwa kategorii:</label>
                        <input type="text" name="name" class="form-control" required maxlength="100" placeholder="np. Awaria sprzętu">
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="submit_add_category" class="btn btn-success fw-bold shadow-sm">
                            Zapisz kategorię
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nazwa kategorii</th>
                                <th class="text-end pe-4">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($wynik_kategorie) > 0) {
                                while($kat = mysqli_fetch_assoc($wynik_kategorie)) {
                                    echo "<tr>";
                                    echo "<td class='ps-4'>" . $kat['id'] . "</td>";
                                    echo "<td class='fw-bold'>" . htmlspecialchars($kat['name']) . "</td>";
                                    
                                    // Przycisk usuwania
                                    echo "<td class='text-end pe-4'>";
                                    echo "<a href='categories.php?delete=" . $kat['id'] . "' class='btn btn-outline-danger btn-sm' onclick='return confirm(\"Czy na pewno chcesz usunąć tę kategorię? Możesz to zrobić tylko, jeśli nie ma ona przypisanych zgłoszeń.\")'>🗑️ Usuń</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-muted py-4 text-center'>Brak kategorii w systemie.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>