<?php
session_start();
include '../config/connexio.php';
include '../config/auth.php';

$usuari = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT id, nom, email FROM usuaris WHERE id = ?");
    $stmt->execute([$id]);
    $usuari = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuari) {
        $_SESSION['message'] = 'Usuari no trobat.';
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validació
    $errors = [];
    if (empty($nom)) {
        $errors[] = 'El nom és obligatori.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email no vàlid.';
    }

    if (empty($errors)) {
        $updateFields = "nom = ?, email = ?";
        $params = [$nom, $email, $id];

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $updateFields .= ", password = ?";
            $params = [$nom, $email, $hashed_password, $id];
        } else {
            $params = [$nom, $email, $id];
        }

        $stmt = $pdo->prepare("UPDATE usuaris SET $updateFields WHERE id = ?");
        try {
            $stmt->execute($params);
            $_SESSION['message'] = 'Usuari actualitzat correctament.';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = 'L\'email ja existeix.';
            } else {
                $errors[] = 'Error en actualitzar l\'usuari.';
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
    <title>Editar Usuari</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">

</head>
<body>
<div class="container">
    <h1>Editar Usuari</h1>

    <?php if (!empty($message)): ?>
        <p class="error"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Nom
            <input type="text" name="nom" value="<?= htmlspecialchars($usuari['nom']) ?>" required>
        </label>
        <label>Email
            <input type="email" name="email" value="<?= htmlspecialchars($usuari['email']) ?>" required>
        </label>
        <label>Nova contrasenya
            <input type="password" name="password">
        </label>

        <button type="submit">Actualitzar</button>
    </form>

    <a href="index.php">← Tornar</a>
</div>
</body>

</html>