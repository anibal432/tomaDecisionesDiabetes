<?php
session_start();
include('conexionL.php');

$disponibilidadData = [];
$disponibilidadFile = 'Disponible.json';
if (file_exists($disponibilidadFile)) {
    $disponibilidadData = json_decode(file_get_contents($disponibilidadFile), true) ?: [];
}

$correo_medico_logeado = $_SESSION['correo'] ?? '';
$tipo_usuario = '';
if (!empty($correo_medico_logeado)) {
    $query_medico = "SELECT IdMedico FROM Medico WHERE CorreoMedico = ?";
    $stmt_medico = $conn->prepare($query_medico);
    $stmt_medico->bind_param("s", $correo_medico_logeado);
    $stmt_medico->execute();
    $result_medico = $stmt_medico->get_result();
    
    if ($result_medico->num_rows > 0) {
        $tipo_usuario = 'medico';
    } else {
        $query_secre = "SELECT IdSecre FROM Secretarias WHERE CorreoSecre = ?";
        $stmt_secre = $conn->prepare($query_secre);
        $stmt_secre->bind_param("s", $correo_medico_logeado);
        $stmt_secre->execute();
        $result_secre = $stmt_secre->get_result();
        
        if ($result_secre->num_rows > 0) {
            $tipo_usuario = 'secretaria';
        }
    }
    
    $stmt_medico->close();
    if (isset($stmt_secre)) $stmt_secre->close();
}

if (empty($correo_medico_logeado)) {
    header("Location: iniciomedico.php");
    exit();
}

if ($tipo_usuario === 'medico') {
    $query_jefe = "SELECT j.IdJefeM FROM JefeMed j
                  JOIN Medico m ON j.IdMedico = m.IdMedico
                  WHERE m.CorreoMedico = ?";
    $stmt_jefe = $conn->prepare($query_jefe);
    $stmt_jefe->bind_param("s", $correo_medico_logeado);
    $stmt_jefe->execute();
    $result_jefe = $stmt_jefe->get_result();

    if ($result_jefe->num_rows === 0) {
        header("Location: iniciomedico.php");
        exit();
    }
    $stmt_jefe->close();
}

$mostrar_desactivados = isset($_GET['mostrar_desactivados']) && $_GET['mostrar_desactivados'] == '1';
$busqueda = $_GET['busqueda'] ?? '';
$query = "SELECT m.IdMedico, m.PrimerNombre, m.PrimerApellido, m.CorreoMedico, m.NoColegiado, 
          (d.IdMedico IS NOT NULL) AS desactivado
          FROM Medico m
          LEFT JOIN Desactivado d ON m.IdMedico = d.IdMedico
          WHERE m.CorreoMedico != ? 
          AND (m.PrimerNombre LIKE ? OR m.PrimerApellido LIKE ? OR m.CorreoMedico LIKE ? OR m.NoColegiado LIKE ?)";
          
if (!$mostrar_desactivados) {
    $query .= " AND d.IdMedico IS NULL";
}

$query .= " ORDER BY m.PrimerNombre, m.PrimerApellido";

$stmt = $conn->prepare($query);
$busqueda_param = "%$busqueda%";
$stmt->bind_param("sssss", $correo_medico_logeado, $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param);
$stmt->execute();
$result = $stmt->get_result();
$medicos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Médicos | Diabetes Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/insert.css">
</head>
<body>

<?php if ($tipo_usuario == 'medico'): ?>
    <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
        <div class="logo">Diabetes Log</div>
        <ul>            
            <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="../Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> <span>Ing. Paciente</span></a></li>
            <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
            <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Datos del Paciente</span></a></li>
            <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
            <li><a href="insertusuarios.php" class="active"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>
<?php elseif ($tipo_usuario == 'secretaria'): ?>
    <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
        <div class="logo">Admin Log</div>
        <ul>            
            <li><a href="iniciosecre.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="insertusuarios.php" class="active"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
            <li><a href="gestión_secretarias.php"><i class="fa-solid fa-id-card"></i> <span>Ingresar Secre</span></a></li>
            <li><a href="Citas.php"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
            <li><a href="turnospacientes.php"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>
