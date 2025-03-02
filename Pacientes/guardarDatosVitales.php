<?php
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = (int)$_POST['id_paciente'];
    $peso = $_POST['peso'];
    $talla = $_POST['talla'];
    $presion_arterial = $_POST['presion_arterial'];
    $imc = $_POST['imc'];
    $temperatura = $_POST['temperatura'];
    $frecuencia_cardiaca = $_POST['frecuencia_cardiaca'];
    $oxigenacion = $_POST['oxigenacion'];
    $frecuencia_respiratoria = $_POST['frecuencia_respiratoria'];

    // Verificar si ya hay registros de signos vitales para este paciente
    $sql = "SELECT idSignosVitales FROM SignosVitales WHERE idPaciente = ? ORDER BY Fecha DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Si existe, actualiza el registro
        $id_signos_vitales = $row['idSignosVitales'];
        $sql_update = "UPDATE SignosVitales SET 
            Peso = ?, 
            Talla = ?, 
            PresionArterial = ?, 
            IndiceMasaCorporal = ?, 
            Temperatura = ?, 
            FrecuenciaCardiaca = ?, 
            Oxigenacion = ?, 
            FrecuenciaRespiratoria = ? 
            WHERE idSignosVitales = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ddssdiddi", $peso, $talla, $presion_arterial, $imc, $temperatura, $frecuencia_cardiaca, $oxigenacion, $frecuencia_respiratoria, $id_signos_vitales);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Si no existe, inserta un nuevo registro
        $sql_insert = "INSERT INTO SignosVitales (Peso, Talla, PresionArterial, IndiceMasaCorporal, Temperatura, FrecuenciaCardiaca, Oxigenacion, FrecuenciaRespiratoria, idPaciente)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ddssdidd", $peso, $talla, $presion_arterial, $imc, $temperatura, $frecuencia_cardiaca, $oxigenacion, $frecuencia_respiratoria, $id_paciente);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Método de solicitud no permitido.']);
}

$conn->close();
?>