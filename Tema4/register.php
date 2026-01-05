<?php
session_start();
require 'db.php';
require_once 'mail/Mailer.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_POST['captcha']) || !isset($_SESSION['captcha']) || $_POST['captcha'] != $_SESSION['captcha']) {
        $mesaj = "❌ Codul de securitate din imagine este greșit!";
    } else {
        $nume = $_POST['nume'];
        $email = $_POST['email'];
        $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);

        $chk = $pdo->prepare("SELECT id FROM t_users WHERE email=?");
        $chk->execute([$email]);
        
        if ($chk->rowCount() > 0) {
            $mesaj = "Acest email este deja înregistrat!";
        } else {
            $sql = "INSERT INTO t_users (nume, email, parola_hash, rol_id) VALUES (?, ?, ?, 3)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$nume, $email, $parola])) {
                $mesaj = "✅ Cont creat! <a href='login.php'>Login</a>";
                
                // MAIL DE BUN VENIT
                try {
                    $mailer = new Mailer();
                    $mailer->sendMail($email, $nume, "Bun venit pe Fitness App!", "Salut $nume,\n\nContul tau a fost creat cu succes. Spor la treaba!");
                } catch (Exception $e) {}
            } else {
                $mesaj = "Eroare la baza de date.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Inregistrare</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; }
        input, button { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; cursor: pointer; margin-top: 15px; }
        .captcha-box { background: #f9f9f9; padding: 10px; text-align: center; border: 1px solid #eee; margin-top: 10px; }
        img { border: 1px solid #ccc; vertical-align: middle; }
    </style>
</head>
<body>
    <h2>Creează Cont</h2>
    <p style="color:red; font-weight:bold; text-align:center;"><?= $mesaj ?></p>
    
    <form method="POST">
        <label>Nume:</label>
        <input type="text" name="nume" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Parola:</label>
        <input type="password" name="parola" required>
        
        <div class="captcha-box">
            <p style="margin:0 0 5px 0; font-size:14px;">Introdu codul din imagine:</p>
            <img src="captcha.php?rand=<?= time(); ?>" alt="Captcha">
            <input type="text" name="captcha" required placeholder="Codul de sus" style="width: 150px; text-align: center; margin-left: 10px;">
        </div>
        
        <button type="submit">Înregistrează-te</button>
    </form>
</body>
</html>