<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('conexionL.php');

    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

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
        $_SESSION['correo'] = $correo;
        
        if (strpos($mensaje, 'Medico') !== false) {
            header("Location: iniciomedico.php");
        } elseif (strpos($mensaje, 'Secretaria') !== false) {
            header("Location: iniciosecre.php");
        } else {
            $_SESSION['error'] = 'Error desconocido.';
            header("Location: index.php");
        }
    } else {
        $_SESSION['error'] = 'Usuario o Contraseña incorrectos.';
        header("Location: index.php");
    }
    
    exit;
}

mysqli_close($conn);
?>
