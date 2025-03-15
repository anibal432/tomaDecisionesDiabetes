<?php
include '../conexionDiabetes.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_responsable = isset($_POST['id_responsable']) ? $_POST['id_responsable'] : null;
    $id_paciente = $_POST['id_paciente'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $tercer_nombre = $_POST['tercer_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $no_dpi = $_POST['no_dpi'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    // Iniciar una transacción para asegurar la consistencia de los datos
    $conn->begin_transaction();

    try {
        if ($id_responsable) {
            // Si existe, actualizar el responsable
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
            // Si no existe, insertar un nuevo responsable
            $sql = "INSERT INTO ResponsablePaciente (IdPaciente, PrimerNombre, SegundoNombre, TercerNombre, PrimerApellido, SegundoApellido, NoDpi, Telefono, Email) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "issssssss",
                $id_paciente,
                $primer_nombre,
                $segundo_nombre,
                $tercer_nombre,
                $primer_apellido,
                $segundo_apellido,
                $no_dpi,
                $telefono,
                $email
            );
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Confirmar la transacción
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            // Revertir la transacción en caso de error
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        // Cerrar la consulta
        $stmt->close();
    } catch (Exception $e) {
        // Revertir la transacción en caso de excepción
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>