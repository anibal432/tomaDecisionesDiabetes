<?php
session_start();
include('conexionL.php');

$correo_usuario_logeado = $_SESSION['correo'] ?? '';

$tipo_usuario = '';
if (!empty($correo_usuario_logeado)) {
    $query_medico = "SELECT IdMedico FROM Medico WHERE CorreoMedico = ?";
    $stmt_medico = $conn->prepare($query_medico);
    $stmt_medico->bind_param("s", $correo_usuario_logeado);
    $stmt_medico->execute();
    $result_medico = $stmt_medico->get_result();
    
    if ($result_medico->num_rows > 0) {
        $tipo_usuario = 'medico';
    } else {
        $query_secre = "SELECT IdSecre FROM Secretarias WHERE CorreoSecre = ?";
        $stmt_secre = $conn->prepare($query_secre);
        $stmt_secre->bind_param("s", $correo_usuario_logeado);
        $stmt_secre->execute();
        $result_secre = $stmt_secre->get_result();
        
        if ($result_secre->num_rows > 0) {
            $tipo_usuario = 'secretaria';
        }
    }
}

$mostrar_desactivados = isset($_GET['mostrar_desactivados']) && $_GET['mostrar_desactivados'] == '1';
$busqueda = $_GET['busqueda'] ?? '';
$query = "SELECT s.IdSecre, s.PrimerNombre, s.PrimerApellido, s.CorreoSecre, 
          (d.IdSecre IS NOT NULL) AS desactivado
          FROM Secretarias s
          LEFT JOIN Desactivado d ON s.IdSecre = d.IdSecre
          WHERE s.CorreoSecre != ? 
          AND (s.PrimerNombre LIKE ? OR s.PrimerApellido LIKE ? OR s.CorreoSecre LIKE ?)";
          
if (!$mostrar_desactivados) {
    $query .= " AND d.IdSecre IS NULL";
}

$query .= " ORDER BY s.PrimerNombre, s.PrimerApellido";

$stmt = $conn->prepare($query);
$busqueda_param = "%$busqueda%";
$stmt->bind_param("ssss", $correo_usuario_logeado, $busqueda_param, $busqueda_param, $busqueda_param);
$stmt->execute();
$result = $stmt->get_result();
$secretarias = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Secretarias | Diabetes Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/insert.css">
</head>
<body>

<?php if ($tipo_usuario == 'medico'): ?>
    <!-- Navbar para Médicos -->
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
    <!-- Navbar para Secretarias -->
    <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
        <div class="logo">Admin Log</div>
        <ul>            
            <li><a href="../iniciosecre.php" class="active"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
            <li><a href="gestión_secretarias.php"><i class="fa-solid fa-id-card"></i> <span>Ingresar Secre</span></a></li>
            <li><a href="Citas.php"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
            <li><a href="turnospacientes.php"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>

<?php else: ?>
 <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
        <div class="logo">Admin Log</div>
        <ul>            
            <li><a href="../iniciosecre.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
            <li><a href="gestión_secretarias.php" class="active"><i class="fa-solid fa-id-card"></i> <span>Ingresar Secre</span></a></li>
            <li><a href="Citas.php"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
            <li><a href="turnospacientes.php"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>
<?php endif; ?>

<main class="main-content">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-tie"></i> Gestión de Secretarias</h1>
            
            <div class="search-container">
                <form method="GET" class="search-form">
                <div class="search-group">
    <i class="fas fa-search search-icon"></i>
    <input type="text" name="busqueda" class="search-input" placeholder="Buscar secretarias..." value="<?= htmlspecialchars($busqueda) ?>">
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
                            <span class="filter-label">Mostrar desactivadas</span>
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
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($secretarias)): ?>
                        <tr>
                            <td colspan="4" class="no-results">No se encontraron secretarias</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($secretarias as $secretaria): ?>
                            <tr>
                                <td><?= htmlspecialchars($secretaria['PrimerNombre'] . ' ' . $secretaria['PrimerApellido']) ?></td>
                                <td><?= htmlspecialchars($secretaria['CorreoSecre']) ?></td>
                                <td>
                                    <span class="status-badge <?= $secretaria['desactivado'] ? 'status-inactive' : 'status-active' ?>">
                                        <?= $secretaria['desactivado'] ? 'Inactiva' : 'Activa' ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button class="action-btn edit-btn" onclick="editarSecretaria(<?= $secretaria['IdSecre'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn toggle-btn <?= $secretaria['desactivado'] ? 'inactive' : '' ?>" 
                                            onclick="toggleSecretaria(<?= $secretaria['IdSecre'] ?>, <?= $secretaria['desactivado'] ? '0' : '1' ?>)">
                                        <i class="fas <?= $secretaria['desactivado'] ? 'fa-user-check' : 'fa-user-slash' ?>"></i>
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
        <h2><i class="fas fa-user-plus"></i> Nueva Secretaria</h2>
        <form id="createForm" method="POST" action="insert_secre.php">
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
                    <label for="correoSecre">Correo Electrónico*</label>
                    <input type="email" id="correoSecre" name="correoSecre" required>
                </div>
                <div class="form-group">
                    <label for="contraSecre">Contraseña*</label>
                    <input type="password" id="contraSecre" name="contraSecre" required>
                </div>
            </div>
            <div class="form-footer">
                <button type="button" class="cancel-btn" onclick="closeCreateModal()">Cancelar</button>
                <button type="submit" class="submit-btn">Guardar Secretaria</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2><i class="fas fa-user-edit"></i> Editar Secretaria</h2>
        <form id="editForm" method="POST" action="editar_secre.php">
            <input type="hidden" id="editIdSecre" name="idSecre">
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

<script>
    function toggleSecretaria(id, desactivar) {
        const confirmMsg = desactivar ? '¿Desactivar esta secretaria?' : '¿Activar esta secretaria?';

        Swal.fire({
            title: confirmMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('toggle_secre.php', {
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
            title: '¿Crear nueva secretaria?',
            text: 'Por favor confirme que desea registrar una nueva secretaria',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('insert_secre.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.message || 'Secretaria creada correctamente',
                            icon: 'success'
                        }).then(() => {
                            closeCreateModal();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Error al crear la secretaria',
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

    function editarSecretaria(idSecre) {
        fetch('obtener_secre.php?id=' + idSecre)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('editIdSecre').value = data.secretaria.IdSecre;
                    document.getElementById('editPrimerNombre').value = data.secretaria.PrimerNombre;
                    document.getElementById('editSegundoNombre').value = data.secretaria.SegundoNombre || '';
                    document.getElementById('editTercerNombre').value = data.secretaria.TercerNombre || '';
                    document.getElementById('editPrimerApellido').value = data.secretaria.PrimerApellido;
                    document.getElementById('editSegundoApellido').value = data.secretaria.SegundoApellido || '';
                    document.getElementById('editModal').style.display = 'block';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al cargar los datos de la secretaria',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar los datos de la secretaria',
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
            text: 'Por favor confirme que desea actualizar los datos de la secretaria',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('editar_secre.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: data.message || 'Secretaria actualizada correctamente',
                            icon: 'success'
                        }).then(() => {
                            closeEditModal();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Error al actualizar la secretaria',
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

    window.onclick = function(event) {
        const modal = document.getElementById('createModal');
        const editModal = document.getElementById('editModal');
        
        if (event.target == modal) {
            closeCreateModal();
        }
        if (event.target == editModal) {
            closeEditModal();
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>