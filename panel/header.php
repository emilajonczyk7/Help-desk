<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">🛠️ Help Desk</a>
            <div class="d-flex text-white align-items-center">
                <?php if(isset($_SESSION['username'])): ?>
                    <span class="me-3 d-none d-md-block">Witaj, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>! (<?php echo $_SESSION['role']; ?>)</span>
                    
                    <a href="user_profile.php" class="btn btn-outline-light btn-sm fw-bold me-2">👤 Mój profil</a>
                    
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm fw-bold">Wyloguj się</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <?php include 'flash_messages.php'; ?>