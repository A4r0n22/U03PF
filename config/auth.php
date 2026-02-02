<?php
if (!isset($_SESSION['usuari'])) {
    header('Location: login.php');
    exit;
}
