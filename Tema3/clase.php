<?php
session_start();
require 'db.php';

// Verificam daca e logat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// DEFINIM ROLUL: Daca e 1 e Admin, altfel e Client
$esteAdmin = (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 1);

$mesaj = "";

// --- PARTEA DE CREATE (Doar pentru ADMIN) ---
if ($esteAdmin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adauga_clasa'])) {
    $nume = $_POST['nume_clasa'];
    $data = $_POST['data_ora'];
    $locuri = $_POST['locuri'];
    $antrenor_id = $_SESSION['user_id']; 

    $sql = "INSERT INTO t_classes (nume_clasa, antrenor_id, data_ora, locuri_totale) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nume, $antrenor_id, $data, $locuri])) {
        $mesaj = "✅ Clasă adăugată cu succes!";
    }
}

// --- PARTEA DE DELETE (Doar pentru ADMIN) ---
if ($esteAdmin && isset($_GET['sterge_id'])) {
    $id_de_sters = $_GET['sterge_id'];
    $stmt = $pdo->prepare("DELETE FROM t_classes WHERE id = ?");
    $stmt->execute([$id_de_sters]);
    header("Location: clase.php");
    exit;
}

// Citire Clase
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
        .form-box { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; width: 300px; border-left: 5px solid #007bff; }
        input { width: 100%; margin-bottom: 10px; padding: 5px; }
    </style>
</head>
<body>
    <a href="index.php">⬅ Înapoi la Dashboard</a>
    <h1>Orar Clase Fitness</h1>

    <?php if ($esteAdmin): ?>
        <div class="form-box">
            <h3>👮 ADMIN: Adaugă o Clasă</h3>
            <p><?= $mesaj ?></p>
            <form method="POST">
                <label>Nume Clasă:</label>
                <input type="text" name="nume_clasa" required>
                <label>Data și Ora:</label>
                <input type="datetime-local" name="data_ora" required>
                <label>Număr Locuri:</label>
                <input type="number" name="locuri" value="20" required>
                <button type="submit" name="adauga_clasa">Salvează Clasa</button>
            </form>
        </div>
    <?php else: ?>
        <p><em>Esti logat ca si Client. Poti doar vizualiza orarul.</em></p>
    <?php endif; ?>

    <hr>

    <h3>Lista Claselor</h3>
    <table>
        <tr>
            <th>Nume Clasă</th>
            <th>Antrenor</th>
            <th>Data / Ora</th>
            <th>Locuri</th>
            <?php if ($esteAdmin): ?> <th>Acțiuni</th> <?php endif; ?>
        </tr>
        <?php foreach ($clase as $clasa): ?>
        <tr>
            <td><?= htmlspecialchars($clasa['nume_clasa']) ?></td>
            <td><?= htmlspecialchars($clasa['nume_antrenor']) ?></td>
            <td><?= $clasa['data_ora'] ?></td>
            <td><?= $clasa['locuri_totale'] ?></td>
            
            <?php if ($esteAdmin): ?>
            <td>
                <a href="clase.php?sterge_id=<?= $clasa['id'] ?>" class="btn-delete" onclick="return confirm('Sigur ștergi?')">Șterge 🗑️</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>