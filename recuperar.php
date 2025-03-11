<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    require_once "conexionL.php";
    if ($conn) {
        $tablas = [
            ['tabla' => 'Medico', 'campo_correo' => 'CorreoMedico'],
            ['tabla' => 'Secretarias', 'campo_correo' => 'CorreoSecre']
        ];
        $correoEncontrado = false;
        
        foreach ($tablas as $tabla) {
            $query = "SELECT * FROM {$tabla['tabla']} WHERE {$tabla['campo_correo']} = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $correo);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result !== false && mysqli_num_rows($result) > 0) {
                $correoEncontrado = true;
                $codigoRecuperacion = generateRandomCode(8);
                $query = "UPDATE {$tabla['tabla']} SET CodigoContra = ? WHERE {$tabla['campo_correo']} = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ss", $codigoRecuperacion, $correo);
                mysqli_stmt_execute($stmt);
                break;
            }
        }
        
        if ($correoEncontrado) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'servidorumg677@gmail.com';
                $mail->Password = 'edbnxcguetbzwknl';
                $mail->Port = 587;
                $mail->setFrom('servidorumg677@gmail.com', 'Equipo Tecnico');
                $mail->addAddress($correo, 'Destinatario'); 
                $mail->isHTML(true);
                $mail->Subject = 'Cambio de contrasena';
                $mail->Body = "Tu código para el cambio de contrasena es: $codigoRecuperacion";      
                $mail->send();

                $_SESSION['correo_ingresado'] = $correo;
                header("Location: ingreseelcodigo.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error desconocido: ' . $mail->ErrorInfo;
                header("Location: CambioContra.php");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Correo no encontrado en ninguna tabla.';
            header("Location: CambioContra.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'Problemas de conexión con la base de datos.';
        header("Location: CambioContra.php");
        exit();
    }
}

function generateRandomCode($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $randomIndex = mt_rand(0, strlen($characters) - 1);
        $code .= $characters[$randomIndex];
    }
    return $code;
}
?>