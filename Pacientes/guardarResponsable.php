<?php
include '../conexionDiabetes.php';

// Datos del formulario
$idResponsable = $_POST['id_responsable'] ?? null;
$idPaciente = $_POST['id_paciente'];
$primerNombre = $_POST['primer_nombre'];
$segundoNombre = $_POST['segundo_nombre'] ?? null;
$tercerNombre = $_POST['tercer_nombre'] ?? null;
$primerApellido = $_POST['primer_apellido'];
$segundoApellido = $_POST['segundo_apellido'] ?? null;
$dpi = $_POST['dpi'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];

try {
    if (!empty($idResponsable)) {
        // Actualizar registro existente
        $sql = "UPDATE ResponsablePaciente SET 
                PrimerNombre = ?, 
                SegundoNombre = ?, 
                TercerNombre = ?, 
                PrimerApellido = ?, 
                SegundoApellido = ?, 
                NoDpi = ?, 
                Telefono = ?, 
                Email = ? 
                WHERE IdResponsable = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, 
                         $segundoApellido, $dpi, $telefono, $email, $idResponsable);
        $message = "Responsable actualizado correctamente";
    } else {
        // Insertar nuevo registro
        $sql = "INSERT INTO ResponsablePaciente (
                PrimerNombre, 
                SegundoNombre, 
                TercerNombre, 
                PrimerApellido, 
                SegundoApellido, 
                NoDpi, 
                Telefono, 
                Email, 
                IdPaciente
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, 
                         $segundoApellido, $dpi, $telefono, $email, $idPaciente);
        $message = "Responsable creado correctamente";
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>