<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../conexionL.php');

session_start();

while ($conn->more_results() && $conn->next_result()) {
    if ($result = $conn->store_result()) {
        $result->free();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado']) && !empty($_SESSION['correo'])) {
    $stmt = $conn->prepare("SELECT IdMedico FROM Medico WHERE CorreoMedico = ?");
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $stmt->bind_result($idMedico);
    $stmt->fetch();
    $stmt->close();

    if (!$idMedico) {
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    $idTurno = $_POST['id_turno'];
    $nuevoEstado = $_POST['nuevo_estado'];

    if ($nuevoEstado === 'Atendiendo') {
        unset($_SESSION['paciente_actual']);

        $stmtTurno = $conn->prepare("SELECT PrimerNombrePac, SegundoNombrePac, PrimerApellidoPac, SegundoApellidoPac, 
                NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico FROM Turnos WHERE IdTurno = ? AND IdMedico = ?");
        $stmtTurno->bind_param("ii", $idTurno, $idMedico);
        $stmtTurno->execute();
        $resultTurno = $stmtTurno->get_result();

        if ($rowTurno = $resultTurno->fetch_assoc()) {
            $stmtCheck = $conn->prepare("SELECT IdPaciente FROM Paciente WHERE NoDpi = ?");
            $stmtCheck->bind_param("s", $rowTurno['NoDpi']);
            $stmtCheck->execute();
            $stmtCheck->bind_result($idPaciente);
            $stmtCheck->fetch();
            $stmtCheck->close();

            if (!$idPaciente) {
                $stmtInsert = $conn->prepare("INSERT INTO Paciente (IdDiabetes, NombreUno, NombreDos, PrimerApellido, SegundoApellido, 
                        NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("sssssssss",
                    $rowTurno['PrimerNombrePac'],
                    $rowTurno['SegundoNombrePac'],
                    $rowTurno['PrimerApellidoPac'],
                    $rowTurno['SegundoApellidoPac'],
                    $rowTurno['NoDpi'],
                    $rowTurno['Telefono'] ?? '',
                    $rowTurno['FechaNacimiento'],
                    $rowTurno['Sexo'],
                    $rowTurno['GrupoEtnico'] ?? '');
                $stmtInsert->execute();
                $idPaciente = $stmtInsert->insert_id;
                $stmtInsert->close();
            }
            $_SESSION['paciente_actual'] = $idPaciente;
        }

        $resultTurno->free();
        $stmtTurno->close();
    } elseif (in_array($nuevoEstado, ['Atendido', 'Cancelado'], true)) {
        unset($_SESSION['paciente_actual']);
    }

    $update = $conn->prepare("UPDATE Turnos SET EstadoCita = ? WHERE IdTurno = ? AND IdMedico = ?");
    $update->bind_param("sii", $nuevoEstado, $idTurno, $idMedico);
    $update->execute();
    $update->close();

    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}
?>