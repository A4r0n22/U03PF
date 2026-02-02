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
            </head>
            <body>
                <h1>Confirmar Eliminació</h1>
                <p>Estàs segur que vols eliminar l'usuari <strong><?php echo htmlspecialchars($usuari['nom']); ?> (<?php echo htmlspecialchars($usuari['email']); ?>)</strong>?</p>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit" name="confirm" value="1">Sí, eliminar</button>
                    <a href="index.php">Cancel·lar</a>
                </form>
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