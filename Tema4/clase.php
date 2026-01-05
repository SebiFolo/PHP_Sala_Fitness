<?php
session_start();
require 'db.php';

date_default_timezone_set('Europe/Bucharest');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mesaj = "";
$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['user_rol']; // 1=Admin, 2=Antrenor, 3=Client
$acum_ro = date("Y-m-d H:i:s");

$stmt_cleanup = $pdo->prepare("DELETE FROM t_classes WHERE data_ora < ?");
$stmt_cleanup->execute([$acum_ro]);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_rol == 1) {
    
    if (isset($_POST['sterge_id'])) {
        $stmt = $pdo->prepare("DELETE FROM t_classes WHERE id = ?");
        $stmt->execute([$_POST['sterge_id']]);
        $mesaj = "✅ Clasa a fost ștearsă.";
    } 
    else {
        $nume_clasa = $_POST['nume_clasa'];
        $antrenor_id = $_POST['antrenor_id'];
        $data_ora = $_POST['data_ora'];
        $locuri = $_POST['locuri'];

        if ($data_ora < $acum_ro) {
            $mesaj = "❌ Eroare: Nu poți programa o clasă în trecut!";
        } else {
            $sql = "INSERT INTO t_classes (nume_clasa, antrenor_id, data_ora, locuri_totale) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nume_clasa, $antrenor_id, $data_ora, $locuri])) {
                $mesaj = "✅ Clasa a fost adăugată!";
                header("Refresh:0"); 
            } else {
                $mesaj = "❌ Eroare la salvare.";
            }
        }
    }
}


$stmt_antr = $pdo->query("SELECT id, nume FROM t_users WHERE rol_id = 2");
$antrenori = $stmt_antr->fetchAll();

$sql_afisare = "SELECT t_classes.*, t_users.nume as nume_antrenor 
                FROM t_classes 
                LEFT JOIN t_users ON t_classes.antrenor_id = t_users.id 
                ORDER BY data_ora ASC";
$stmt_clase = $pdo->query($sql_afisare);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Orar Clase Fitness</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: #f4f6f9; text-align: center; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 12px; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }

        .form-box { background: #e9ecef; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #ced4da; }
        input, select, button { padding: 10px; margin: 5px; border-radius: 5px; border: 1px solid #ccc; }
        button { cursor: pointer; font-weight: bold; }
        .btn-add { background: #28a745; color: white; border: none; }
        .btn-del { background: #dc3545; color: white; border: none; padding: 5px 10px; }
        
        .chart-box { margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; }
        .chart-box img { max-width: 100%; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        .back-link { text-decoration: none; color: #555; display: inline-block; margin-bottom: 15px; font-weight: bold; }
        .info-time { font-size: 13px; color: #777; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">⬅ Înapoi la Dashboard</a>
        <h1>📅 Orar Clase & Activitate</h1>
        
        <p class="info-time">
            🕒 Ora Serverului: <?= date("d.m.Y H:i") ?><br>
           
        </p>

        <?php if ($user_rol == 1): ?>
            <div class="form-box">
                <h3>➕ Programează o Clasă</h3>
                <p style="color: <?= strpos($mesaj, '✅') !== false ? 'green' : 'red' ?>; font-weight:bold;"><?= $mesaj ?></p>
                
                <form method="POST">
                    <input type="text" name="nume_clasa" placeholder="Nume Clasă (ex: Pilates)" required>
                    
                    <select name="antrenor_id" required>
                        <option value="">-- Alege Antrenor --</option>
                        <?php foreach ($antrenori as $antr): ?>
                            <option value="<?= $antr['id'] ?>">👤 <?= htmlspecialchars($antr['nume']) ?></option>
                        <?php endforeach; ?>
                        <?php if(empty($antrenori)): ?>
                            <option value="" disabled>Nu există antrenori!</option>
                        <?php endif; ?>
                    </select>

                    <input type="datetime-local" name="data_ora" required>
                    <input type="number" name="locuri" placeholder="Locuri" style="width: 80px;" required>
                    
                    <button type="submit" class="btn-add">Salvează</button>
                </form>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Clasă</th>
                <th>Antrenor</th>
                <th>Data și Ora</th>
                <th>Locuri</th>
                <?php if ($user_rol == 1) echo "<th>Acțiuni</th>"; ?>
            </tr>

            <?php while ($row = $stmt_clase->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><b><?= htmlspecialchars($row['nume_clasa']) ?></b></td>
                    <td><?= $row['nume_antrenor'] ? htmlspecialchars($row['nume_antrenor']) : "<span style='color:red'>Fără Antrenor</span>" ?></td>
                    <td><?= date("d.m.Y H:i", strtotime($row['data_ora'])) ?></td>
                    <td><?= $row['locuri_totale'] ?></td>
                    
                    <?php if ($user_rol == 1): ?>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="sterge_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-del" onclick="return confirm('Sigur ștergi această clasă?')">Șterge</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </table>

        <?php if ($stmt_clase->rowCount() == 0): ?>
            <p>Nu există clase programate momentan.</p>
        <?php endif; ?>

        <div class="chart-box">
            <h3>📊 Grafic Activitate Antrenori </h3>
            
            <img src="grafic_php.php?rand=<?= time() ?>" alt="Grafic Antrenori">
        </div>

    </div>
</body>
</html>