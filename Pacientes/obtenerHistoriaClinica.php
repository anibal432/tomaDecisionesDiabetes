<?php
include '../conexionDiabetes.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idHistoriaClinica = $_POST['id_historia_clinica']; // Obtiene el ID de la historia clínica

    // Consulta para obtener los datos de la historia clínica
    $sql = "SELECT IdHistoriaClinica, IdPaciente, MotivoConsulta, HistoriaEnfActual, DatosSubjetivos, ExamenFisico, ImpresionClinica, Tratamiento, EstudiosLaboratorio 
            FROM HistoriaClinica 
            WHERE IdHistoriaClinica = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idHistoriaClinica); // Asocia el parámetro
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Obtiene la fila de resultados
        echo json_encode($row); // Devuelve los datos en formato JSON
    } else {
        echo json_encode(null); // Si no hay resultados, devuelve null
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>