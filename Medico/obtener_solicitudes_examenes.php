<?php
require_once 'conexion.php';

header('Content-Type: text/html');

$idPaciente = isset($_GET['idPaciente']) ? (int)$_GET['idPaciente'] : 0;

try {
    // Obtener solicitudes de exámenes del paciente
    $sql = "SELECT s.IdSolicitud, s.FechaSolicitud, s.ExamenesSolicitados, s.Instrucciones, s.Estado,
                   CONCAT(m.PrimerNombre, ' ', m.PrimerApellido) AS MedicoSolicitante
            FROM SolicitudExamenes s
            JOIN Medico m ON s.IdMedico = m.IdMedico
            WHERE s.IdPaciente = ?
            ORDER BY s.FechaSolicitud DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPaciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($solicitud = $result->fetch_assoc()) {
            $badgeClass = $solicitud['Estado'] === 'Pendiente' ? 'bg-warning' : ($solicitud['Estado'] === 'Completado' ? 'bg-success' : 'bg-secondary');
            
            echo '<li class="list-group-item">';
            echo '<div class="d-flex justify-content-between align-items-start">';
            echo '<div>';
            echo '<h6 class="mb-1">Solicitud del ' . date('d/m/Y', strtotime($solicitud['FechaSolicitud'])) . ' <span class="badge ' . $badgeClass . '">' . $solicitud['Estado'] . '</span></h6>';
            echo '<small class="text-muted">Médico: ' . htmlspecialchars($solicitud['MedicoSolicitante']) . '</small>';
            echo '<div class="mt-2">';
            echo '<strong>Exámenes:</strong>';
            echo '<p>' . nl2br(htmlspecialchars($solicitud['ExamenesSolicitados'])) . '</p>';
            
            if (!empty($solicitud['Instrucciones'])) {
                echo '<strong>Instrucciones:</strong>';
                echo '<p>' . nl2br(htmlspecialchars($solicitud['Instrucciones'])) . '</p>';
            }
            
            echo '</div>';
            echo '</div>';
            
            // Solo mostrar botón de eliminar si el estado es Pendiente
            if ($solicitud['Estado'] === 'Pendiente') {
                echo '<button class="btn btn-sm btn-outline-danger btn-eliminar-solicitud" data-id="' . $solicitud['IdSolicitud'] . '">';
                echo '<i class="fas fa-trash-alt"></i>';
                echo '</button>';
            }
            
            echo '</div>';
            echo '</li>';
        }
    } else {
        echo '<li class="list-group-item text-muted">No hay solicitudes de exámenes registradas</li>';
    }
} catch (Exception $e) {
    echo '<li class="list-group-item text-danger">Error al cargar solicitudes: ' . htmlspecialchars($e->getMessage()) . '</li>';
}
?>