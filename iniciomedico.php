<?php
session_start();
include('conexionL.php');

$nombreMedico = 'Invitado';

if (!empty($_SESSION['correo'])) {
    $stmt = $conn->prepare("SELECT PrimerNombre FROM Medico WHERE CorreoMedico = ?");
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $stmt->bind_result($nombre);
    if ($stmt->fetch()) {
        $nombreMedico = $nombre;
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Medicos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
    <nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
        <div class="logo">Diabetes Log</div>
        <ul>            
            <li><a href="../iniciomedico.php" class="active"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="../tomaDecisionesDiabetes/Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> <span>Ing. Paciente</span></a></li>
            <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
            <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Datos del Paciente</span></a></li>
            <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
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
        <div class="med-card today compact">
            <i class="fas fa-calendar-day"></i>
            <h3>Citas Hoy</h3>
            <p class="count">--.--</p>
            <a href="../Consultas/AsignarTurno.php" class="med-link">Ver</a>
        </div>
        
        <div class="med-card upcoming compact">
            <i class="fas fa-calendar-week"></i>
            <h3>Próximas</h3>
            <p class="count">--.--</p>
            <a href="../Consultas/AsignarTurno.php" class="med-link">Ver</a>
        </div>
    </div>

    <div class="chart-container">
    <h2><i class="fas fa-chart-bar"></i> Distribución de Pacientes por Tipo de Diabetes</h2>
    <canvas id="diabetesChart"></canvas>
    <p class="chart-note">Total de pacientes: <span id="total-pacientes">0</span></p>
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

</body>
</html>