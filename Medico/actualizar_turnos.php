<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../conexionL.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_estado'])) {
    if (!empty($_SESSION['correo'])) {
        while($conn->more_results()) {
            $conn->next_result();
            if($result = $conn->store_result()) {
                $result->free();
            }
        }

        $stmt = $conn->prepare("SELECT IdMedico FROM Medico WHERE CorreoMedico = ?");
        if($stmt === false) {
            die('Error en prepare: ' . $conn->error);
        }
        
        $stmt->bind_param("s", $_SESSION['correo']);
        if(!$stmt->execute()) {
            die('Error en execute: ' . $stmt->error);
        }
        
        $stmt->bind_result($idMedico);
        
        if ($stmt->fetch()) {
            $stmt->free_result();
            $stmt->close();
            
            $idTurno = $_POST['id_turno'];
            $nuevoEstado = $_POST['nuevo_estado'];
            
            $update = $conn->prepare("UPDATE Turnos SET EstadoCita = ? WHERE IdTurno = ? AND IdMedico = ?");
            if($update === false) {
                die('Error en prepare: ' . $conn->error);
            }
            
            $update->bind_param("sii", $nuevoEstado, $idTurno, $idMedico);
            if(!$update->execute()) {
                die('Error en execute: ' . $update->error);
            }
            $update->close();
        } else {
            $stmt->close();
        }
    }
}

header("Location: ".$_SERVER['HTTP_REFERER']);
exit();
?>