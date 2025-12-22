<?php
session_start();
require 'db.php';

// Verificam daca e logat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mesaj = "";
$user_id = $_SESSION['user_id'];

// --- 1. ACTUALIZARE DATE (Daca a apasat butonul Salveaza) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tel = $_POST['telefon'];
    $kg = $_POST['greutate'];
    $cm = $_POST['inaltime'];

    // Update in baza de date
    $sql = "UPDATE t_users SET telefon = ?, greutate = ?, inaltime = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$tel, $kg, $cm, $user_id])) {
        $mesaj = "✅ Datele au fost actualizate!";
    } else {
        $mesaj = "❌ Eroare la salvare.";
    }
}

// --- 2. CITIRE DATE CURENTE (Ca sa le afisam in formular) ---
$stmt = $pdo->prepare("SELECT * FROM t_users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// --- 3. CALCUL IMC (Indice Masa Corporala) ---
$imc_msg = "Completează greutatea și înălțimea pentru a afla IMC-ul.";
$imc_color = "gray";

if ($user['greutate'] > 0 && $user['inaltime'] > 0) {
    // Formula: Greutate / (Inaltime in metri ^ 2)
    $h_metri = $user['inaltime'] / 100;
    $imc = $user['greutate'] / ($h_metri * $h_metri);
    $imc_format = number_format($imc, 1);

    if ($imc < 18.5) { 
        $imc_msg = "IMC: $imc_format (Subponderal)"; $imc_color = "#f0ad4e"; // Portocaliu
    } elseif ($imc >= 18.5 && $imc < 25) { 
        $imc_msg = "IMC: $imc_format (Greutate Normală)"; $imc_color = "#28a745"; // Verde
    } elseif ($imc >= 25 && $imc < 30) { 
        $imc_msg = "IMC: $imc_format (Supraponderal)"; $imc_color = "#ffc107"; // Galben
    } else { 
        $imc_msg = "IMC: $imc_format (Obezitate)"; $imc_color = "#dc3545"; // Rosu
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Profilul Meu</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 5px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        label { font-weight: bold; }
        button { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .back-link { display: block; margin-bottom: 20px; color: #666; text-decoration: none; }
        
        /* Cardul de IMC */
        .imc-box { background-color: <?= $imc_color ?>; color: white; padding: 15px; text-align: center; border-radius: 5px; margin-bottom: 20px; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>

    <div class="container">
        <a href="index.php" class="back-link">⬅ Înapoi la Dashboard</a>
        
        <h1>Profil: <?= htmlspecialchars($user['nume']) ?></h1>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        <p style="color: green; font-weight: bold;"><?= $mesaj ?></p>
        <hr>

        <div class="imc-box">
            <?= $imc_msg ?>
        </div>

        <form method="POST">
            <label>Număr de Telefon:</label>
            <input type="text" name="telefon" value="<?= htmlspecialchars($user['telefon'] ?? '') ?>" placeholder="ex: 0722...">

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Greutate (kg):</label>
                    <input type="number" step="0.1" name="greutate" value="<?= $user['greutate'] ?>" placeholder="ex: 75.5">
                </div>
                <div style="flex: 1;">
                    <label>Înălțime (cm):</label>
                    <input type="number" name="inaltime" value="<?= $user['inaltime'] ?>" placeholder="ex: 180">
                </div>
            </div>

            <button type="submit">💾 Salvează Datele</button>
        </form>
    </div>

</body>
</html>