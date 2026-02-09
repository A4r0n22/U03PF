<?php
session_start();
include '../config/connexio.php';
include '../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM usuaris WHERE id = ?");
        try {
            $stmt->execute([$id]);
            $_SESSION['message'] = 'Usuari eliminat correctament.';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $message = 'Error en eliminar l\'usuari.';
        }
    } elseif (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT nom, email FROM usuaris WHERE id = ?");
        $stmt->execute([$id]);
        $usuari = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuari) {
            // Mostrar confirmació
            ?>
            <!DOCTYPE html>
            <html lang="ca">
            <head>
                <meta charset="UTF-8">
                <title>Confirmar Eliminació</title>
                <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">

            </head>
            <body>
<div class="container">
    <h1>Eliminar Usuari</h1>

    <p>
        Estàs segur que vols eliminar:<br><br>
        <strong><?= htmlspecialchars($usuari['nom']) ?></strong><br>
        <?= htmlspecialchars($usuari['email']) ?>
    </p>

    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <button type="submit" name="confirm" value="1">Sí, eliminar</button>
    </form>

    <br>
    <a href="index.php">Cancel·lar</a>
</div>
</body>

            </html>
            <?php
            exit;
        } else {
            $message = 'Usuari no trobat.';
        }
    }
} else {
    header('Location: index.php');
    exit;
}

if ($message) {
    echo "<p style='color: red;'>$message</p>";
    echo '<a href="index.php">Tornar al llistat</a>';
}
?>