<?php
require_once 'conexion.php';

header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['idPaciente'])) {
    $idPaciente = (int)$_GET['idPaciente'];
    
    try {
        $sql = "SELECT d.IdDiagnostico, d.FechaDiagnostico, c.Codigo, c.Descripcion 
                FROM Diagnostico d
                JOIN CIE10 c ON d.IdCIE10 = c.IdCIE10
                WHERE d.IdPaciente = ?
                ORDER BY d.FechaDiagnostico DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idPaciente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                echo '<div>';
                echo '<span class="badge bg-primary me-2">' . htmlspecialchars($row['Codigo']) . '</span>';
                echo htmlspecialchars($row['Descripcion']);
                echo '<br><small class="text-muted">' . date('d/m/Y H:i', strtotime($row['FechaDiagnostico'])) . '</small>';
                echo '</div>';
                echo '<button class="btn btn-sm btn-outline-danger btn-eliminar-diagnostico" data-id="' . $row['IdDiagnostico'] . '" title="Eliminar diagnóstico">';
                echo '<i class="fas fa-trash-alt"></i>';
                echo '</button>';
                echo '</li>';
            }
        } else {
            echo '<li class="list-group-item text-center text-muted py-4">';
            echo '<i class="fas fa-info-circle me-2"></i>No se encontraron diagnósticos registrados';
            echo '</li>';
        }
    } catch (Exception $e) {
        echo '<li class="list-group-item text-center text-danger py-4">';
        echo '<i class="fas fa-exclamation-triangle me-2"></i>Error al cargar diagnósticos';
        echo '</li>';
    }
    
    if (isset($stmt)) $stmt->close();
} else {
    echo '<li class="list-group-item text-center text-danger py-4">';
    echo '<i class="fas fa-exclamation-triangle me-2"></i>Paciente no especificado';
    echo '</li>';
}

$conn->close();
?>