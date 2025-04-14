<?php
$servername = "167.86.71.203";
$username = "allen";       
$password = "patopatopato1";           
$dbname = "DiabetesUmg"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {

}
?>