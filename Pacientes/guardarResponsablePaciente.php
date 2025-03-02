<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar datos del formulario
    $idPaciente = $_POST['id_paciente'];
    $primerNombre = $_POST['primer_nombre'];
    $segundoNombre = $_POST['segundo_nombre'];
    $tercerNombre = $_POST['tercer_nombre'];
    $primerApellido = $_POST['primer_apellido'];
    $segundoApellido = $_POST['segundo_apellido'];
    $dpi = $_POST['dpi'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    // Insertar datos en la tabla ResponsablePaciente
    $stmt = $conn->prepare("INSERT INTO ResponsablePaciente (PrimerNombre, SegundoNombre, TercerNombre, PrimerApellido, SegundoApellido, NoDpi, Telefono, Email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $dpi, $telefono, $email);

    if ($stmt->execute()) {
        $idResponsable = $stmt->insert_id; // Obtener el ID del responsable insertado

        // Actualizar el paciente con el IdResponsable
        $stmt2 = $conn->prepare("UPDATE Paciente SET IdResponsable = ? WHERE IdPaciente = ?");
        $stmt2->bind_param("ii", $idResponsable, $idPaciente);
        $stmt2->execute();
        $stmt2->close();

        header("Location: pacientesPrueba.php?id=" . $idPaciente);
        exit();
    } else {
        echo "Error al guardar el responsable: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>