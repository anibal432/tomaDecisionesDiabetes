<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';


function enviarResultadoYActualizar($conn, $idPaciente, $idSolicitud, $rutaArchivo) {
    $correoDestino = null;

    $stmt = $conn->prepare("SELECT Email FROM ResponsablePaciente WHERE IdPaciente = ? LIMIT 1");
    $stmt->bind_param("i", $idPaciente);
    $stmt->execute();
    $stmt->bind_result($correo);
    if ($stmt->fetch()) {
        $correoDestino = $correo;
    }
    $stmt->close();

    if (!$correoDestino) {
        $stmt = $conn->prepare("SELECT NoDpi FROM Paciente WHERE IdPaciente = ?");
        $stmt->bind_param("i", $idPaciente);
        $stmt->execute();
        $stmt->bind_result($noDpi);
        if (!$stmt->fetch()) return;
        $stmt->close();

        $stmt = $conn->prepare("SELECT correo_electronico FROM citas WHERE NoDpi = ? ORDER BY fecha DESC, hora DESC LIMIT 1");
        $stmt->bind_param("s", $noDpi);
        $stmt->execute();
        $stmt->bind_result($correo);
        if ($stmt->fetch()) {
            $correoDestino = $correo;
        }
        $stmt->close();
    }

    if ($correoDestino) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'servidorumg677@gmail.com';
            $mail->Password = 'edbnxcguetbzwknl';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('servidorumg677@gmail.com', 'Diabetes Log');
            $mail->addAddress($correoDestino);
            $mail->Subject = 'Resultado de Examen';
            $mail->Body = 'Adjunto encontrará el resultado del examen solicitado.';
            $mail->addAttachment($rutaArchivo);

            $mail->send();
        } catch (Exception $e) {
        }
    }

    $stmt = $conn->prepare("UPDATE SolicitudExamenes SET Estado = 'Completado' WHERE IdSolicitud = ?");
    $stmt->bind_param("i", $idSolicitud);
    $stmt->execute();
    $stmt->close();
}

?>
