<?php
include '../conexionDiabetes.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPaciente = $_POST['id_paciente']; // Obtiene el ID del paciente

    // Consulta para obtener los antecedentes personales
    $sql = "SELECT IdAntPersonal, IdPaciente, Medicos, Quirurgicos, Traumaticos, Ginecobstetricos, Alergias, ViciosManias 
            FROM AntecedentesPersonales 
            WHERE IdPaciente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPaciente); // Asocia el parámetro
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