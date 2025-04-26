<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../conexionL.php');
session_start();

while ($conn->more_results()) {
    $conn->next_result();
    if ($res = $conn->store_result()) {
        $res->free();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_estado'])) {
    if (!empty($_SESSION['correo'])) {
        $stmt = $conn->prepare("SELECT IdMedico FROM Medico WHERE CorreoMedico = ?");
        if (!$stmt) {
            die("Error preparando consulta: " . $conn->error);
        }
        $stmt->bind_param("s", $_SESSION['correo']);
        $stmt->execute();
        $stmt->bind_result($idMedico);
        
        if ($stmt->fetch()) {
            $id = $_POST['id'];
            $tipo = $_POST['tipo'];
            $nuevoEstado = $_POST['nuevo_estado'];
            
            $stmt->free_result();
            $stmt->close();

            if ($nuevoEstado == 'Atendiendo') {
                $sqlDatos = ($tipo == 'turno') 
                    ? "SELECT PrimerNombrePac, SegundoNombrePac, PrimerApellidoPac, SegundoApellidoPac, 
                              NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico 
                       FROM Turnos WHERE IdTurno = ? AND IdMedico = ?"
                    : "SELECT primer_nombre as PrimerNombrePac, segundo_nombre as SegundoNombrePac, 
                              primer_apellido as PrimerApellidoPac, segundo_apellido as SegundoApellidoPac, 
                              NoDpi, numero_celular as Telefono, FechaNacimiento, Sexo, GrupoEtnico 
                       FROM citas WHERE id = ? AND IdMedico = ?";
                
                $stmtDatos = $conn->prepare($sqlDatos);
                if (!$stmtDatos) {
                    die("Error preparando consulta de datos: " . $conn->error);
                }
                $stmtDatos->bind_param("ii", $id, $idMedico);
                $stmtDatos->execute();
                $resultDatos = $stmtDatos->get_result();
                
                if ($rowDatos = $resultDatos->fetch_assoc()) {
                    $sexo = $rowDatos['Sexo'];
                    if ($tipo == 'cita') {
                        $sexo = match(strtolower($sexo)) {
                            'masculino', 'hombre', 'm' => 'Masculino',
                            'femenino', 'mujer', 'f' => 'Femenino',
                            default => 'Prefiero no decir'
                        };
                    }

                    $stmtCheck = $conn->prepare("SELECT IdPaciente FROM Paciente WHERE NoDpi = ?");
                    if (!$stmtCheck) {
                        die("Error preparando verificación de paciente: " . $conn->error);
                    }
                    $stmtCheck->bind_param("s", $rowDatos['NoDpi']);
                    $stmtCheck->execute();
                    $stmtCheck->store_result();
                    
                    if ($stmtCheck->num_rows == 0) {
                        $stmtInsert = $conn->prepare("INSERT INTO Paciente 
                            (IdDiabetes, NombreUno, NombreDos, PrimerApellido, SegundoApellido, 
                            NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico) 
                            VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        if (!$stmtInsert) {
                            die("Error preparando inserción: " . $conn->error);
                        }
                        $stmtInsert->bind_param("sssssssss", 
                            $rowDatos['PrimerNombrePac'], 
                            $rowDatos['SegundoNombrePac'],
                            $rowDatos['PrimerApellidoPac'],
                            $rowDatos['SegundoApellidoPac'],
                            $rowDatos['NoDpi'],
                            $rowDatos['Telefono'],
                            $rowDatos['FechaNacimiento'],
                            $sexo, 
                            $rowDatos['GrupoEtnico']);
                            
                        if (!$stmtInsert->execute()) {
                            error_log("Error al insertar paciente: " . $stmtInsert->error);
                            die("Error al registrar paciente: " . $stmtInsert->error);
                        }
                        $idPaciente = $stmtInsert->insert_id;
                        $stmtInsert->close();
                    } else {
                        $stmtCheck->bind_result($idPaciente);
                        $stmtCheck->fetch();
                    }
                    $stmtCheck->close();
                    
                    $_SESSION['paciente_actual'] = $idPaciente;
                }
                $resultDatos->free();
                $stmtDatos->close();
            } 
            elseif (in_array($nuevoEstado, ['Atendido', 'Cancelado', 'Atendida'])) {
                if (isset($_SESSION['paciente_actual'])) {
                    unset($_SESSION['paciente_actual']);
                }
            }
            $sqlUpdate = ($tipo == 'turno') 
                ? "UPDATE Turnos SET EstadoCita = ? WHERE IdTurno = ? AND IdMedico = ?"
                : "UPDATE citas SET estado = ? WHERE id = ? AND IdMedico = ?";
                
            $update = $conn->prepare($sqlUpdate);
            if (!$update) {
                die("Error preparando actualización: " . $conn->error);
            }
            $update->bind_param("sii", $nuevoEstado, $id, $idMedico);
            if (!$update->execute()) {
                error_log("Error al actualizar estado: " . $update->error);
                die("Error al actualizar estado: " . $update->error);
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