<?php
session_start();
include('conexionL.php');

$nombreSecre = 'Invitado';
$esJefeSecretaria = false; 

if (!empty($_SESSION['correo'])) {
    $stmt = $conn->prepare("SELECT IdSecre, PrimerNombre FROM Secretarias WHERE CorreoSecre = ?");
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $stmt->bind_result($idSecre, $nombre);
    
    if ($stmt->fetch()) {
        $nombreSecre = $nombre;
        $stmt_jefe = $conn->prepare("SELECT IdJefeS FROM JefeSec WHERE IdSecre = ?");
        $stmt_jefe->bind_param("i", $idSecre);
        $stmt_jefe->execute();
        $stmt_jefe->store_result();
        
        if ($stmt_jefe->num_rows > 0) {
            $esJefeSecretaria = true;
        }
        $stmt_jefe->close();
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
    <title>Inicio | Admin Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
    <div class="logo">Admin Log</div>
    <ul>            
        <li><a href="../iniciosecre.php" class="active"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <?php if ($esJefeSecretaria): ?>
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
            <li><a href="gestión_secretarias.php"><i class="fa-solid fa-id-card"></i> <span>Ingresar Secre</span></a></li>
        <?php endif; ?>
        <li><a href="Citas.php"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
        <li><a href="turnospacientes.php"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
        <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>

    <div class="main-content">
    <div class="welcome-card compact">
        <div class="greeting-container">
        <h1><i class="fas fa-stethoscope"></i> <span id="greeting-text"></span>mi amor, Admn. <?php echo htmlspecialchars($nombreSecre); ?></h1>
            <div id="live-clock" class="clock"></div>
        </div>
    </div>

    <div class="cards-container compact">
        <div class="med-card today compact">
            <i class="fas fa-calendar-day"></i>
            <h3>Citas Totales Hoy</h3>
            <p class="count">--.--</p>
            <a href="../Consultas/AsignarTurno.php" class="med-link">Ver</a>
        </div>
        
        <div class="med-card upcoming compact">
            <i class="fas fa-calendar-week"></i>
            <h3>Citas Proximas Totales</h3>
            <p class="count">--.--</p>
            <a href="../Consultas/AsignarTurno.php" class="med-link">Ver</a>
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
</body>
</html>