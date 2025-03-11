<?php
$host = "167.86.71.203";
$username = "allen";
$password = "patopatopato1";
$dbname = "AnalisisRec";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>