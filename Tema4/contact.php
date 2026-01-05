<?php
session_start();
require_once 'mail/Mailer.php';

$mesaj_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume = $_POST['nume'];
    $email_user = $_POST['email'];
    $mesaj_text = $_POST['mesaj'];
    
    $admin_email = "fologeasebastian2005@gmail.com"; 

    try {
        $mailer = new Mailer();
        $corp = "Mesaj nou de la $nume ($email_user):\n\n$mesaj_text";
        
        $mailer->sendMail($admin_email, "Admin", "Contact Site Fitness", $corp);
        $mesaj_status = "✅ Mesaj trimis!";
    } catch (Exception $e) {
        $mesaj_status = "❌ Eroare la trimitere.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact</title>
    <style>body{font-family:sans-serif; padding:20px; text-align:center;} form{max-width:400px; margin:0 auto; text-align:left;} input,textarea{width:100%; margin:5px 0; padding:8px;}</style>
</head>
<body>
    <h1>Contact</h1>
    <a href="index.php">Inapoi</a>
    <p><?= $mesaj_status ?></p>
    <form method="POST">
        <label>Nume:</label><input type="text" name="nume" required>
        <label>Email:</label><input type="email" name="email" required>
        <label>Mesaj:</label><textarea name="mesaj" required></textarea>
        <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white; border:none;">Trimite</button>
    </form>
</body>
</html>