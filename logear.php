<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'PHPMailer-master/src/Exception.php';
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('conexionL.php');
    
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'];
    
    if (!$correo || !$contrasena) {
        $_SESSION['error'] = 'Datos de entrada inválidos';
        header("Location: index.php");
        exit();
    }
    $sql = "SELECT 
                d.IdMedico, 
                d.IdSecre,
                CASE 
                    WHEN m.IdMedico IS NOT NULL THEN 'Medico'
                    WHEN s.IdSecre IS NOT NULL THEN 'Secretaria'
                    ELSE NULL
                END AS tipo_usuario,
                m.IdMedico AS id_usuario,
                s.IdSecre AS id_secre
            FROM Desactivado d
            LEFT JOIN Medico m ON d.IdMedico = m.IdMedico AND m.CorreoMedico = ? AND m.ContraMedico = ?
            LEFT JOIN Secretarias s ON d.IdSecre = s.IdSecre AND s.CorreoSecre = ? AND s.ContraSecre = ?
            WHERE (m.CorreoMedico IS NOT NULL OR s.CorreoSecre IS NOT NULL)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $correo, $contrasena, $correo, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Usuario inactivo. Por favor verifique con el Jefe de su área.';
        $stmt->close();
        header("Location: index.php");
        exit();
    }
    $stmt->close();

    $sql = "CALL VerificarLogin(?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $conn->error);
        $_SESSION['error'] = 'Error en el sistema. Por favor intente más tarde.';
        header("Location: index.php");
        exit();
    }
    
    $stmt->bind_param("ss", $correo, $contrasena);
    
    if (!$stmt->execute()) {
        error_log("Error en la ejecución del procedimiento: " . $conn->error);
        $_SESSION['error'] = 'Error en el sistema. Por favor intente más tarde.';
        header("Location: index.php");
        exit();
    }
    
    $stmt->bind_result($mensaje);
    $stmt->fetch();
    $stmt->close();

    if (strpos($mensaje, 'Acceso concedido:') !== false) {
        $codigo_verificacion = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $tipo_usuario = (strpos($mensaje, 'Medico') !== false) ? 'Medico' : 'Secretaria';
        
        $tabla = ($tipo_usuario == 'Medico') ? 'Medico' : 'Secretarias';
        $campo_id = ($tipo_usuario == 'Medico') ? 'IdMedico' : 'IdSecre';
        
        $sql = "UPDATE $tabla SET CodigoContra = ? WHERE $campo_id = (
                    SELECT $campo_id FROM (
                        SELECT $campo_id FROM $tabla 
                        WHERE " . ($tipo_usuario == 'Medico' ? 'CorreoMedico' : 'CorreoSecre') . " = ?
                    ) AS temp
                )";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $codigo_verificacion, $correo);
        $stmt->execute();
        $stmt->close();
        
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'servidorumg677@gmail.com';
            $mail->Password = 'edbnxcguetbzwknl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom('servidorumg677@gmail.com', 'Equipo Tecnico');
            $mail->addAddress($correo);
            $mail->Subject = 'Código de Verificación';
            
            $mail->isHTML(true);
            $mail->Body = sprintf(
                '<p>Tu código de verificación para iniciar sesión es: <strong>%s</strong></p>',
                $codigo_verificacion
            );
            $mail->AltBody = "Tu código de verificación para iniciar sesión es: $codigo_verificacion";
            
            session_write_close();
            $mail->send();
            session_start();
            
            $_SESSION['correo_verificacion'] = $correo;
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            $_SESSION['mostrar_modal_verificacion'] = true;
            
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $e->getMessage());
            $_SESSION['error'] = "Error al enviar el código de verificación. Por favor intente nuevamente.";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'Usuario o Contraseña incorrectos.';
        header("Location: index.php");
        exit();
    }
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>