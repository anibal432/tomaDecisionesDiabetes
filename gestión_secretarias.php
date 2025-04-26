<?php
session_start();
include('conexionL.php');

$correo_usuario_logeado = $_SESSION['correo'] ?? '';

if (empty($correo_usuario_logeado)) {
    header("Location: iniciosecre.php");
    exit();
}

$query_jefe_sec = "SELECT js.IdJefeS FROM JefeSec js
                  JOIN Secretarias s ON js.IdSecre = s.IdSecre
                  WHERE s.CorreoSecre = ?";
$stmt_jefe_sec = $conn->prepare($query_jefe_sec);
$stmt_jefe_sec->bind_param("s", $correo_usuario_logeado);
$stmt_jefe_sec->execute();
$result_jefe_sec = $stmt_jefe_sec->get_result();

if ($result_jefe_sec->num_rows === 0) {
    header("Location: iniciosecre.php");
    exit();
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
    <title>Gestión de Secretarias | Admin Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/insert.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
        <div class="logo">Admin Log</div>
        <ul>            
            <li><a href="iniciosecre.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Gestion de Medicos</span></a></li>
            <li><a href="gestión_secretarias.php" class="active"><i class="fa-solid fa-id-card"></i> <span>Gestion de Secretaría</span></a></li>
            <li><a href="Citas.php"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
            <li><a href="turnospacientes.php"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>

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
                                    <button class="action-btn crown-btn" onclick="assignAsChief(<?= $secretaria['IdSecre'] ?>, '<?= htmlspecialchars($secretaria['PrimerNombre'] . ' ' . $secretaria['PrimerApellido']) ?>')">
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

    function assignAsChief(secretariaId, secretariaNombre) {
    Swal.fire({
        title: '¿Asignar como Jefe Admin?',
        html: `¿Está seguro de otorgar el puesto de jefe a <b>${secretariaNombre}</b>?<br><br>
              <small>Esta acción reemplazará al jefe admin actual (Tú)</small>`,
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
            fetch('asignar_jefeAdmin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `idSecre=${secretariaId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: data.message || 'Jefe Admin asignado correctamente',
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Error al asignar jefe admin',
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

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>