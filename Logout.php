<?php
session_start();

if (!isset($_SESSION['correo'])) {
    header("Location: index.php");
    exit;
}

include('conexionL.php');
session_destroy();

header("Location: index.php");
exit;
?>