<?php else: ?>
    <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-user"></i></div>
        <div class="logo">Sistema</div>
        <ul>
            <li><a href="Login.php"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
        </ul>
    </nav>
<?php endif; ?>

<main class="main-content">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-md"></i> Gestión de Médicos</h1>
            
            <div class="search-container">
                <form method="GET" class="search-form">
                    <div class="search-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="busqueda" class="search-input" placeholder="Buscar médicos..." value="<?= htmlspecialchars($busqueda) ?>">
                        <?php if(!empty($busqueda)): ?>
                            <button type="button" class="clear-search" onclick="resetSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        <?php endif; ?>
                        <button type="submit" class="search-btn">Buscar</button>
                        <button type="button" class="create-btn" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Crear
                        </button>
                    </div>
                    <div class="filter-group">
                        <label class="filter-toggle">
                            <input type="checkbox" name="mostrar_desactivados" value="1" 
                                <?= $mostrar_desactivados ? 'checked' : '' ?> onchange="this.form.submit()">
                            <span class="filter-label">Mostrar desactivados</span>
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-container">
            <table class="medicos-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>No. Colegiado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicos)): ?>
                        <tr>
                            <td colspan="5" class="no-results">No se encontraron médicos</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($medicos as $medico): ?>
                            <tr>
                                <td><?= htmlspecialchars($medico['PrimerNombre'] . ' ' . $medico['PrimerApellido']) ?></td>
                                <td><?= htmlspecialchars($medico['CorreoMedico']) ?></td>
                                <td><?= htmlspecialchars($medico['NoColegiado']) ?></td>
                                <td>
                                    <span class="status-badge <?= $medico['desactivado'] ? 'status-inactive' : 'status-active' ?>">
                                        <?= $medico['desactivado'] ? 'Inactivo' : 'Activo' ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button class="action-btn edit-btn" onclick="editarMedico(<?= $medico['IdMedico'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn toggle-btn <?= $medico['desactivado'] ? 'inactive' : '' ?>" 
                                            onclick="toggleMedico(<?= $medico['IdMedico'] ?>, <?= $medico['desactivado'] ? '0' : '1' ?>)">
                                        <i class="fas <?= $medico['desactivado'] ? 'fa-user-check' : 'fa-user-slash' ?>"></i>
                                    </button>
                                    <button class="action-btn schedule-btn" onclick="openScheduleModal('<?= $medico['CorreoMedico'] ?>')">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                    <button class="action-btn crown-btn" onclick="assignAsChief(<?= $medico['IdMedico'] ?>, '<?= htmlspecialchars($medico['PrimerNombre'] . ' ' . $medico['PrimerApellido']) ?>')">
                                        <i class="fas fa-crown"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCreateModal()">&times;</span>
        <h2><i class="fas fa-user-plus"></i> Nuevo Médico</h2>
        <form id="createForm" method="POST" action="insertar_medico.php">
            <div class="form-grid">
                <div class="form-group">
                    <label for="primerNombre">Primer Nombre*</label>
                    <input type="text" id="primerNombre" name="primerNombre" required>
                </div>
                <div class="form-group">
                    <label for="segundoNombre">Segundo Nombre</label>
                    <input type="text" id="segundoNombre" name="segundoNombre">
                </div>
                <div class="form-group">
                    <label for="tercerNombre">Tercer Nombre</label>
                    <input type="text" id="tercerNombre" name="tercerNombre">
                </div>
                <div class="form-group">
                    <label for="primerApellido">Primer Apellido*</label>
                    <input type="text" id="primerApellido" name="primerApellido" required>
                </div>
                <div class="form-group">
                    <label for="segundoApellido">Segundo Apellido</label>
                    <input type="text" id="segundoApellido" name="segundoApellido">
                </div>
                <div class="form-group">
                    <label for="correoMedico">Correo Electrónico*</label>
                    <input type="email" id="correoMedico" name="correoMedico" required>
                </div>
                <div class="form-group">
                    <label for="contraMedico">Contraseña*</label>
                    <input type="password" id="contraMedico" name="contraMedico" required>
                </div>
                <div class="form-group">
                    <label for="noColegiado">Número de Colegiado*</label>
                    <input type="text" id="noColegiado" name="noColegiado" required>
                </div>
            </div>
            <div class="form-footer">
                <button type="button" class="cancel-btn" onclick="closeCreateModal()">Cancelar</button>
                <button type="submit" class="submit-btn">Guardar Médico</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2><i class="fas fa-user-edit"></i> Editar Médico</h2>
        <form id="editForm" method="POST" action="editar_medico.php">
            <input type="hidden" id="editIdMedico" name="idMedico">
            <div class="form-grid">
                <div class="form-group">
                    <label for="editPrimerNombre">Primer Nombre*</label>
                    <input type="text" id="editPrimerNombre" name="primerNombre" required>
                </div>
                <div class="form-group">
                    <label for="editSegundoNombre">Segundo Nombre</label>
                    <input type="text" id="editSegundoNombre" name="segundoNombre">
                </div>
                <div class="form-group">
                    <label for="editTercerNombre">Tercer Nombre</label>
                    <input type="text" id="editTercerNombre" name="tercerNombre">
                </div>
                <div class="form-group">
                    <label for="editPrimerApellido">Primer Apellido*</label>
                    <input type="text" id="editPrimerApellido" name="primerApellido" required>
                </div>
                <div class="form-group">
                    <label for="editSegundoApellido">Segundo Apellido</label>
                    <input type="text" id="editSegundoApellido" name="segundoApellido">
                </div>
            </div>
            <div class="form-footer">
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" class="submit-btn">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para horarios -->
<div id="scheduleModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close" onclick="closeScheduleModal()">&times;</span>
        <h3 id="scheduleUserEmail" style="text-align: center; margin-bottom: 10px;"></h3>
        <hr style="margin-bottom: 20px;">
        <div class="days-container">
            <div class="day-box" data-day="Lun" onclick="toggleDay(this)">Lun</div>
            <div class="day-box" data-day="Mar" onclick="toggleDay(this)">Mar</div>
            <div class="day-box" data-day="Mié" onclick="toggleDay(this)">Mie</div>
            <div class="day-box" data-day="Jue" onclick="toggleDay(this)">Jue</div>
            <div class="day-box" data-day="Vie" onclick="toggleDay(this)">Vie</div>
            <div class="day-box" data-day="Sáb" onclick="toggleDay(this)">Sab</div>
            <div class="day-box" data-day="Dom" onclick="toggleDay(this)">Dom</div>
        </div>
        <div class="modal-footer">
            <button class="save-btn" onclick="saveSchedule()">Guardar</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentUserEmail = '';
