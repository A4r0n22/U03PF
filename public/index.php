<?php
session_start();
include '../config/connexio.php';
include '../config/auth.php';

// Crear la taula si no existeix
$sql = "CREATE TABLE IF NOT EXISTS usuaris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    data_creacio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($sql);

// Obtenir usuaris
$stmt = $pdo->query("SELECT id, nom, email, rol, data_creacio FROM usuaris ORDER BY data_creacio DESC");
$usuaris = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Llistat d'Usuaris</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Llistat d'Usuaris</h1>
    <a href="crear.php">Afegir Nou Usuari</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Data de Creació</th>
                <th>Accions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuaris as $usuari): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuari['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuari['nom']); ?></td>
                    <td><?php echo htmlspecialchars($usuari['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuari['rol'] ?? 'usuari'); ?></td>
                    <td><?php echo htmlspecialchars($usuari['data_creacio']); ?></td>
                    <td class="actions">
                    <?php if ($_SESSION['usuari']['rol'] === 'admin'): ?>
                        <a href="editar.php?id=<?php echo $usuari['id']; ?>">Editar</a>
                        <form method="post" action="eliminar.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $usuari['id']; ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    <?php else: ?>
                        No permès
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>