<?php
session_start();
include('conexionL.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['correo_verificacion']) || !isset($_SESSION['tipo_usuario'])) {
        $_SESSION['error'] = 'Sesión de verificación no encontrada. Por favor inicie sesión nuevamente.';
        header("Location: index.php");
        exit();
    }

    $correo = $_SESSION['correo_verificacion'];
    $tipo_usuario = $_SESSION['tipo_usuario'];
    $codigo_ingresado = $_POST['codigo'];

    $tabla = ($tipo_usuario == 'Medico') ? 'Medico' : 'Secretarias';
    $campo_correo = ($tipo_usuario == 'Medico') ? 'CorreoMedico' : 'CorreoSecre';
    $sql_verificar_codigo = "SELECT CodigoContra FROM $tabla WHERE $campo_correo = ?";
    $stmt_verificar_codigo = $conn->prepare($sql_verificar_codigo);
    $stmt_verificar_codigo->bind_param("s", $correo);
    $stmt_verificar_codigo->execute();
    $stmt_verificar_codigo->bind_result($codigo_bd);
    $stmt_verificar_codigo->fetch();
    $stmt_verificar_codigo->close();

    if ($codigo_bd == $codigo_ingresado) {
        $sql_limpiar_codigo = "UPDATE $tabla SET CodigoContra = NULL WHERE $campo_correo = ?";
        $stmt_limpiar_codigo = $conn->prepare($sql_limpiar_codigo);
        $stmt_limpiar_codigo->bind_param("s", $correo);
        $stmt_limpiar_codigo->execute();
        $stmt_limpiar_codigo->close();
        $_SESSION['correo'] = $correo;
        unset($_SESSION['correo_verificacion']);
        unset($_SESSION['tipo_usuario']);

        if ($tipo_usuario == 'Medico') {
            header("Location: iniciomedico.php");
        } else {
            header("Location: iniciosecre.php");
        }
    } else {
        $_SESSION['error'] = 'Código de verificación incorrecto.';
        $_SESSION['mostrar_modal_verificacion'] = true;
        header("Location: index.php");
    }

    exit();
}

header("Location: index.php");
?>