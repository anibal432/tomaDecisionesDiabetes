<?php
session_start();
include('conexionL.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? '';
    
    if (!empty($correo) && isset($_SESSION['tipo_usuario'])) {
        $tipo_usuario = $_SESSION['tipo_usuario'];
        $tabla = ($tipo_usuario == 'Medico') ? 'Medico' : 'Secretarias';
        $campo_correo = ($tipo_usuario == 'Medico') ? 'CorreoMedico' : 'CorreoSecre';
        $sql = "UPDATE $tabla SET CodigoContra = NULL WHERE $campo_correo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->close();
    }
    
    unset($_SESSION['correo_verificacion']);
    unset($_SESSION['tipo_usuario']);
    unset($_SESSION['mostrar_modal_verificacion']);
}

echo "OK";
?>