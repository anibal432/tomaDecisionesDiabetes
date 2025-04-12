<?php
session_start();
if (!isset($_SESSION['correo'])) {
    header("Location: index.php");
    exit();
}
include('conexionL.php');

$nombreMedico = 'Invitado';
$esJefeMedico = false;
$citasHoy = 0;
$citasProximas = 0;
$totalPacientes = 0; 
$idMedico = null;

if (!empty($_SESSION['correo'])) {
    $stmt = $conn->prepare("SELECT IdMedico, PrimerNombre FROM Medico WHERE CorreoMedico = ?");
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $stmt->bind_result($idMedico, $nombre);
    
    if ($stmt->fetch()) {
        $nombreMedico = $nombre;
        
        $stmt->free_result();
        $stmt->close();
        
        $stmt_jefe = $conn->prepare("SELECT IdJefeM FROM JefeMed WHERE IdMedico = ?");
        $stmt_jefe->bind_param("i", $idMedico);
        $stmt_jefe->execute();
        $stmt_jefe->store_result();
        
        if ($stmt_jefe->num_rows > 0) {
            $esJefeMedico = true;
        }
        $stmt_jefe->close();
        
        $hoy = date('Y-m-d');
        $queryHoy = "SELECT COUNT(*) FROM citas WHERE fecha = ? AND IdMedico = ?";
        $stmtHoy = $conn->prepare($queryHoy);
        $stmtHoy->bind_param("si", $hoy, $idMedico);
        $stmtHoy->execute();
        $stmtHoy->bind_result($citasHoy);
        $stmtHoy->fetch();
        $stmtHoy->close();
        
        $queryProximas = "SELECT COUNT(*) FROM citas WHERE fecha > ? AND IdMedico = ?";
        $stmtProximas = $conn->prepare($queryProximas);
        $stmtProximas->bind_param("si", $hoy, $idMedico);
        $stmtProximas->execute();
        $stmtProximas->bind_result($citasProximas);
        $stmtProximas->fetch();
        $stmtProximas->close();
        
        $queryPacientes = "SELECT COUNT(*) FROM Turnos WHERE IdMedico = ?";
        $stmtPacientes = $conn->prepare($queryPacientes);
        $stmtPacientes->bind_param("i", $idMedico);
        $stmtPacientes->execute();
        $stmtPacientes->bind_result($totalPacientes);
        $stmtPacientes->fetch();
        $stmtPacientes->close();
    } else {
        $stmt->close();
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Diabetes Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/medico.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
    <div class="logo">Diabetes Log</div>
    <ul>            
        <li><a href="../iniciomedico.php" class="active"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="../Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> <span>Ing. Paciente</span></a></li>
        <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
        <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Datos del Paciente</span></a></li>
        <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
        <?php if ($esJefeMedico): ?>
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
        <?php endif; ?>
        <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>
    
<div class="main-content">
    <div class="welcome-card compact">
        <div class="greeting-container">
        <h1><i class="fas fa-stethoscope"></i> <span id="greeting-text"></span>mi amor, Dr. <?php echo htmlspecialchars($nombreMedico); ?></h1>
            <div id="live-clock" class="clock"></div>
        </div>
    </div>

    <div class="cards-container compact">
    <?php if ($citasHoy > 0): ?>
        <div class="med-card today compact">
            <i class="fas fa-calendar-day"></i>
            <h3>Citas Hoy</h3>
            <p class="count"><?php echo $citasHoy; ?></p>
            <a href="../Consultas/AsignarTurno.php" class="med-link">Ver</a>
        </div>
    <?php else: ?>
        <div class="med-card today compact" onclick="mostrarModalPacientes()">
            <i class="fas fa-users"></i>
            <h3>Pacientes</h3>
            <p class="count"><?php echo $totalPacientes; ?></p>
            <a href="#" class="med-link">Ver</a>
        </div>
    <?php endif; ?>
    
    <div class="med-card upcoming compact">
        <i class="fas fa-calendar-week"></i>
        <h3>Próximas</h3>
        <p class="count"><?php echo $citasProximas; ?></p>
    </div>
</div>

    <div class="chart-container">
    <h2><i class="fas fa-chart-bar"></i> Distribución de Pacientes por Tipo de Diabetes</h2>
    <canvas id="diabetesChart"></canvas>
    <p class="chart-note">Total de pacientes: <span id="total-pacientes">0</span></p>
</div>
<div id="modalPacientes" class="modal-pacientes">
    <div class="modal-content-pacientes">
        <span class="close-modal-pacientes" onclick="cerrarModalPacientes()">&times;</span>
        <h2><i class="fas fa-users"></i> Gestión de Pacientes</h2>
        
        <div class="tab-section-pacientes">
            <h3><i class="fas fa-user-clock"></i> Pacientes en Proceso</h3>
            <div class="table-container-pacientes">
                <table class="table-pacientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProcesoBody">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="tab-section-pacientes">
            <h3><i class="fas fa-list"></i> Pacientes Pendientes</h3>
            <div class="table-container-pacientes">
                <table class="table-pacientes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaPendientesBody">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>


<script>
function updateGreetingAndClock() {
    const now = new Date();
    const hour = now.getHours();
    
    let greeting;
    if (hour < 12) greeting = "Buenos días";
    else if (hour < 19) greeting = "Buenas tardes";
    else greeting = "Buenas noches";
    const options = {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };
    const timeString = now.toLocaleTimeString('es-ES', options);

    document.getElementById('greeting-text').textContent = greeting;
    document.getElementById('live-clock').textContent = timeString;
}

setInterval(updateGreetingAndClock, 1000);
updateGreetingAndClock();
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('get_pacientes_data.php');
        const data = await response.json();
        
        document.getElementById('total-pacientes').textContent = data.total;
        
        const diabetesData = {
            labels: data.labels,
            datasets: [{
                label: 'Número de Pacientes',
                data: data.values,
                backgroundColor: [
                    '#3a7bd5', 
                    '#4CAF50',
                    '#FF9800',
                    '#9C27B0'  
                ],
                borderWidth: 0
            }]
        };

        const config = {
            type: 'bar',
            data: diabetesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'No. de Pacientes' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        title: { display: true, text: 'Tipo de Diabetes' }
                    }
                }
            }
        };

        new Chart(document.getElementById('diabetesChart'), config);
        
    } catch (error) {
        console.error('Error al cargar datos:', error);
        mostrarDatosEjemplo();
    }
});

