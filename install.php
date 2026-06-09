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
                        // 1. Konfiguracja domyślna dla XAMPP
                        $host = "localhost";
                        $user = "root";
                        $password = ""; // Domyślnie puste w XAMPP
                        $dbname = "helpdesk";
                        
                        // Ścieżka do pliku SQL 
                        $sql_file = "database/helpdesk.sql"; 

                        echo "<ul class='list-group mb-4'>";

                        // 2. Połączenie z serwerem MySQL 
                        $conn = new mysqli($host, $user, $password);
                        if ($conn->connect_error) {
                            die("<li class='list-group-item list-group-item-danger'>❌ Błąd połączenia z MySQL: " . $conn->connect_error . "</li></ul>");
                        }
                        echo "<li class='list-group-item list-group-item-success'>✅ Połączono z serwerem MySQL.</li>";

                        // 3. Utworzenie bazy danych
                        $sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                        if ($conn->query($sql_create_db) === TRUE) {
                            echo "<li class='list-group-item list-group-item-success'>✅ Utworzono bazę danych <b>$dbname</b>.</li>";
                        } else {
                            die("<li class='list-group-item list-group-item-danger'>❌ Błąd tworzenia bazy: " . $conn->error . "</li></ul>");
                        }

                        // 4. Wybranie bazy
                        $conn->select_db($dbname);

                        // 5. Wczytanie i wykonanie pliku SQL )
                        if (file_exists($sql_file)) {
                            $query = file_get_contents($sql_file);
                            
                            if ($conn->multi_query($query)) {
                                do {
                                    if ($res = $conn->store_result()) { $res->free(); }
                                } while ($conn->more_results() && $conn->next_result());
                                
                                echo "<li class='list-group-item list-group-item-success'>✅ Zaimportowano tabele i konta testowe z pliku SQL.</li>";
                            } else {
                                echo "<li class='list-group-item list-group-item-danger'>❌ Błąd importu SQL: " . $conn->error . "</li>";
                            }
                        } else {
                            echo "<li class='list-group-item list-group-item-danger'>❌ Nie znaleziono pliku struktury: <b>$sql_file</b>. Upewnij się, że znajduje się w folderze 'database/'.</li>";
                        }

                        $conn->close();
                        echo "</ul>";
                        ?>

                        <div class="alert alert-info text-center fw-bold">
                            Instalacja została zakończona! Możesz teraz korzystać z systemu.
                        </div>

                        <div class="d-grid mt-4">
                            <a href="login.php" class="btn btn-primary btn-lg fw-bold">Przejdź do strony logowania ➡</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
