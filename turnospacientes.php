<?php
session_start();
include('conexionL.php');

$esJefeSecretaria = false;

if (!empty($_SESSION['correo'])) {
    $query = "SELECT COUNT(js.IdSecre) > 0 AS es_jefe
              FROM Secretarias s
              LEFT JOIN JefeSec js ON js.IdSecre = s.IdSecre
              WHERE s.CorreoSecre = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $stmt->bind_result($esJefeSecretaria);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos | Admin log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/turno.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
    <div class="logo">Admin Log</div>
    <ul>            
        <li><a href="iniciosecre.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <?php if ($esJefeSecretaria): ?>
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
            <li><a href="gestión_secretarias.php"><i class="fa-solid fa-id-card"></i> <span>Ingresar Secre</span></a></li>
        <?php endif; ?>
        <li><a href="Citas.php"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
        <li><a href="turnospacientes.php" class="active"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
        <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>

<main class="main-content">
    <div class="turnos-container">
        <div class="turnos-header">
            <h1><i class="fas fa-ticket-alt"></i> Gestión de Turnos</h1>
            <a href="crear_turno.php" class="btn-crear">Crear Turno</a>
        </div>
        
        <div class="tabla-turnos">
            <table>
                <thead>
                    <tr>
                        <th>ID Médico</th>
                        <th>Nombre Paciente</th>
                        <th>Apellido Paciente</th>
                        <th>Estado Cita</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include('conexionL.php');
                    $query = "SELECT IdTurno, IdMedico, PrimerNombrePac, PrimerApellidoPac, EstadoCita FROM Turnos";
                    $result = $conn->query($query);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>".$row["IdMedico"]."</td>
                                <td>".$row["PrimerNombrePac"]."</td>
                                <td>".$row["PrimerApellidoPac"]."</td>
                                <td>".$row["EstadoCita"]."</td>
                                <td>
                                    <button class='btn-eliminar' data-id='".$row["IdTurno"]."'>
                                        <i class='fas fa-trash-alt'></i> Eliminar
                                    </button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay turnos registrados</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="modalTurno" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2><i class="fas fa-plus-circle"></i> Crear Nuevo Turno</h2>
        <form id="formTurno" action="crear_turno.php" method="POST">
            <div class="form-group">
                <label for="idMedico">Médico:</label>
                <select id="idMedico" name="idMedico" required>
                    <option value="">Seleccione un médico</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="primerNombre">Primer Nombre:</label>
                <input type="text" id="primerNombre" name="primerNombre" required>
            </div>
            
            <div class="form-group">
                <label for="segundoNombre">Segundo Nombre (opcional):</label>
                <input type="text" id="segundoNombre" name="segundoNombre">
            </div>
            
            <div class="form-group">
                <label for="primerApellido">Primer Apellido:</label>
                <input type="text" id="primerApellido" name="primerApellido" required>
            </div>
            
            <div class="form-group">
                <label for="segundoApellido">Segundo Apellido (opcional):</label>
                <input type="text" id="segundoApellido" name="segundoApellido">
            </div>
            
            <input type="hidden" name="estadoCita" value="Pendiente">
            
            <button type="submit" class="btn-submit">Crear Turno</button>
        </form>
    </div>
</div>

<script>
    function recargarPagina() {
    setTimeout(function() {
        location.reload(true); 
    }, 10000);
}
    document.addEventListener('DOMContentLoaded', function() {
        recargarPagina();
        const modal = document.getElementById("modalTurno");
        const btnCrear = document.querySelector(".btn-crear");
        const span = document.getElementsByClassName("close-modal")[0];
        
        btnCrear.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = "block";
            cargarMedicosDisponibles();
        });
        
        span.addEventListener('click', function() {
            modal.style.display = "none";
        });
        
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    });
    
    async function cargarMedicosDisponibles() {
        try {
            const dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            const hoy = new Date();
            const diaActual = dias[hoy.getDay()];
            
            const response = await fetch('Disponible.json');
            const disponibilidad = await response.json();
            
            const medicosResponse = await fetch('obtener_medicos.php');
            const medicos = await medicosResponse.json();
            
            const medicosDisponibles = medicos.filter(medico => {
                const disponibilidadMedico = disponibilidad[medico.CorreoMedico];
                return disponibilidadMedico && disponibilidadMedico[diaActual] === "available";
            });
            
            const select = document.getElementById('idMedico');
            select.innerHTML = '<option value="">Seleccione un médico</option>';
            
            medicosDisponibles.forEach(medico => {
                const option = document.createElement('option');
                option.value = medico.IdMedico;
                option.textContent = `${medico.PrimerNombre} ${medico.PrimerApellido}`;
                select.appendChild(option);
            });
            
        } catch (error) {
            console.error('Error al cargar médicos:', error);
            alert('Error al cargar médicos disponibles');
        }
    }
</script>

<script>
    document.querySelectorAll('.btn-eliminar').forEach(button => {
        button.addEventListener('click', function() {
            const idTurno = this.getAttribute('data-id');
            if (confirm('¿Está seguro que desea eliminar este turno?')) {
                window.location.href = 'eliminar_turno.php?id=' + idTurno;
            }
        });
    });
</script>


</body>
</html>
