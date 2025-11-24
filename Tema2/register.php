<?php
require 'db.php';
$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume = $_POST['nume'];
    $email = $_POST['email'];
    $parola = $_POST['parola'];

    // 1. Verificam daca emailul exista deja
    $stmt = $pdo->prepare("SELECT id FROM t_users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $mesaj = "Acest email este deja folosit!";
    } else {
        // 2. Criptam parola (obligatoriu pentru securitate)
        $parola_hash = password_hash($parola, PASSWORD_DEFAULT);
        
        // 3. Introducem userul (rol_id 3 = client)
        $sql = "INSERT INTO t_users (nume, email, parola_hash, rol_id) VALUES (?, ?, ?, 3)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nume, $email, $parola_hash])) {
            $mesaj = "✅ Cont creat cu succes! <a href='login.php'>Autentifică-te aici</a>";
        } else {
            $mesaj = "❌ A apărut o eroare la salvare.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Inregistrare - Fitness</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; }
        input { width: 100%; padding: 10px; margin: 5px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        button:hover { background: #218838; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Creează Cont Nou</h2>
    <p><?= $mesaj ?></p>
    
    <form method="POST">
        <label>Nume Complet:</label>
        <input type="text" name="nume" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Parola:</label>
        <input type="password" name="parola" required>
        
        <button type="submit">Înregistrează-te</button>
    </form>
    <p>Ai deja cont? <a href="login.php">Loghează-te</a></p>
</body>
</html>