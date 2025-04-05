<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Médicos | Diabetes Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c7873;
            --secondary-color: #6fb98f;
            --accent-color: #f8b400;
            --light-color: #f5f5f5;
            --dark-color: #333;
            --danger-color: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        
        .logo::before {
            content: "\f0f1";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            color: var(--accent-color);
        }
        
        .navbar ul {
            display: flex;
            list-style: none;
        }
        
        .navbar li {
            margin-left: 1.5rem;
            position: relative;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .navbar a i {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        
        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .navbar a.active {
            background-color: var(--accent-color);
        }
        
        .logout-btn {
            background-color: var(--danger-color);
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .main-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-icon {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-right: 1rem;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .card-body {
            color: #666;
        }
        
        .welcome-section {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        
        .welcome-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .welcome-text {
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                padding: 1rem;
            }
            
            .navbar ul {
                margin-top: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .navbar li {
                margin: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">Diabetes Log</div>
        <ul>            
            <li><a href="../iniciomedico.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="/Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> Ing. Paciente</a></li>
            <li><a href="/Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> Asignar Turno</a></li>
            <li><a href="/Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> Datos del Paciente</a></li>
            <li><a href="/Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> Tipos de Diabetes</a></li>
            <li><a href="Logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </nav>
    
    <div class="main-content">
        <section class="welcome-section">
            <h1 class="welcome-title">Bienvenido, Doctor</h1>
            <p class="welcome-text">Desde este panel podrá gestionar a sus pacientes, asignar turnos y consultar información relevante sobre diabetes.</p>
        </section>
        
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-user-plus"></i></div>
                    <h3 class="card-title">Ingresar Paciente</h3>
                </div>
                <div class="card-body">
                    Registre nuevos pacientes en el sistema para comenzar a monitorear su condición.
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                    <h3 class="card-title">Asignar Turnos</h3>
                </div>
                <div class="card-body">
                    Programe consultas y controles para sus pacientes de manera rápida y organizada.
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    <h3 class="card-title">Seguimiento</h3>
                </div>
                <div class="card-body">
                    Revise el progreso y evolución de sus pacientes a lo largo del tiempo.
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-book-medical"></i></div>
                    <h3 class="card-title">Recursos</h3>
                </div>
                <div class="card-body">
                    Acceda a información actualizada sobre los diferentes tipos de diabetes y tratamientos.
                </div>
            </div>
        </div>
    </div>
</body>
</html>