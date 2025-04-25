<?php
session_start();
header('Content-Type: application/json');
include('conexionL.php');

$fechaActual = date('Y-m-d');

$query = "SELECT t.IdTurno, 
                 CONCAT(m.PrimerNombre, ' ', m.PrimerApellido) AS NombreMedico,
                 CONCAT(t.PrimerNombrePac, ' ', 
                        IFNULL(CONCAT(t.SegundoNombrePac, ' '), ''), 
                        IFNULL(CONCAT(t.TercerNombrePac, ' '), ''),
                        t.PrimerApellidoPac, ' ', 
                        IFNULL(t.SegundoApellidoPac, '')) AS NombreCompletoPaciente,
                 t.NoDpi, t.Telefono, t.EstadoCita
          FROM Turnos t
          JOIN Medico m ON t.IdMedico = m.IdMedico
          WHERE t.FechaTurno = ?
          ORDER BY t.IdTurno DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $fechaActual);
$stmt->execute();
$result = $stmt->get_result();

$turnos = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['NombreCompletoPaciente'] = preg_replace('/\s+/', ' ', trim($row['NombreCompletoPaciente']));
        $turnos[] = $row;
    }
}

echo json_encode($turnos);
$stmt->close();
$conn->close();
?>