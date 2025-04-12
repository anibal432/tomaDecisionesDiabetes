<?php
session_start();
include('conexionL.php');

if (!empty($_GET['id'])) {
    $idTurno = $_GET['id'];
    
    $query = "DELETE FROM Turnos WHERE IdTurno = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idTurno);
    
    if ($stmt->execute()) {
        header("Location: turnospacientes.php?success=Turno eliminado correctamente");
    } else {
        header("Location: turnospacientes.php?error=Error al eliminar el turno");
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: turnospacientes.php");
}
exit();
?>