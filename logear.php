<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('conexionL.php');

    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $sql_verificar_desactivado = "SELECT d.IdMedico, d.IdSecre 
                                 FROM Desactivado d
                                 LEFT JOIN Medico m ON d.IdMedico = m.IdMedico AND m.CorreoMedico = ?
                                 LEFT JOIN Secretarias s ON d.IdSecre = s.IdSecre AND s.CorreoSecre = ?
                                 WHERE m.CorreoMedico IS NOT NULL OR s.CorreoSecre IS NOT NULL";
    
    $stmt_verificar_desactivado = $conn->prepare($sql_verificar_desactivado);
    $stmt_verificar_desactivado->bind_param("ss", $correo, $correo);
    $stmt_verificar_desactivado->execute();
    $stmt_verificar_desactivado->store_result();
    
    if ($stmt_verificar_desactivado->num_rows > 0) {
        $_SESSION['error'] = 'Usuario inactivo. Por favor verifique con el Jefe de su área.';
        header("Location: index.php");
        exit();
    }
    $stmt_verificar_desactivado->close();

    $sql_verificar_login = "CALL VerificarLogin(?, ?)";
    $stmt_verificar_login = $conn->prepare($sql_verificar_login);
    
    if ($stmt_verificar_login === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt_verificar_login->bind_param("ss", $correo, $contrasena);

    if ($stmt_verificar_login->execute()) {
        $stmt_verificar_login->store_result();
        $stmt_verificar_login->bind_result($mensaje);
        $stmt_verificar_login->fetch();
        $stmt_verificar_login->free_result();
        $stmt_verificar_login->close();
    } else {
        die("Error en la ejecución del procedimiento: " . $conn->error);
    }

    if (strpos($mensaje, 'Acceso concedido:') !== false) {
        $codigo_verificacion = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $tipo_usuario = (strpos($mensaje, 'Medico') !== false) ? 'Medico' : 'Secretaria';
        $tabla = ($tipo_usuario == 'Medico') ? 'Medico' : 'Secretarias';
        $campo_correo = ($tipo_usuario == 'Medico') ? 'CorreoMedico' : 'CorreoSecre';
        
        $sql_guardar_codigo = "UPDATE $tabla SET CodigoContra = ? WHERE $campo_correo = ?";
        $stmt_guardar_codigo = $conn->prepare($sql_guardar_codigo);
        $stmt_guardar_codigo->bind_param("ss", $codigo_verificacion, $correo);
        $stmt_guardar_codigo->execute();
        $stmt_guardar_codigo->close();
        
        $mail = new PHPMailer(true);
        try {            
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'servidorumg677@gmail.com'; 
            $mail->Password   = 'edbnxcguetbzwknl';        
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('servidorumg677@gmail.com', 'Equipo Tecnico');
            $mail->addAddress($correo);

            $mail->isHTML(true);
            $mail->Subject = 'Codigo de Verificacion.';
            $mail->Body    = "Tu codigo de verificacion para iniciar sesion es: <strong>$codigo_verificacion</strong>";
            $mail->AltBody = "Tu codigo de verificacion para iniciar sesion es: $codigo_verificacion";

            $mail->send();
            
            $_SESSION['correo_verificacion'] = $correo;
            $_SESSION['tipo_usuario'] = $tipo_usuario;
            $_SESSION['mostrar_modal_verificacion'] = true;
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al enviar el código de verificación. Mailer Error: {$mail->ErrorInfo}";
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
    mysqli_close($conn);
}
?>