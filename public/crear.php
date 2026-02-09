<?php
session_start();
include '../config/connexio.php';
include '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'usuari';

    // Validació
    $errors = [];
    if (empty($nom)) {
        $errors[] = 'El nom és obligatori.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email no vàlid.';
    }
    if (empty($password)) {
        $errors[] = 'La contrasenya és obligatòria.';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if ($_SESSION['usuari']['rol'] === 'admin') {
            $rol = $_POST['rol'] ?? 'usuari'; // admin puede elegir
        } else {
            $rol = 'usuari'; // usuarios normales solo pueden crear usuarios con rol usuari
        }
        $stmt = $pdo->prepare("INSERT INTO usuaris (nom, email, password, rol) VALUES (?, ?, ?, ?)");
        try {
            
            $stmt->execute([$nom, $email, $hashed_password, $rol]);
            $_SESSION['message'] = 'Usuari creat correctament.';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $errors[] = 'L\'email ja existeix.';
            } else {
                $errors[] = 'Error en crear l\'usuari.';
            }
        }
    }

    if (!empty($errors)) {
        $message = implode('<br>', $errors);
    }

    
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuari</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">

</head>
<body>
<div class="container">
    <h1>Crear Usuari</h1>

    <?php if (!empty($message)): ?>
        <p class="error"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Nom
            <input type="text" name="nom" required>
        </label>
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Contrasenya
            <input type="password" name="password" required>
        </label>

        <?php if ($_SESSION['usuari']['rol'] === 'admin'): ?>
            <label>Rol
                <select name="rol">
                    <option value="usuari">Usuari</option>
                    <option value="admin">Admin</option>
                </select>
            </label>
        <?php endif; ?>

        <button type="submit">Crear</button>
    </form>

    <a href="index.php">← Tornar</a>
</div>
</body>

</html>