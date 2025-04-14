<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = date('Y-m-d');
    $id_paciente = $_POST['id_paciente'] ?? null;
    $observaciones = $_POST['observaciones'] ?? '';
    $medicamentos = $_POST['medicamentos'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    $observaciones_detalle = $_POST['observaciones_detalle'] ?? [];

    if (!$id_paciente || empty($medicamentos)) {
        echo "Debe seleccionar un paciente y agregar al menos un medicamento.";
        exit;
    }

    // Insertar receta
    $stmt = $conn->prepare("INSERT INTO Receta (IdPaciente, FechaReceta, Observaciones) VALUES (?, CURDATE(), ?)");
    $stmt->bind_param("is", $id_paciente, $observaciones);
    
    if ($stmt->execute()) {
        $id_receta = $stmt->insert_id;

        // Insertar cada medicamento como detalle
        $stmt_detalle = $conn->prepare("INSERT INTO DetalleReceta (IdReceta, Medicamento, Cantidad, Observacion) VALUES (?, ?, ?, ?)");

        foreach ($medicamentos as $i => $med) {
            $med = trim($med);
            $cantidad = isset($cantidades[$i]) ? (int)$cantidades[$i] : 1;
            $obs_detalle = $observaciones_detalle[$i] ?? null;
        
            if ($med !== '' && $cantidad > 0) {
                $stmt_detalle->bind_param("isis", $id_receta, $med, $cantidad, $obs_detalle);
                $stmt_detalle->execute();
        
                // Puedes guardar la observación individual en una columna adicional si la agregas
            }
        }
        echo "✅ Receta guardada correctamente.";
        // Opcional: redirigir
        // header("Location: formulario_receta.php?exito=1&id_paciente=$id_paciente");
    } else {
        echo "❌ Error al guardar la receta: " . $stmt->error;
    }
}
?>
