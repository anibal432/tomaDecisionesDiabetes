<?php
include '../conexionDiabetes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPaciente = $_POST['id_paciente'];
    $primerNombre = $_POST['primer_nombre'];
    $segundoNombre = $_POST['segundo_nombre'];
    $tercerNombre = $_POST['tercer_nombre'];
    $primerApellido = $_POST['primer_apellido'];
    $segundoApellido = $_POST['segundo_apellido'];
    $dpi = $_POST['dpi'];
    $telefono = $_POST['telefono'];
    $fechaNacimiento = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];
    $grupoEtnico = $_POST['grupo_etnico'];
    $tipoDiabetes = $_POST['tipo_diabetes'];

    $sql = "UPDATE Paciente SET 
            NombreUno = ?, 
            NombreDos = ?, 
            NombreTres = ?, 
            PrimerApellido = ?, 
            SegundoApellido = ?, 
            NoDpi = ?, 
            Telefono = ?, 
            FechaNacimiento = ?, 
            Sexo = ?, 
            GrupoEtnico = ?, 
            IdDiabetes = ? 
            WHERE IdPaciente = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssii", 
        $primerNombre, 
        $segundoNombre, 
        $tercerNombre, 
        $primerApellido, 
        $segundoApellido, 
        $dpi, 
        $telefono, 
        $fechaNacimiento, 
        $sexo, 
        $grupoEtnico, 
        $tipoDiabetes, 
        $idPaciente);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}
?>