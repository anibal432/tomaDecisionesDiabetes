<?php
header('Content-Type: application/json');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('conexionL.php');
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data === null) {
        throw new Exception('Datos de la cita no válidos');
    }

    $checkDpiQuery = "SELECT COUNT(*) as count FROM citas WHERE NoDpi = ?";
    $stmtDpi = $conn->prepare($checkDpiQuery);
    $stmtDpi->bind_param("s", $data['NoDpi']);
    $stmtDpi->execute();
    $resultDpi = $stmtDpi->get_result();
    $rowDpi = $resultDpi->fetch_assoc();

    if ($rowDpi['count'] > 0) {
        throw new Exception('El número de DPI ya está registrado en el sistema');
    }

    $checkQuery = "SELECT COUNT(*) as count FROM citas 
                  WHERE IdMedico = ? AND fecha = ? AND hora = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("iss", $data['IdMedico'], $data['fecha'], $data['hora']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        throw new Exception('Ya existe una cita para este médico en la fecha y hora seleccionada');
    }

    $checkPacienteQuery = "SELECT COUNT(*) as count FROM citas 
                          WHERE fecha = ? 
                          AND (correo_electronico = ? OR NoDpi = ?)";
    $stmtPaciente = $conn->prepare($checkPacienteQuery);
    $stmtPaciente->bind_param("sss", 
        $data['fecha'],
        $data['correoElectronico'],
        $data['NoDpi']
    );
    $stmtPaciente->execute();
    $resultPaciente = $stmtPaciente->get_result();
    $rowPaciente = $resultPaciente->fetch_assoc();

    if ($rowPaciente['count'] > 0) {
        throw new Exception('Ya tiene una cita agendada para este día');
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'servidorumg677@gmail.com';
    $mail->Password = 'edbnxcguetbzwknl';
    $mail->Port = 587;
    
    $mail->setFrom('servidorumg677@gmail.com', 'Clinica Diabetes Log');
    $mail->addAddress($data['correoElectronico'], $data['primerNombre'].' '.$data['primerApellido']);
    $mail->isHTML(true);
    $mail->Subject = 'Confirmacion de cita medica';
    
    $mail->Body = "<h2>Confirmación de Cita Médica</h2>
                  <p>Estimado(a) {$data['primerNombre']} {$data['primerApellido']},</p>
                  <p>Su cita ha sido programada con éxito:</p>
                  <ul>
                    <li><strong>Fecha:</strong> {$data['fecha']}</li>
                    <li><strong>Hora:</strong> {$data['hora']}</li>
                  </ul>
                  <p>Por favor llegue 15 minutos antes de su cita.</p>
                  <p>Si necesita cancelar o reprogramar, contáctenos con 24 horas de anticipación.</p>
                  <p>Atentamente,</p>
                  <p><strong>Equipo de Diabetes Log</strong></p>";

    // Insertar la cita en la base de datos
    $query = "INSERT INTO citas (
                primer_nombre, segundo_nombre, tercer_nombre, 
                primer_apellido, segundo_apellido, correo_electronico, 
                NoDpi, numero_celular, FechaNacimiento, Sexo, GrupoEtnico,
                fecha, hora, estado, IdMedico
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssssssssi", 
        $data['primerNombre'],
        $data['segundoNombre'],
        $data['tercerNombre'],
        $data['primerApellido'],
        $data['segundoApellido'],
        $data['correoElectronico'],
        $data['NoDpi'],
        $data['numeroCelular'],
        $data['FechaNacimiento'],
        $data['Sexo'],
        $data['GrupoEtnico'],
        $data['fecha'],
        $data['hora'],
        $data['estado'],
        $data['IdMedico']
    );

    if (!$stmt->execute()) {
        throw new Exception('Error al guardar la cita: '.$conn->error);
    }

    $correoEnviado = false;
    try {
        $correoEnviado = $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: ".$e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => $correoEnviado 
            ? 'Cita agendada correctamente. Se ha enviado un correo de confirmación.'
            : 'Cita agendada correctamente, pero hubo un error al enviar el correo de confirmación.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>