<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

function sendJsonResponse($success, $message = '', $data = []) {
    http_response_code($success ? 200 : ($message == 'Secretaria no encontrada' ? 404 : 500));
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'secretaria' => $data
    ]);
    exit;
}

try {
    if (!isset($_GET['id'])) {
        sendJsonResponse(false, 'ID no proporcionado');
    }

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0) {
        sendJsonResponse(false, 'ID inválido');
    }
    $query = "SELECT IdSecre, PrimerNombre, SegundoNombre, TercerNombre, 
              PrimerApellido, SegundoApellido, CorreoSecre
              FROM Secretarias WHERE IdSecre = ?";
    
    if (!($stmt = $conn->prepare($query))) {
        throw new Exception("Error en preparación: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error en ejecución: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $secretaria = $result->fetch_assoc();
        $secretaria = array_map(function($value) {
            return is_string($value) ? mb_convert_encoding($value, 'UTF-8') : $value;
        }, $secretaria);
        sendJsonResponse(true, '', $secretaria);
    } else {
        sendJsonResponse(false, 'Secretaria no encontrada');
    }
} catch (Exception $e) {
    sendJsonResponse(false, 'Error del servidor: ' . $e->getMessage());
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>