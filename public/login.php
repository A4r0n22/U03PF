<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



session_start();
include '../config/connexio.php';


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuaris WHERE email = ?");
    $stmt->execute([$email]);
    $usuari = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuari && password_verify($password, $usuari['password'])) {
        $_SESSION['usuari'] = [
            'id' => $usuari['id'],
            'nom' => $usuari['nom'],
            'email' => $usuari['email'],
            'rol' => $usuari['rol']
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Credencials incorrectes';
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">

</head>
<body>
    <div class="container">
        <h1>Login</h1>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Email
                <input type="email" name="email" required>
            </label>
            <label>Contrasenya
                <input type="password" name="password" required>
            </label>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>

</html>
