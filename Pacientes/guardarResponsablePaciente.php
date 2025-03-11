<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_responsable = $_POST['id_responsable'];
    $id_paciente = $_POST['id_paciente'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $tercer_nombre = $_POST['tercer_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $no_dpi = $_POST['no_dpi'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if (empty($id_responsable)) {
        // Insertar nuevo responsable
        $sql = "INSERT INTO ResponsablePaciente (IdPaciente, PrimerNombre, SegundoNombre, TercerNombre, PrimerApellido, SegundoApellido, NoDpi, Telefono, Email) 
                VALUES ('$id_paciente', '$primer_nombre', '$segundo_nombre', '$tercer_nombre', '$primer_apellido', '$segundo_apellido', '$no_dpi', '$telefono', '$email')";
    } else {
        // Actualizar responsable existente
        $sql = "UPDATE ResponsablePaciente 
                SET PrimerNombre = '$primer_nombre', SegundoNombre = '$segundo_nombre', TercerNombre = '$tercer_nombre', 
                    PrimerApellido = '$primer_apellido', SegundoApellido = '$segundo_apellido', NoDpi = '$no_dpi', 
                    Telefono = '$telefono', Email = '$email' 
                WHERE IdResponsable = '$id_responsable'";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $conn->close();
}
?>