<?php
$host = "167.86.71.203"; // Cambia esto si tu base de datos está en otro servidor
$user = "allen"; // Usuario de la base de datos
$password = "patopatopato1"; // Contraseña de la base de datos (déjala vacía si no tiene)
$database = "DiabetesUmg"; // Nombre de la base de datos

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
