<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$quote_text = "Fii cea mai bună versiune a ta!";
$quote_author = "Fitness App";
$json_data = @file_get_contents("https://zenquotes.io/api/today"); 
if ($json_data) {
    $data = json_decode($json_data, true);
    if (isset($data[0]['q'])) {
        $quote_text = $data[0]['q'];
        $quote_author = $data[0]['a'];
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Dashboard Fitness</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; text-align: center; background-color: #f4f6f9; }
        .card { background: white; max-width: 800px; margin: 0 auto; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        
        .btn-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; margin-top: 30px; }
        .btn { display: inline-block; padding: 15px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; color: white; transition: transform 0.2s; min-width: 200px; }
        .btn:hover { transform: translateY(-3px); opacity: 0.9; }
        
        .btn-blue { background: linear-gradient(135deg, #007bff, #0056b3); }
        .btn-green { background: linear-gradient(135deg, #28a745, #1e7e34); }
        .btn-yellow { background: linear-gradient(135deg, #ffc107, #d39e00); color: #333; }
        .btn-info { background: linear-gradient(135deg, #17a2b8, #117a8b); } 
        .btn-gray { background: #6c757d; } 
        .btn-red { background: #dc3545; margin-top: 30px; width: 100%; max-width: 300px; }

        .quote-box { background: #e3f2fd; border-left: 5px solid #2196f3; padding: 15px; margin: 20px auto; max-width: 600px; border-radius: 5px; font-style: italic; color: #555; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Salut, <?= htmlspecialchars($_SESSION['user_nume']) ?>! 👋</h1>
        <p>Bine ai venit în aplicația de fitness a salii.</p>

        <div class="quote-box">
            "<?= $quote_text ?>" <br>
            <small style="font-weight:bold; color:#333;">— <?= $quote_author ?></small>
        </div>

        <div class="btn-container">
            <a href="profil.php" class="btn btn-blue">👤 Profil & IMC</a>
            <a href="abonamente.php" class="btn btn-yellow">💳 Abonamente</a>
            <a href="clase.php" class="btn btn-green">📅 Orar Clase</a>
            <a href="export_clase.php" class="btn btn-info">⬇️ Export Excel</a>
            <a href="contact.php" class="btn btn-gray">📩 Contact</a>
        </div>

        <a href="logout.php" class="btn btn-red">Ieșire din cont</a>
    </div>
</body>
</html>