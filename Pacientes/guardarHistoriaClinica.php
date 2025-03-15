<?php
include '../conexionDiabetes.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_historia_clinica = isset($_POST['id_historia_clinica']) ? $_POST['id_historia_clinica'] : null;
    $id_paciente = $_POST['id_paciente'];
    $motivo_consulta = $_POST['motivo_consulta'];
    $historia_enf_actual = $_POST['historia_enf_actual'];
    $datos_subjetivos = $_POST['datos_subjetivos'];
    $examen_fisico = $_POST['examen_fisico'];
    $impresion_clinica = $_POST['impresion_clinica'];
    $tratamiento = $_POST['tratamiento'];
    $estudios_laboratorio = $_POST['estudios_laboratorio'];

    // Iniciar una transacción para asegurar la consistencia de los datos
    $conn->begin_transaction();

    try {
        if ($id_historia_clinica) {
            // Si existe, actualizar la historia clínica
            $sql = "UPDATE HistoriaClinica 
                    SET MotivoConsulta = ?, HistoriaEnfActual = ?, DatosSubjetivos = ?, 
                        ExamenFisico = ?, ImpresionClinica = ?, Tratamiento = ?, 
                        EstudiosLaboratorio = ? 
                    WHERE IdHistoriaClinica = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssssi",
                $motivo_consulta,
                $historia_enf_actual,
                $datos_subjetivos,
                $examen_fisico,
                $impresion_clinica,
                $tratamiento,
                $estudios_laboratorio,
                $id_historia_clinica
            );
        } else {
            // Si no existe, insertar una nueva historia clínica
            $sql = "INSERT INTO HistoriaClinica (IdPaciente, MotivoConsulta, HistoriaEnfActual, DatosSubjetivos, ExamenFisico, ImpresionClinica, Tratamiento, EstudiosLaboratorio) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "isssssss",
                $id_paciente,
                $motivo_consulta,
                $historia_enf_actual,
                $datos_subjetivos,
                $examen_fisico,
                $impresion_clinica,
                $tratamiento,
                $estudios_laboratorio
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