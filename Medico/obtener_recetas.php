<?php
require_once 'conexion.php';

header('Content-Type: text/html');

$idPaciente = isset($_GET['idPaciente']) ? (int)$_GET['idPaciente'] : 0;

try {
    $sqlRecetas = "SELECT r.IdReceta, r.FechaReceta, r.Observaciones 
                   FROM Receta r 
                   WHERE r.IdPaciente = ? 
                   ORDER BY r.FechaReceta DESC";
    $stmtRecetas = $conn->prepare($sqlRecetas);
    $stmtRecetas->bind_param("i", $idPaciente);
    $stmtRecetas->execute();
    $resultRecetas = $stmtRecetas->get_result();

    if ($resultRecetas->num_rows > 0) {
        while ($receta = $resultRecetas->fetch_assoc()) {
            echo '<li class="list-group-item">';
            echo '<div class="d-flex justify-content-between align-items-start">';
            echo '<div>';
            echo '<h6 class="mb-1">Receta del ' . date('d/m/Y', strtotime($receta['FechaReceta'])) . '</h6>';
            
            // Obtener detalles de la receta
            $sqlDetalles = "SELECT Medicamento, Cantidad, Observacion 
                            FROM DetalleReceta 
                            WHERE IdReceta = ?";
            $stmtDetalles = $conn->prepare($sqlDetalles);
            $stmtDetalles->bind_param("i", $receta['IdReceta']);
            $stmtDetalles->execute();
            $resultDetalles = $stmtDetalles->get_result();
            
            echo '<ul class="list-unstyled ms-3">';
            while ($detalle = $resultDetalles->fetch_assoc()) {
                echo '<li>';
                echo '<small>' . htmlspecialchars($detalle['Medicamento']) . ' - ';
                echo 'Cantidad: ' . $detalle['Cantidad'];
                if (!empty($detalle['Observacion'])) {
                    echo ' (' . htmlspecialchars($detalle['Observacion']) . ')';
                }
                echo '</small>';
                echo '</li>';
            }
            echo '</ul>';
            
            if (!empty($receta['Observaciones'])) {
                echo '<small class="text-muted">Observaciones: ' . htmlspecialchars($receta['Observaciones']) . '</small>';
            }
            
            echo '</div>';
            echo '<button class="btn btn-sm btn-outline-danger btn-eliminar-receta" data-id="' . $receta['IdReceta'] . '">';
            echo '<i class="fas fa-trash-alt"></i>';
            echo '</button>';
            echo '</div>';
            echo '</li>';
        }
    } else {
        echo '<li class="list-group-item text-muted">No hay recetas registradas</li>';
    }
} catch (Exception $e) {
    echo '<li class="list-group-item text-danger">Error al cargar recetas: ' . htmlspecialchars($e->getMessage()) . '</li>';
}
?>