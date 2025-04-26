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
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Gestion de Medicos</span></a></li>
            <li><a href="gestión_secretarias.php"><i class="fa-solid fa-id-card"></i> <span>Gestion de Secretaría</span></a></li>
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
            <a href="#" class="btn-crear">Crear Turno</a>
        </div>
        
        <div class="tabla-turnos">
            <table id="tablaTurnos">
                <thead>
                    <tr>
                        <th>ID Turno</th>
                        <th>Médico</th>
                        <th>Nombre Paciente</th>
                        <th>DPI</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="modalTurno" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2><i class="fas fa-plus-circle"></i> Crear Nuevo Turno</h2>
        <form id="formTurno">
            <div class="form-row">
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
                    <label for="segundoNombre">Segundo Nombre:</label>
                    <input type="text" id="segundoNombre" name="segundoNombre">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tercerNombre">Tercer Nombre:</label>
                    <input type="text" id="tercerNombre" name="tercerNombre">
                </div>
                
                <div class="form-group">
                    <label for="primerApellido">Primer Apellido:</label>
                    <input type="text" id="primerApellido" name="primerApellido" required>
                </div>
                
                <div class="form-group">
                    <label for="segundoApellido">Segundo Apellido:</label>
                    <input type="text" id="segundoApellido" name="segundoApellido">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="noDpi">Número de DPI:</label>
                    <input type="text" id="noDpi" name="noDpi" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                
                <div class="form-group">
                    <label for="fechaNacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sexo">Sexo:</label>
                    <select id="sexo" name="sexo" required>
                        <option value="">Seleccione</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Prefiero no decir">Prefiero no decir</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="grupoEtnico">Grupo Étnico:</label>
                    <select class="form-control" id="grupoEtnico" name="grupoEtnico" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Ladino">Ladino</option>
                                    <option value="Mestizo">Mestizo</option>
                                    <option value="Maya">Maya</option>
                                    <option value="Garifuna">Garifuna</option>
                                    <option value="Otro">Otro</option>
                                </select>
                </div>
            </div>
            
            <input type="hidden" name="estadoCita" value="Pendiente">
            
            <button type="submit" class="btn-submit">Crear Turno</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("modalTurno");
    const btnCrear = document.querySelector(".btn-crear");
    const span = document.getElementsByClassName("close-modal")[0];
    const formTurno = document.getElementById("formTurno");
    
    cargarTurnos();
    
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
    
    formTurno.addEventListener('submit', function(e) {
        e.preventDefault();
        crearTurno();
    });
    
    setInterval(cargarTurnos, 5000);
});

async function cargarMedicosDisponibles() {
    try {
        const dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        const hoy = new Date();
        const diaActual = dias[hoy.getDay()];
        console.log('Día actual:', diaActual);
        
        const disponibilidadResponse = await fetch('Disponible.json');
        if (!disponibilidadResponse.ok) {
            throw new Error('Error al cargar Disponible.json');
        }
        const disponibilidad = await disponibilidadResponse.json();
        console.log('Disponibilidad:', disponibilidad);
        
        const medicosResponse = await fetch('obtener_medicos.php');
        if (!medicosResponse.ok) {
            throw new Error('Error al cargar médicos');
        }
        const medicos = await medicosResponse.json();
        console.log('Médicos:', medicos);
        
        const medicosDisponibles = medicos.filter(medico => {
            if (!disponibilidad[medico.CorreoMedico]) {
                console.log('No se encontró disponibilidad para:', medico.CorreoMedico);
                return false;
            }
            const disponible = disponibilidad[medico.CorreoMedico][diaActual] === "available";
            console.log(`Médico ${medico.CorreoMedico} - ${diaActual}:`, disponible);
            return disponible;
        });
        
        console.log('Médicos disponibles:', medicosDisponibles);
        
        const select = document.getElementById('idMedico');
        select.innerHTML = '<option value="">Seleccione un médico</option>';
        
        if (medicosDisponibles.length === 0) {
            console.warn('No hay médicos disponibles hoy');
            select.innerHTML += '<option value="" disabled>No hay médicos disponibles hoy</option>';
        } else {
            medicosDisponibles.forEach(medico => {
                const option = document.createElement('option');
                option.value = medico.IdMedico;
                option.textContent = `${medico.PrimerNombre} ${medico.PrimerApellido}`;
                select.appendChild(option);
            });
        }
        
    } catch (error) {
        console.error('Error al cargar médicos:', error);
        const select = document.getElementById('idMedico');
        select.innerHTML = '<option value="">Error al cargar médicos</option>';
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar médicos disponibles: ' + error.message,
            confirmButtonColor: '#3a7bd5'
        });
    }
}

async function cargarTurnos() {
    try {
        const response = await fetch('obtener_turnos.php');
        const turnos = await response.json();
        const tbody = document.querySelector("#tablaTurnos tbody");
        tbody.innerHTML = '';
        
        if (turnos.length > 0) {
            turnos.forEach(turno => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${turno.IdTurno}</td>
                    <td>${turno.NombreMedico}</td>
                    <td>${turno.NombreCompletoPaciente}</td>
                    <td>${turno.NoDpi}</td>
                    <td>${turno.Telefono}</td>
                    <td>${turno.EstadoCita}</td>
                    <td>
                        <button class='btn-eliminar' data-id='${turno.IdTurno}'>
                            <i class='fas fa-trash-alt'></i> Eliminar
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', function() {
                    const idTurno = this.getAttribute('data-id');
                    Swal.fire({
                        title: '¿Eliminar turno?',
                        text: "¿Está seguro que desea eliminar este turno?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3a7bd5',
                        cancelButtonColor: '#ff6b6b',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            eliminarTurno(idTurno);
                        }
                    });
                });
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7">No hay turnos registrados para hoy</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar turnos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar los turnos',
            confirmButtonColor: '#3a7bd5'
        });
    }
}

async function crearTurno() {
    try {
        const formData = new FormData(document.getElementById("formTurno"));
        
        const response = await fetch('crear_turno.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: result.message,
                confirmButtonColor: '#3a7bd5'
            }).then(() => {
                document.getElementById("modalTurno").style.display = "none";
                document.getElementById("formTurno").reset();
                cargarTurnos();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message,
                confirmButtonColor: '#3a7bd5'
            });
        }
    } catch (error) {
        console.error('Error al crear turno:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al crear el turno',
            confirmButtonColor: '#3a7bd5'
        });
    }
}

async function eliminarTurno(idTurno) {
    try {
        const response = await fetch(`eliminar_turno.php?id=${idTurno}`);
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: result.message,
                confirmButtonColor: '#3a7bd5'
            }).then(() => {
                cargarTurnos();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message,
                confirmButtonColor: '#3a7bd5'
            });
        }
    } catch (error) {
        console.error('Error al eliminar turno:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al eliminar el turno',
            confirmButtonColor: '#3a7bd5'
        });
    }
}
</script>



</body>
</html>
