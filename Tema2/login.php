<?php
session_start(); // Pornim "memoria" browserului
require 'db.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $parola = $_POST['parola'];

    // Cautam utilizatorul dupa email
    $stmt = $pdo->prepare("SELECT * FROM t_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verificam daca userul exista SI daca parola se potriveste cu cea criptata
    if ($user && password_verify($parola, $user['parola_hash'])) {
        // SETAM VARIABILELE DE SESIUNE
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nume'] = $user['nume'];
        $_SESSION['user_rol'] = $user['rol_id'];
        
        // Il trimitem pe prima pagina
        header("Location: index.php");
        exit;
    } else {
        $mesaj = "Email sau parolă incorectă!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login - Fitness</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; }
        input { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
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
        
        <button type="submit">Intră în cont</button>
    </form>
    <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
</body>
</html>