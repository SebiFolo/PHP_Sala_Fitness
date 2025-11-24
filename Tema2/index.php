<?php
session_start();

// PROTECTIE: Daca nu e logat, il aruncam inapoi la login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - FitnessManager</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; }
        .header { background: #f4f4f4; padding: 20px; border-bottom: 2px solid #ddd; margin-bottom: 20px;}
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            margin: 10px;
        }
        .btn:hover { background-color: #0056b3; }
        .btn-logout { background-color: #dc3545; }
        .btn-logout:hover { background-color: #a71d2a; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Salut, <?= htmlspecialchars($_SESSION['user_nume']) ?>! 👋</h1>
        <p>Bine ai venit în aplicația FitnessManager.</p>
    </div>

    <h3>Ce vrei să faci azi?</h3>
    
    <a href="clase.php" class="btn">📅 Gestionează Clase (CRUD)</a>
    
    <br><br>
    <a href="logout.php" class="btn btn-logout">Deconectare</a>
</body>
</html>