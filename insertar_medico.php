<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

function jsonResponse($success, $message = '', $data = []) {
    http_response_code($success ? 200 : 400);
    die(json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido');
}

include('conexionL.php');

if ($conn->connect_error) {
    jsonResponse(false, 'Error de conexión a la base de datos');
}

$primerNombre = trim($_POST['primerNombre'] ?? '');
$segundoNombre = isset($_POST['segundoNombre']) ? trim($_POST['segundoNombre']) : null;
$tercerNombre = isset($_POST['tercerNombre']) ? trim($_POST['tercerNombre']) : null;
$primerApellido = trim($_POST['primerApellido'] ?? '');
$segundoApellido = isset($_POST['segundoApellido']) ? trim($_POST['segundoApellido']) : null;
$correoMedico = trim($_POST['correoMedico'] ?? '');
$contraMedico = $_POST['contraMedico'] ?? '';
$noColegiado = trim($_POST['noColegiado'] ?? '');

$required_fields = ['primerNombre', 'primerApellido', 'correoMedico', 'contraMedico', 'noColegiado'];
foreach ($required_fields as $field) {
    if (empty($$field)) {
        jsonResponse(false, "El campo $field es obligatorio");
    }
}

if (!filter_var($correoMedico, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, 'Formato de correo electrónico inválido');
}

try {
    $check_query = "SELECT IdMedico FROM Medico WHERE CorreoMedico = ?";
    $check_stmt = $conn->prepare($check_query);
    
    if (!$check_stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $check_stmt->bind_param("s", $correoMedico);
    
    if (!$check_stmt->execute()) {
        throw new Exception("Error al verificar correo: " . $check_stmt->error);
    }
    
    if ($check_stmt->get_result()->num_rows > 0) {
        jsonResponse(false, 'El correo electrónico ya está registrado');
    }

    $query = "CALL InsertarMedico(?, ?, ?, ?, ?, ?, ?, NULL, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error al preparar el procedimiento: " . $conn->error);
    }
    
    $stmt->bind_param("ssssssss", 
        $primerNombre,
        $segundoNombre,
        $tercerNombre,
        $primerApellido,
        $segundoApellido,
        $correoMedico,
        $contraMedico,
        $noColegiado
    );
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Médico creado exitosamente', [
            'id' => $conn->insert_id
        ]);
    } else {
        throw new Exception("Error al ejecutar el procedimiento: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Error en insertar_medico.php: " . $e->getMessage());
    jsonResponse(false, "Error del servidor: " . $e->getMessage());
} finally {
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>