let scheduleData = <?php echo json_encode($disponibilidadData); ?>;

function openScheduleModal(email) {
    currentUserEmail = email;
    document.getElementById('scheduleUserEmail').textContent = email;
    document.getElementById('scheduleModal').style.display = 'block';
    
    const daysContainer = document.querySelector('.days-container');
    const originalContent = daysContainer.innerHTML;
    daysContainer.innerHTML = '<div class="loader">Cargando...</div>';
    
    setTimeout(() => {
        daysContainer.innerHTML = originalContent;
        loadUserSchedule();
    }, 50);
}

function loadUserSchedule() {
    const userSchedule = scheduleData[currentUserEmail] || {};
    const days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    
    days.forEach(day => {
        const dayElement = document.querySelector(`.day-box[data-day="${day}"]`);
        if (dayElement) {
            dayElement.className = 'day-box';
            
            if (userSchedule[day] === 'available') {
                dayElement.classList.add('available');
            } else if (userSchedule[day] === 'booked') {
                dayElement.classList.add('booked');
            }
        }
    });
}

function toggleDay(element) {
    const day = element.getAttribute('data-day');
    
    if (!scheduleData[currentUserEmail]) {
        scheduleData[currentUserEmail] = {};
    }
    
    const currentState = scheduleData[currentUserEmail][day] || '';

    if (!currentState) {
        scheduleData[currentUserEmail][day] = 'available';
        element.classList.add('available');
    } else if (currentState === 'available') {
        scheduleData[currentUserEmail][day] = 'booked';
        element.classList.remove('available');
        element.classList.add('booked');
    } else {
        delete scheduleData[currentUserEmail][day];
        element.className = 'day-box';
    }
}

