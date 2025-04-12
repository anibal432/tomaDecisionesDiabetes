<?php
include '../conexionDiabetes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_responsable = isset($_POST['id_responsable']) ? $_POST['id_responsable'] : null;
    $id_paciente = $_POST['id_paciente'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'] ?? null;
    $tercer_nombre = $_POST['tercer_nombre'] ?? null;
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'] ?? null;
    $no_dpi = $_POST['no_dpi'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $conn->begin_transaction();

    try {
        if ($id_responsable) {
            $sql = "UPDATE ResponsablePaciente 
                    SET PrimerNombre = ?, SegundoNombre = ?, TercerNombre = ?,
                        PrimerApellido = ?, SegundoApellido = ?, NoDpi = ?,
                        Telefono = ?, Email = ?
                    WHERE IdResponsable = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssi",
                $primer_nombre,
                $segundo_nombre,
                $tercer_nombre,
                $primer_apellido,
                $segundo_apellido,
                $no_dpi,
                $telefono,
                $email,
                $id_responsable
            );
        } else {
            $sql = "INSERT INTO ResponsablePaciente 
                    (PrimerNombre, SegundoNombre, TercerNombre, PrimerApellido, 
                     SegundoApellido, NoDpi, Telefono, Email, IdPaciente) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssi",
                $primer_nombre,
                $segundo_nombre,
                $tercer_nombre,
                $primer_apellido,
                $segundo_apellido,
                $no_dpi,
                $telefono,
                $email,
                $id_paciente
            );
        }

        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>