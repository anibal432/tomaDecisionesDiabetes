<?php
session_start();

if (!isset($_SESSION['correo_usuario'])) {
    header("Location: CambioContra.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('conexionL.php'); 

    $correo_user = $_SESSION['correo_usuario'];
    $codigo = $_POST['codigo'];
    $nueva_contrasena = $_POST['contrasena'];
    
    $sql = "CALL actualizar_contrasena(?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $correo_user, $codigo, $nueva_contrasena);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $mensaje);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        $_SESSION['mensaje'] = $mensaje;
        
        if ($mensaje === 'Contraseña Actualizada Correctamente') {
            $_SESSION['tipo_mensaje'] = 'success'; // Mensaje de éxito
            header("Location: index.php");
        } else {
            $_SESSION['tipo_mensaje'] = 'error'; // Mensaje de error
            header("Location: ingreseelcodigo.php");
        }
    }
    
    exit();
} else {
    header("Location: error.php");
    exit();
}
?>


