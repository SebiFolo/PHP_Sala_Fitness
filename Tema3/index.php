<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Dashboard Fitness</title>
    <style>
        body { font-family: sans-serif; padding: 40px; text-align: center; background-color: #f8f9fa; }
        .card { background: white; max-width: 500px; margin: 0 auto; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .btn { display: block; width: 80%; margin: 10px auto; padding: 15px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-blue { background: #007bff; color: white; }
        .btn-green { background: #28a745; color: white; }
        .btn-red { background: #dc3545; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Salut, <?= htmlspecialchars($_SESSION['user_nume']) ?>! 👋</h1>
        <p>Bine ai venit în aplicația ta de fitness.</p>
        
        <br>
        <a href="profil.php" class="btn btn-blue">👤 Profilul Meu & IMC</a>
        <a href="abonamente.php" class="btn btn-blue" style="background-color: #ffc107; color: #333;">💳 Abonamente & Tarife</a>
        
        <a href="clase.php" class="btn btn-green">📅 Vezi Orarul Claselor</a>

        <br><hr><br>
        
        <a href="logout.php" class="btn btn-red">Ieșire din cont</a>
    </div>
</body>
</html>