<?php
// Sprawdzamy, czy istnieje komunikat o sukcesie
if (isset($_SESSION['success_message'])) {
    echo "<div style='color: green; background-color: #e6f4ea; padding: 10px; border: 1px solid green; margin-bottom: 15px; width: fit-content;'>";
    echo "<b>✔ " . $_SESSION['success_message'] . "</b>";
    echo "</div>";
    
    // Usuwamy komunikat, żeby wyświetlił się tylko raz
    unset($_SESSION['success_message']);
}

// Sprawdzamy, czy istnieje komunikat o błędzie
if (isset($_SESSION['error_message'])) {
    echo "<div style='color: red; background-color: #fce4e4; padding: 10px; border: 1px solid red; margin-bottom: 15px; width: fit-content;'>";
    echo "<b>✖ " . $_SESSION['error_message'] . "</b>";
    echo "</div>";
    
    // Usuwamy komunikat
    unset($_SESSION['error_message']);
}
?>