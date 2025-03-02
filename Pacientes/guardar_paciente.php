<?php
include '../conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos del paciente
    $tipoDiabetes = $_POST['producto'][0]; // Suponiendo que el primer elemento es el tipo de diabetes
    $nombreUno = $_POST['nombre'];
    $nombreDos = $_POST['nombredos'];
    $nombreTres = $_POST['nombretres'];
    $primerApellido = $_POST['apellido'];
    $segundoApellido = $_POST['apellidodos'];
    $dpi = $_POST['dpi'];
    $telefono = $_POST['telefono'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $sexo = $_POST['sexo']; // Asegúrate de que el campo tenga el nombre correcto
    $grupoEtnico = $_POST['grupoEtnico']; // Asegúrate de que el campo tenga el nombre correcto
    $idResponsable = NULL; // Aquí puedes gestionar la inserción del responsable si es necesario

    // Insertar datos en la tabla Paciente
    $sqlPaciente = "INSERT INTO Paciente (IdDiabetes, NombreUno, NombreDos, NombreTres, PrimerApellido, SegundoApellido, NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlPaciente);
    $stmt->bind_param("issssssssss", $tipoDiabetes, $nombreUno, $nombreDos, $nombreTres, $primerApellido, $segundoApellido, $dpi, $telefono, $fechaNacimiento, $sexo, $grupoEtnico);
    
    if ($stmt->execute()) {
        // Obtener el IdPaciente generado
        $idPaciente = $conn->insert_id;

        // Aquí puedes agregar lógica para manejar la inserción de datos en otras tablas
        // Por ejemplo, si se agregan antecedentes personales
        if (isset($_POST['antecedentesPersonales'])) {
            // Supón que tienes un campo para antecedentes personales
            $medicos = $_POST['antecedentesPersonales']['medicos'];
            $quirurgicos = $_POST['antecedentesPersonales']['quirurgicos'];
            // Y así sucesivamente...

            $sqlAntecedentes = "INSERT INTO AntecedentesPersonales (IdPaciente, Medicos, Quirurgicos) VALUES (?, ?, ?)";
            $stmtAntecedentes = $conn->prepare($sqlAntecedentes);
            $stmtAntecedentes->bind_param("iss", $idPaciente, $medicos, $quirurgicos);
            $stmtAntecedentes->execute();
        }

        // Repite esto para otras tablas como HistoriaClinica, SignosVitales, etc.

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Paciente guardado exitosamente!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el paciente.']);
        }
    }

    $stmt->close();
}

$conn->close();
?>