function saveSchedule() {
    const saveBtn = document.querySelector('.save-btn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    saveBtn.disabled = true;
    
    fetch('save_schedule.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(scheduleData)
    })
    .then(response => response.json())
    .then(data => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        
        if (data.success) {
            Swal.fire({
                title: 'Éxito',
                text: 'Horario guardado correctamente',
                icon: 'success'
            });
            closeScheduleModal();
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Error al guardar el horario',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        Swal.fire({
            title: 'Error',
            text: 'Error en la conexión',
            icon: 'error'
        });
    });
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}

function toggleMedico(id, desactivar) {
    const confirmMsg = desactivar ? '¿Desactivar este médico?' : '¿Activar este médico?';

    Swal.fire({
        title: confirmMsg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('toggle_medico.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id + '&desactivar=' + desactivar
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo completar la acción.',
                        icon: 'error'
                    });
                } else {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    icon: 'error'
                });
            });
        }
    });
}

function resetSearch() {
    const form = document.querySelector('.search-form');
    const searchInput = form.querySelector('.search-input');
    searchInput.value = '';

    form.submit();
}

function openCreateModal() {
    document.getElementById('createModal').style.display = 'block';
}

function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
}

document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    Swal.fire({
        title: '¿Crear nuevo médico?',
        text: 'Por favor confirme que desea registrar un nuevo médico',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('insertar_medico.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: data.message || 'Médico creado correctamente',
                        icon: 'success'
                    }).then(() => {
                        closeCreateModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Error al crear el médico',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error en la conexión: ' + error,
                    icon: 'error'
                });
            });
        }
    });
});

function editarMedico(idMedico) {
    fetch('obtener_medico.php?id=' + idMedico)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('editIdMedico').value = data.medico.IdMedico;
                document.getElementById('editPrimerNombre').value = data.medico.PrimerNombre;
                document.getElementById('editSegundoNombre').value = data.medico.SegundoNombre || '';
                document.getElementById('editTercerNombre').value = data.medico.TercerNombre || '';
                document.getElementById('editPrimerApellido').value = data.medico.PrimerApellido;
                document.getElementById('editSegundoApellido').value = data.medico.SegundoApellido || '';
                document.getElementById('editModal').style.display = 'block';
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar los datos del médico',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al cargar los datos del médico',
                icon: 'error'
            });
        });
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    Swal.fire({
        title: '¿Guardar cambios?',
        text: 'Por favor confirme que desea actualizar los datos del médico',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('editar_medico.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: data.message || 'Médico actualizado correctamente',
                        icon: 'success'
                    }).then(() => {
                        closeEditModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Error al actualizar el médico',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error en la conexión: ' + error,
                    icon: 'error'
                });
            });
        }
    });
});

function assignAsChief(medicoId, medicoNombre) {
    Swal.fire({
        title: '¿Asignar como Jefe Médico?',
        html: `¿Está seguro de otorgar el puesto de jefe a <b>${medicoNombre}</b>?<br><br>
              <small>Esta acción reemplazará al jefe médico actual (Tú)</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ffd700',
        customClass: {
            confirmButton: 'swal-confirm-chief-btn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('asignar_jefe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `idMedico=${medicoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: data.message || 'Jefe médico asignado correctamente',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Error al asignar jefe médico',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error en la conexión: ' + error,
                    icon: 'error'
                });
            });
        }
    });
}

setInterval(() => {
    fetch('Disponible.json?' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
            scheduleData = data;
        })
        .catch(console.error);
}, 30000);
</script>
</body>
</html>