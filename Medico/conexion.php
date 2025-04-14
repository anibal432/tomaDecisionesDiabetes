<?php
$host = "167.86.71.203";
$user = "allen"; 
$password = "patopatopato1"; 
$database = "DiabetesUmg";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
