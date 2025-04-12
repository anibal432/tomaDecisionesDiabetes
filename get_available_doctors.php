<?php
include('conexionL.php');

$date = $_GET['date'];
$dayOfWeek = date('D', strtotime($date));
$dayAbbrMap = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb', 'Sun' => 'Dom'];
$dayAbbr = $dayAbbrMap[$dayOfWeek];

$disponibilidad = [];
if (file_exists('Disponible.json')) {
    $disponibilidad = json_decode(file_get_contents('Disponible.json'), true);
}

$availableDoctors = [];
foreach ($disponibilidad as $correo => $schedule) {
    if (isset($schedule[$dayAbbr]) && $schedule[$dayAbbr] === 'booked') {
        $query = "SELECT m.IdMedico, CONCAT(m.PrimerNombre, ' ', m.PrimerApellido) AS nombre_completo
                  FROM Medico m
                  LEFT JOIN (
                      SELECT IdMedico, COUNT(*) as citas_count 
                      FROM citas 
                      WHERE fecha = ?
                      GROUP BY IdMedico
                  ) c ON m.IdMedico = c.IdMedico
                  WHERE m.CorreoMedico = ? AND (c.citas_count IS NULL OR c.citas_count < 5)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $date, $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $availableDoctors[] = $result->fetch_assoc();
        }
    }
}

header('Content-Type: application/json');
echo json_encode($availableDoctors);
?>