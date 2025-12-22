<?php
session_start(); 
require 'db.php';
$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $cod_user = isset($_POST['captcha_code']) ? strtoupper($_POST['captcha_code']) : '';
    $cod_real = isset($_SESSION['cod_captcha']) ? $_SESSION['cod_captcha'] : '';

    if ($cod_user != $cod_real) {
        $mesaj = "❌ Codul din imagine este greșit!";
    } else {
        $email = $_POST['email'];
        $parola = $_POST['parola'];

        $stmt = $pdo->prepare("SELECT * FROM t_users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($parola, $user['parola_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nume'] = $user['nume'];
            $_SESSION['user_rol'] = $user['rol_id'];
            
            unset($_SESSION['cod_captcha']);
            
            header("Location: index.php");
            exit;
        } else {
            $mesaj = "Email sau parolă incorectă!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; }
        input { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        .captcha-box { background: #f9f9f9; padding: 10px; text-align: center; border: 1px dashed #ccc; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Autentificare</h2>
    <p style="color:red"><?= $mesaj ?></p>
    
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Parola:</label>
        <input type="password" name="parola" required>

        <div class="captcha-box">
            <p style="margin:0 0 5px 0; font-size: 14px;">Introdu codul de securitate:</p>
            <img src="captcha.php" alt="Cod Captcha" style="border: 1px solid #999;">
            <br><br>
            <input type="text" name="captcha_code" placeholder="Codul din imagine" required autocomplete="off" style="width: 60%; text-align: center;">
        </div>
        <button type="submit">Intră în cont</button>
    </form>
    <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
</body>
</html>