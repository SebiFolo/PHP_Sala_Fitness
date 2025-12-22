<?php
session_start(); 
require 'db.php';
$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $cod_user = isset($_POST['captcha_code']) ? strtoupper($_POST['captcha_code']) : '';
    $cod_real = isset($_SESSION['cod_captcha']) ? $_SESSION['cod_captcha'] : '';

    if ($cod_user != $cod_real) {
        $mesaj = "❌ Codul Captcha este greșit! Încearcă din nou.";
    } else {
       
        $nume = $_POST['nume'];
        $email = $_POST['email'];
        $parola = $_POST['parola'];

        $stmt = $pdo->prepare("SELECT id FROM t_users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $mesaj = "Acest email este deja folosit!";
        } else {
            $parola_hash = password_hash($parola, PASSWORD_DEFAULT);
           
            $sql = "INSERT INTO t_users (nume, email, parola_hash, rol_id) VALUES (?, ?, ?, 3)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nume, $email, $parola_hash])) {
                $mesaj = "✅ Cont creat! <a href='login.php'>Autentifică-te</a>";
                
                unset($_SESSION['cod_captcha']); 
            } else {
                $mesaj = "❌ Eroare la salvare.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Inregistrare</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; }
        input { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .captcha-box { background: #f9f9f9; padding: 10px; text-align: center; border: 1px dashed #ccc; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Creează Cont Nou</h2>
    <p style="color:red"><?= $mesaj ?></p>
    
    <form method="POST">
        <label>Nume:</label>
        <input type="text" name="nume" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Parola:</label>
        <input type="password" name="parola" required>
        
        <div class="captcha-box">
            <p style="margin:0 0 5px 0; font-size: 14px;">Introdu codul din imagine:</p>
            <img src="captcha.php" alt="Cod de securitate" style="vertical-align: middle; border: 1px solid #999;">
            <br><br>
            <input type="text" name="captcha_code" placeholder="Scrie codul aici..." required autocomplete="off" style="width: 60%; text-align: center;">
        </div>
        <button type="submit">Înregistrează-te</button>
    </form>
</body>
</html>