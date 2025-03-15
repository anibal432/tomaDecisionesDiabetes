<?php
include '../conexionDiabetes.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_ant_personal = isset($_POST['id_ant_personal']) ? $_POST['id_ant_personal'] : null;
    $id_paciente = $_POST['id_paciente'];
    $medicos = $_POST['medicos'];
    $quirurgicos = $_POST['quirurgicos'];
    $traumaticos = $_POST['traumaticos'];
    $ginecobstetricos = $_POST['ginecobstetricos'];
    $alergias = $_POST['alergias'];
    $vicios_manias = $_POST['vicios_manias'];

    // Iniciar una transacción para asegurar la consistencia de los datos
    $conn->begin_transaction();

    try {
        if ($id_ant_personal) {
            // Si existe, actualizar los antecedentes personales
            $sql = "UPDATE AntecedentesPersonales 
                    SET Medicos = ?, Quirurgicos = ?, Traumaticos = ?, 
                        Ginecobstetricos = ?, Alergias = ?, ViciosManias = ? 
                    WHERE IdAntPersonal = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssi",
                $medicos,
                $quirurgicos,
                $traumaticos,
                $ginecobstetricos,
                $alergias,
                $vicios_manias,
                $id_ant_personal
            );
        } else {
            // Si no existe, insertar nuevos antecedentes personales
            $sql = "INSERT INTO AntecedentesPersonales (IdPaciente, Medicos, Quirurgicos, Traumaticos, Ginecobstetricos, Alergias, ViciosManias) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "issssss",
                $id_paciente,
                $medicos,
                $quirurgicos,
                $traumaticos,
                $ginecobstetricos,
                $alergias,
                $vicios_manias
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