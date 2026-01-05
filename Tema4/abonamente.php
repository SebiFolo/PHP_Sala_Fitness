<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mesaj = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_abonament'])) {
    $id_tip = $_POST['id_abonament'];
    $zile = $_POST['zile'];
    
    $data_start = date('Y-m-d'); 
    $data_stop = date('Y-m-d', strtotime("+$zile days")); 

    $stmt = $pdo->prepare("UPDATE t_abonamente_clienti SET activ = 0 WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $sql = "INSERT INTO t_abonamente_clienti (user_id, tip_abonament_id, data_start, data_stop, activ) VALUES (?, ?, ?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id, $id_tip, $data_start, $data_stop])) {
        $mesaj = "✅ Felicitări! Abonamentul tău a fost activat.";
    } else {
        $mesaj = "❌ Eroare la activare.";
    }
}

$sql_activ = "SELECT t_abonamente_clienti.*, t_tipuri_abonament.nume 
              FROM t_abonamente_clienti 
              JOIN t_tipuri_abonament ON t_abonamente_clienti.tip_abonament_id = t_tipuri_abonament.id
              WHERE user_id = ? AND activ = 1 AND data_stop >= CURDATE()";
$stmt = $pdo->prepare($sql_activ);
$stmt->execute([$user_id]);
$abonament_curent = $stmt->fetch();

$stmt = $pdo->query("SELECT * FROM t_tipuri_abonament");
$tipuri = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Abonamente</title>
    <style>
        body { font-family: sans-serif; background-color: #f8f9fa; padding: 20px; text-align: center; }
        .mesaj { color: green; font-weight: bold; margin-bottom: 20px; }
        
        /* Stil pentru Abonament Activ */
        .active-sub { background: #d4edda; color: #155724; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; max-width: 600px; margin: 0 auto 30px auto; }
        
        /* Container Carduri */
        .pricing-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        
        /* Card Individual */
        .card { background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; overflow: hidden; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card-header { background: #333; color: white; padding: 20px; }
        .price { font-size: 36px; font-weight: bold; }
        .card-body { padding: 20px; text-align: left; color: #666; height: 80px; }
        .btn-buy { display: block; width: 80%; margin: 20px auto; padding: 15px; background: #007bff; color: white; text-decoration: none; border: none; border-radius: 25px; cursor: pointer; font-size: 16px; transition: background 0.3s; }
        .btn-buy:hover { background: #0056b3; }
        
        /* Card VIP (Special) */
        .vip .card-header { background: #ffc107; color: #333; }
        .vip .btn-buy { background: #ffc107; color: #333; }
        .vip .btn-buy:hover { background: #e0a800; }
    </style>
</head>
<body>

    <a href="index.php" style="float: left; text-decoration: none; color: #333;">⬅ Înapoi la Dashboard</a>
    <h1>Alege Abonamentul Potrivit</h1>
    <p class="mesaj"><?= $mesaj ?></p>

    <?php if ($abonament_curent): ?>
        <div class="active-sub">
            <h3>🎉 Ai deja un abonament activ!</h3>
            <p>Tip: <strong><?= htmlspecialchars($abonament_curent['nume']) ?></strong></p>
            <p>Expiră la data de: <strong><?= $abonament_curent['data_stop'] ?></strong></p>
            <small>Nu poți cumpăra altul până când acesta nu expiră.</small>
        </div>
    <?php else: ?>
        
    <div class="pricing-container">
        <?php foreach ($tipuri as $tip): ?>
            <?php $cssClass = ($tip['pret'] > 300) ? 'vip' : '';  ?>
            
            <div class="card <?= $cssClass ?>">
                <div class="card-header">
                    <h3><?= htmlspecialchars($tip['nume']) ?></h3>
                    <div class="price"><?= $tip['pret'] ?> RON</div>
                    <small>Valabil <?= $tip['zile_valabilitate'] ?> zile</small>
                </div>
                <div class="card-body">
                    <p><?= htmlspecialchars($tip['descriere']) ?></p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="id_abonament" value="<?= $tip['id'] ?>">
                    <input type="hidden" name="zile" value="<?= $tip['zile_valabilitate'] ?>">
                    <button type="submit" class="btn-buy" onclick="return confirm('Confirmi achiziția?')">Cumpără Acum</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</body>
</html>