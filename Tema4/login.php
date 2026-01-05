<?php
session_start();
require 'db.php';
require_once 'mail/Mailer.php'; 

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_POST['captcha']) || !isset($_SESSION['captcha']) || $_POST['captcha'] != $_SESSION['captcha']) {
        $mesaj = "❌ Codul de securitate din imagine este greșit!";
    } else {
        $email = $_POST['email'];
        $parola = $_POST['parola'];

        $stmt = $pdo->prepare("SELECT * FROM t_users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($parola, $user['parola_hash'])) {
            
            $cod_2fa = rand(100000, 999999);
            $_SESSION['2fa_code'] = $cod_2fa;
            $_SESSION['2fa_user_data'] = $user; 

            try {
                $mailer = new Mailer();
                $subiect = "Cod Autentificare Fitness";
                $corp = "Salut " . $user['nume'] . ",\n\nCodul tau este: " . $cod_2fa;
                
                $mailer->sendMail($email, $user['nume'], $subiect, $corp);
            } catch (Exception $e) {
            }

            header("Location: verificare_2fa.php");
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
    <title>Login</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; }
        input, button { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; cursor: pointer; margin-top: 15px; }
        .captcha-box { background: #f9f9f9; padding: 10px; text-align: center; border: 1px solid #eee; margin-top: 10px; }
        img { border: 1px solid #ccc; vertical-align: middle; }
    </style>
</head>
<body>
    <h2>Autentificare</h2>
    <p style="color:red; font-weight:bold; text-align:center;"><?= $mesaj ?></p>
    
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Parola:</label>
        <input type="password" name="parola" required>
        
        <div class="captcha-box">
            <p style="margin:0 0 5px 0; font-size:14px;">Introdu codul din imagine:</p>
            <img src="captcha.php?rand=<?= time(); ?>" alt="Captcha">
            <input type="text" name="captcha" required placeholder="Codul de sus" style="width: 150px; text-align: center; margin-left: 10px;">
        </div>

        <button type="submit">Logare Securizată 🔒</button>
    </form>
    
    <p style="text-align:center; margin-top:20px;">
        Nu ai cont? <a href="register.php">Înregistrează-te</a>
    </p>
</body>
</html>