function mostrarDatosEjemplo() {
    new Chart(document.getElementById('diabetesChart'), {
        type: 'bar',
        data: {
            labels: ['Tipo 1', 'Tipo 2', 'Gestacional'],
            datasets: [{
                label: 'Datos no disponibles',
                data: [0, 0, 0],
                backgroundColor: ['#3a7bd5', '#4CAF50', '#FF9800']
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });
}
</script>

<script>
function mostrarModalPacientes() {
    document.getElementById('modalPacientes').style.display = 'block';
    cargarPacientes();
}

function cerrarModalPacientes() {
    Swal.fire({
        title: '¿Cerrar ventana?',
        text: '¿Estás seguro de que quieres cerrar la gestión de pacientes?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3a7bd5',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('modalPacientes').style.display = 'none';
        }
    });
}

window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('modalPacientes')) {
        cerrarModalPacientes();
    }
});

async function cargarPacientes() {
    try {
        const response = await fetch(`obtener_pacientes.php?idMedico=<?php echo $idMedico; ?>`);
        
        if (!response.ok) {
            throw new Error('Error al obtener los datos');
        }
        
        const pacientes = await response.json();
        
        const tablaProceso = document.getElementById('tablaProcesoBody');
        const tablaPendientes = document.getElementById('tablaPendientesBody');
        
        tablaProceso.innerHTML = '';
        tablaPendientes.innerHTML = '';
        
        pacientes.forEach(paciente => {
            const nombreCompleto = `${paciente.PrimerNombrePac} ${paciente.SegundoNombrePac || ''} ${paciente.PrimerApellidoPac} ${paciente.SegundoApellidoPac || ''}`;
            
            if (paciente.EstadoCita === 'Adelante' || paciente.EstadoCita === 'Atendiendo') {
                tablaProceso.innerHTML += `
                    <tr>
                        <td>${paciente.IdTurno}</td>
                        <td>${nombreCompleto}</td>
                        <td><span class="estado-badge-pacientes ${paciente.EstadoCita.toLowerCase()}">${paciente.EstadoCita}</span></td>
                        <td>
                            <select class="estado-select-pacientes" data-id="${paciente.IdTurno}">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Adelante" ${paciente.EstadoCita === 'Adelante' ? 'selected' : ''}>Adelante</option>
                                <option value="Atendiendo" ${paciente.EstadoCita === 'Atendiendo' ? 'selected' : ''}>Atendiendo</option>
                                <option value="Atendido">Atendido</option>
                            </select>
                            <button class="btn-actualizar-pacientes" data-id="${paciente.IdTurno}">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </td>
                    </tr>
                `;
            }
            
            if (paciente.EstadoCita === 'Pendiente') {
                tablaPendientes.innerHTML += `
                    <tr>
                        <td>${paciente.IdTurno}</td>
                        <td>${paciente.PrimerNombrePac}</td>
                        <td>${paciente.PrimerApellidoPac}</td>
                        <td><span class="estado-badge-pacientes pendiente">${paciente.EstadoCita}</span></td>
                        <td>
                            <select class="estado-select-pacientes" data-id="${paciente.IdTurno}">
                                <option value="Pendiente" selected>Pendiente</option>
                                <option value="Adelante">Adelante</option>
                                <option value="Atendiendo">Atendiendo</option>
                                <option value="Atendido">Atendido</option>
                            </select>
                            <button class="btn-actualizar-pacientes" data-id="${paciente.IdTurno}">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </td>
                    </tr>
                `;
            }
        });
        
        document.querySelectorAll('.btn-actualizar-pacientes').forEach(btn => {
            btn.addEventListener('click', async function() {
                const idTurno = this.getAttribute('data-id');
                const select = document.querySelector(`.estado-select-pacientes[data-id="${idTurno}"]`);
                const nuevoEstado = select.value;
                
                try {
                    const response = await fetch('actualizar_estado.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            idTurno: idTurno,
                            estado: nuevoEstado
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            text: 'El estado se ha actualizado correctamente',
                            confirmButtonColor: '#3a7bd5',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarPacientes();
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message || 'Error al actualizar el estado',
                            confirmButtonColor: '#3a7bd5'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al actualizar el estado',
                        confirmButtonColor: '#3a7bd5'
                    });
                }
            });
        });
        
    } catch (error) {
        console.error('Error al cargar pacientes:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la lista de pacientes',
            confirmButtonColor: '#3a7bd5'
        });
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>