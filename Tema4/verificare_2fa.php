<?php
session_start();
if (!isset($_SESSION['2fa_code'])) { header("Location: login.php"); exit; }

$mesaj = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod_introdus = $_POST['cod'];
    
    if ($cod_introdus == $_SESSION['2fa_code']) {
        $_SESSION['user_id'] = $_SESSION['2fa_user_data']['id'];
        $_SESSION['user_nume'] = $_SESSION['2fa_user_data']['nume'];
        $_SESSION['user_rol'] = $_SESSION['2fa_user_data']['rol_id'];
        
        unset($_SESSION['2fa_code']);
        unset($_SESSION['2fa_user_data']);
        
        header("Location: index.php");
        exit;
    } else {
        $mesaj = "❌ Cod incorect!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Verificare 2FA</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 100px auto; text-align: center; padding: 20px; border: 1px solid #ccc; }
        input { font-size: 20px; text-align: center; padding: 10px; width: 80%; margin: 20px 0; letter-spacing: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Verificare Email 📧</h2>
    <p>Am trimis un cod pe email-ul tău.</p>
    <p style="color:red"><?= $mesaj ?></p>
    
    <form method="POST">
        <input type="number" name="cod" placeholder="######" required>
        <br>
        <button type="submit">Verifică</button>
    </form>
</body>
</html>