<?php
require '../conexion.php'; // Archivo de conexión a la base de datos

if (isset($_GET['id'])) {
    $id_paciente = $_GET['id'];

    $stmt = $conexion->prepare("SELECT nombre, apellido, dpi, telefono, fecha_nacimiento, sexo, grupo_etnico FROM pacientes WHERE id = ?");
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $paciente = $resultado->fetch_assoc();
    } else {
        echo "Paciente no encontrado.";
        exit();
    }
}
?>
