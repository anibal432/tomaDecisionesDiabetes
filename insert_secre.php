<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);
function jsonResponse($success, $message = '', $data = []) {
    http_response_code($success ? 200 : 400);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido');
}

include('conexionL.php');

$required_fields = ['primerNombre', 'primerApellido', 'correoSecre', 'contraSecre'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        jsonResponse(false, "El campo $field es requerido");
    }
}

try {
    $check_query = "SELECT IdSecre FROM Secretarias WHERE CorreoSecre = ?";
    $check_stmt = $conn->prepare($check_query);
    
    if (!$check_stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $check_stmt->bind_param("s", $_POST['correoSecre']);
    
    if (!$check_stmt->execute()) {
        throw new Exception("Error al verificar correo: " . $check_stmt->error);
    }
    
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        jsonResponse(false, 'El correo electrónico ya está registrado');
    }
    
    $query = "CALL InsertarSecretaria(?, ?, ?, ?, ?, ?, ?, NULL)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error al preparar el procedimiento: " . $conn->error);
    }
    
    $primerNombre = trim($_POST['primerNombre']);
    $segundoNombre = isset($_POST['segundoNombre']) ? trim($_POST['segundoNombre']) : '';
    $tercerNombre = isset($_POST['tercerNombre']) ? trim($_POST['tercerNombre']) : '';
    $primerApellido = trim($_POST['primerApellido']);
    $segundoApellido = isset($_POST['segundoApellido']) ? trim($_POST['segundoApellido']) : '';
    $correoSecre = filter_var($_POST['correoSecre'], FILTER_SANITIZE_EMAIL);
    $contraSecre = password_hash($_POST['contraSecre'], PASSWORD_DEFAULT);
    
    $stmt->bind_param("sssssss", 
        $primerNombre,
        $segundoNombre,
        $tercerNombre,
        $primerApellido,
        $segundoApellido,
        $correoSecre,
        $contraSecre
    );
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Secretaria creada correctamente', [
            'id' => $conn->insert_id
        ]);
    } else {
        throw new Exception("Error al ejecutar el procedimiento: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Error en insert_secre.php: " . $e->getMessage());
    jsonResponse(false, "Error del servidor: " . $e->getMessage());
} finally {
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>