<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalator Systemu Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0 fw-bold">🛠️ Instalator Systemu Help Desk</h4>
                    </div>
                    <div class="card-body p-4">
                        
                       <?php
                        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                        // KONFIGURACJA - UPEWNIJ SIĘ, ŻE $db_name to nazwa Twojej bazy z phpMyAdmin
                        $db_host = "localhost";
                        $db_user = "2027_jonczyk";
                        $db_password = "420537";
                        $db_name = "2027_jonczyk"; 
                        $sql_file = "database/helpdesk.sql";

                        echo "<ul class='list-group mb-4'>";

                        try {
                            // 1. Łączenie z serwerem bez wybranej bazy
                            $conn = new mysqli($db_host, $db_user, $db_password);
                            $conn->set_charset("utf8mb4");

                            // 2. Próba stworzenia bazy danych
                            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            $conn->select_db($db_name);
                            echo "<li class='list-group-item list-group-item-success'>✅ Połączono z bazą danych <b>$db_name</b>.</li>";

                            // 3. Sprawdzanie czy tabele istnieją
                            $check_tables = $conn->query("SHOW TABLES");
                            
                            if ($check_tables->num_rows > 0) {
                                echo "<li class='list-group-item list-group-item-info'>ℹ️ Baza danych zawiera już tabele. Instalacja pominięta.</li>";
                            } else {
                                // 4. Importowanie pliku SQL (linia po linii)
                                if (file_exists($sql_file)) {
                                    $sql_content = file_get_contents($sql_file);
                                    $queries = explode(";", $sql_content);
                                    
                                    foreach ($queries as $query) {
                                        if (trim($query) != "") {
                                            $conn->query($query);
                                        }
                                    }
                                    echo "<li class='list-group-item list-group-item-success'>🚀 Baza postawiona automatycznie! Wgrano tabele z pliku SQL.</li>";
                                } else {
                                    echo "<li class='list-group-item list-group-item-danger'>❌ Nie znaleziono pliku struktury: $sql_file</li>";
                                }
                            }

                        } catch (Exception $e) {
                            echo "<li class='list-group-item list-group-item-danger'>❌ Błąd: " . htmlspecialchars($e->getMessage()) . "</li>";
                        }

                        if (isset($conn)) { $conn->close(); }
                        echo "</ul>";
                        ?>

                        <div class="alert alert-info text-center fw-bold shadow-sm">
                            Jeśli powyżej widzisz same zielone/niebieskie komunikaty, system działa!
                        </div>

                        <div class="d-grid mt-4">
                            <a href="login.php" class="btn btn-primary btn-lg fw-bold shadow-sm">Przejdź do strony logowania ➡</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>