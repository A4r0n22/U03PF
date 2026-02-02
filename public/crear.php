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
</head>
<body>
    <h1>Crear Nou Usuari</h1>
    <form method="post">
        <label>Nom: <input type="text" name="nom" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Contrasenya: <input type="password" name="password" required></label><br>
        <?php if ($_SESSION['usuari']['rol'] === 'admin'): ?>
            <label>Rol:
                <select name="rol" required>
                    <option value="admin">Admin</option>
                    <option value="usuari">Usuari</option>
                </select>
            </label><br>
        <?php endif; ?>
        <button type="submit">Crear</button>
    </form>
    <a href="index.php">Tornar al llistat</a>
</body>
</html>