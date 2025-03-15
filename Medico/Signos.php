<?php
require 'conexion.php'; // Archivo con la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $peso = $_POST['peso'];
    $talla = $_POST['talla'];
    $presion = $_POST['presion'];
    $imc = $_POST['imc'];
    $temperatura = $_POST['temperatura'];
    $frecuenciaCardiaca = $_POST['frecuenciaCardiaca'];
    $oxigenacion = $_POST['oxigenacion'];
    $frecuenciaRespiratoria = $_POST['frecuenciaRespiratoria'];
    $idPaciente = $_POST['idPaciente'];
    
    $query = "INSERT INTO SignosVitales (Peso, Talla, PresionArterial, IndiceMasaCorporal, Temperatura, FrecuenciaCardiaca, Oxigenacion, FrecuenciaRespiratoria, Fecha, idPaciente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddssddddi", $peso, $talla, $presion, $imc, $temperatura, $frecuenciaCardiaca, $oxigenacion, $frecuenciaRespiratoria, $idPaciente);
    $stmt->execute();
    
    header("Location: ingreso_signos.php"); // Redirige para evitar reenvíos
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Signos Vitales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Ingreso de Signos Vitales</h2>
        <div class="card p-4 shadow">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Peso (kg):</label>
                    <input type="number" step="0.01" class="form-control" name="peso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Talla (m):</label>
                    <input type="number" step="0.01" class="form-control" name="talla" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Presión Arterial:</label>
                    <input type="text" class="form-control" name="presion" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Índice de Masa Corporal (IMC):</label>
                    <input type="number" step="0.01" class="form-control" name="imc" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Temperatura (°C):</label>
                    <input type="number" step="0.01" class="form-control" name="temperatura" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Frecuencia Cardíaca (latidos/min):</label>
                    <input type="number" class="form-control" name="frecuenciaCardiaca" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Oxigenación (%):</label>
                    <input type="number" step="0.01" class="form-control" name="oxigenacion" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Frecuencia Respiratoria (respiraciones/min):</label>
                    <input type="number" class="form-control" name="frecuenciaRespiratoria" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ID Paciente:</label>
                    <input type="number" class="form-control" name="idPaciente" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar Signos Vitales</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
