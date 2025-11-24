<?php
session_start();
require 'db.php';

// Verificam daca e logat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mesaj = "";

// --- PARTEA DE CREATE (Adaugare Clasa) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adauga_clasa'])) {
    $nume = $_POST['nume_clasa'];
    $data = $_POST['data_ora'];
    $locuri = $_POST['locuri'];
    
    // Antrenorul e cel care e logat (pentru simplitate acum, sau poti pune un ID fix)
    // Daca userul logat nu e antrenor, punem NULL sau ID-ul lui oricum
    $antrenor_id = $_SESSION['user_id']; 

    $sql = "INSERT INTO t_classes (nume_clasa, antrenor_id, data_ora, locuri_totale) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nume, $antrenor_id, $data, $locuri])) {
        $mesaj = "✅ Clasă adăugată cu succes!";
    } else {
        $mesaj = "❌ Eroare la adăugare.";
    }
}

// --- PARTEA DE DELETE (Stergere Clasa) ---
if (isset($_GET['sterge_id'])) {
    $id_de_sters = $_GET['sterge_id'];
    $stmt = $pdo->prepare("DELETE FROM t_classes WHERE id = ?");
    $stmt->execute([$id_de_sters]);
    header("Location: clase.php"); // Refresh pagina
    exit;
}

// --- PARTEA DE READ (Citire Clase) ---
// Luam toate clasele din baza de date + numele antrenorului
$sql = "SELECT t_classes.*, t_users.nume as nume_antrenor 
        FROM t_classes 
        LEFT JOIN t_users ON t_classes.antrenor_id = t_users.id
        ORDER BY data_ora ASC";
$stmt = $pdo->query($sql);
$clase = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Management Clase</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-delete { color: red; text-decoration: none; font-weight: bold; }
        .form-box { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; width: 300px; }
        input { width: 100%; margin-bottom: 10px; padding: 5px; }
    </style>
</head>
<body>
    <a href="index.php">⬅ Înapoi la Dashboard</a>
    <h1>Gestionare Clase (CRUD)</h1>

    <div class="form-box">
        <h3>Adaugă o Clasă Nouă</h3>
        <p><?= $mesaj ?></p>
        <form method="POST">
            <label>Nume Clasă (ex: Yoga):</label>
            <input type="text" name="nume_clasa" required>
            
            <label>Data și Ora:</label>
            <input type="datetime-local" name="data_ora" required>
            
            <label>Număr Locuri:</label>
            <input type="number" name="locuri" value="20" required>
            
            <button type="submit" name="adauga_clasa">Salvează Clasa</button>
        </form>
    </div>

    <hr>

    <h3>Lista Claselor Existente</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nume Clasă</th>
            <th>Antrenor</th>
            <th>Data / Ora</th>
            <th>Locuri</th>
            <th>Acțiuni</th>
        </tr>
        <?php foreach ($clase as $clasa): ?>
        <tr>
            <td><?= $clasa['id'] ?></td>
            <td><?= htmlspecialchars($clasa['nume_clasa']) ?></td>
            <td><?= htmlspecialchars($clasa['nume_antrenor'] ?? 'Fără antrenor') ?></td>
            <td><?= $clasa['data_ora'] ?></td>
            <td><?= $clasa['locuri_totale'] ?></td>
            <td>
                <a href="clase.php?sterge_id=<?= $clasa['id'] ?>" class="btn-delete" onclick="return confirm('Sigur ștergi?')">Șterge 🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>