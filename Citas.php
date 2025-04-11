<?php
session_start();
include('conexionL.php');

$query_medicos = "SELECT IdMedico, CONCAT(PrimerNombre, ' ', PrimerApellido) AS nombre_completo, CorreoMedico FROM Medico";
$result_medicos = $conn->query($query_medicos);
$medicos = $result_medicos->fetch_all(MYSQLI_ASSOC);

$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

$disponibilidad = [];
if (file_exists('Disponible.json')) {
    $disponibilidad = json_decode(file_get_contents('Disponible.json'), true);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Citas | Admin Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/citas.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-clipboard-user"></i></div>
    <div class="logo">Admin Log</div>
    <ul>            
        <li><a href="../iniciosecre.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
        <li><a href="insertusuarios.php"><i class="fa-solid fa-id-card"></i> <span>Ingresar Secre</span></a></li>
        <li><a href="Citas.php" class="active"><i class="fa-solid fa-calendar-days"></i> <span>Agendar Cita</span></a></li>
        <li><a href="turnospacientes.php"><i class="fa-solid fa-ticket"></i><span>Turnos de Pacientes</span></a></li>
        <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>

<main class="main-content">
    <div class="calendar-container">
        <div class="calendar-header">
            <h2 class="calendar-title">
                <?php 
                $monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
                              "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                echo $monthNames[$currentMonth - 1] . ' ' . $currentYear;
                ?>
            </h2>
            <div class="calendar-nav">
                <button class="nav-button" id="delete-citas-btn" style="margin: 0 10px;">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button class="nav-button" id="prev-month">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="nav-button" id="next-month">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="calendar-grid">
    <div class="calendar-day-header">Lun</div>
    <div class="calendar-day-header">Mar</div>
    <div class="calendar-day-header">Mié</div>
    <div class="calendar-day-header">Jue</div>
    <div class="calendar-day-header">Vie</div>
    <div class="calendar-day-header">Sáb</div>
    <div class="calendar-day-header">Dom</div>
    
    <?php
    $firstDayOfMonth = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
    $daysInMonth = date('t', $firstDayOfMonth);
    $dayOfWeek = date('N', $firstDayOfMonth); 
    for ($i = 1; $i < $dayOfWeek; $i++) {
        echo '<div class="calendar-day disabled-day"></div>';
    }
    
    $currentDay = date('j');
    $currentMonthNum = date('n');
    $currentYearNum = date('Y');
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $day, $currentYear));
        $dayAbbr = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'][(date('N', mktime(0, 0, 0, $currentMonth, $day, $currentYear)) - 1)];
        
        $dayClass = 'calendar-day';
        $isPast = ($currentYear < $currentYearNum) || 
                 ($currentYear == $currentYearNum && $currentMonth < $currentMonthNum) ||
                 ($currentYear == $currentYearNum && $currentMonth == $currentMonthNum && $day < $currentDay);
        $isToday = ($day == $currentDay && $currentMonth == $currentMonthNum && $currentYear == $currentYearNum);
        
        if ($isToday) {
            $dayClass .= ' current-day';
        }
        
        $hasBookedAvailability = false;
        foreach ($medicos as $medico) {
            $correo = $medico['CorreoMedico'];
            if (isset($disponibilidad[$correo][$dayAbbr]) && $disponibilidad[$correo][$dayAbbr] === 'booked') {
                $query = "SELECT COUNT(*) as count FROM citas 
                         WHERE IdMedico = ? AND fecha = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("is", $medico['IdMedico'], $date);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] < 5) {
                    $hasBookedAvailability = true;
                    break;
                }
            }
        }
        
        if ($isPast) {
            $dayClass .= ' disabled-day';
        } else if ($hasBookedAvailability) {
            $dayClass .= ' available-day';
        } else {
            $dayClass .= ' disabled-day';
        }
        
        echo '<div class="' . $dayClass . '" data-date="' . $date . '" onclick="handleDayClick(this)">';
        echo '<div class="day-number">' . $day . '</div>';
        echo '</div>';
        
        if (date('N', mktime(0, 0, 0, $currentMonth, $day, $currentYear)) == 7 && $day != $daysInMonth) {
        }
    }

    $lastDayOfWeek = date('N', mktime(0, 0, 0, $currentMonth, $daysInMonth, $currentYear));
    if ($lastDayOfWeek != 7) {
        for ($i = $lastDayOfWeek; $i < 7; $i++) {
            echo '<div class="calendar-day disabled-day"></div>';
        }
    }
    ?>
</div>
    </div>
</main>

<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Agendar Cita</h3>
            <button class="close-button" onclick="closeModal()">&times;</button>
        </div>
        
        <div class="form-container">
            <div class="medical-section">
                <div class="form-group">
                    <label for="medicoSelect">Médico:</label>
                    <select id="medicoSelect" class="form-control"></select>
                </div>
                
                <div class="form-group">
                    <label>Horarios disponibles:</label>
                    <div class="time-slots" id="timeSlots"></div>
                </div>
            </div>
            
            <div class="personal-data-section">
                <div class="form-group">
                    <label for="primerNombre">Primer Nombre:</label>
                    <input type="text" id="primerNombre" required>
                </div>
                
                <div class="form-group">
                    <label for="segundoNombre">Segundo Nombre:</label>
                    <input type="text" id="segundoNombre">
                </div>
                
                <div class="form-group">
                    <label for="primerApellido">Primer Apellido:</label>
                    <input type="text" id="primerApellido" required>
                </div>
                
                <div class="form-group">
                    <label for="segundoApellido">Segundo Apellido:</label>
                    <input type="text" id="segundoApellido">
                </div>
                
                <div class="form-group">
                    <label for="correoElectronico">Correo Electrónico:</label>
                    <input type="email" id="correoElectronico" required>
                </div>
                
                <div class="form-group">
                    <label for="numeroCelular">Número de Celular:</label>
                    <input type="tel" id="numeroCelular" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="btn btn-primary" onclick="saveAppointment()">Guardar Cita</button>
            </div>
        </div>
    </div>
</div>

<div id="deleteCitasModal" class="delete-modal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h3 class="delete-modal-title"><i class="fas fa-trash-alt"></i> Eliminar Citas</h3>
            <button class="close-button" onclick="closeDeleteModal()">&times;</button>
        </div>
        
        <div class="search-container">
    <div class="search-group">
        <input type="text" id="searchCitasInput" class="search-input" placeholder="Buscar citas...">
        <button class="clear-search" id="clearSearchBtn">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <button class="search-btn" id="searchCitasBtn">
        Buscar
    </button>
</div>
        
        <div class="citas-table-container">
            <table class="citas-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Correo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Médico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="citasTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let selectedDate = '';
    let selectedTime = '';
    let selectedMedicoId = '';
    const timeSlots = ['07:00', '09:00', '11:00', '14:00', '16:00'];
    
    document.getElementById('prev-month').addEventListener('click', function() {
        let prevMonth = <?= $currentMonth ?> - 1;
        let prevYear = <?= $currentYear ?>;
        
        if (prevMonth < 1) {
            prevMonth = 12;
            prevYear--;
        }
        
        window.location.href = `Citas.php?month=${prevMonth}&year=${prevYear}`;
    });

    document.getElementById('next-month').addEventListener('click', function() {
        let nextMonth = <?= $currentMonth ?> + 1;
        let nextYear = <?= $currentYear ?>;
        
        if (nextMonth > 12) {
            nextMonth = 1;
            nextYear++;
        }
        
        window.location.href = `Citas.php?month=${nextMonth}&year=${nextYear}`;
    });

    document.addEventListener('DOMContentLoaded', function() {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1;
        
        const prevButton = document.getElementById('prev-month');
        
        if (<?= $currentYear ?> == currentYear && <?= $currentMonth ?> == currentMonth) {
            prevButton.disabled = true;
        }
    });
    
    function handleDayClick(dayElement) {
        const date = dayElement.getAttribute('data-date');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selectedDateObj = new Date(date);
        
        if (selectedDateObj < today || !dayElement.classList.contains('available-day')) {
            Swal.fire({
                title: 'Día no disponible',
                text: 'No se pueden agendar citas en días pasados o no disponibles',
                icon: 'info'
            });
            return;
        }
        
        selectedDate = date;
        document.getElementById('appointmentModal').style.display = 'block';
        loadAvailableDoctors(selectedDate);
    }
    
    function loadAvailableDoctors(date) {
        fetch('get_available_doctors.php?date=' + date)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('medicoSelect');
                select.innerHTML = '';
                
                if (data.length === 0) {
                    select.innerHTML = '<option value="">No hay médicos disponibles</option>';
                    document.getElementById('timeSlots').innerHTML = '';
                    return;
                }
                
                data.forEach(medico => {
                    const option = document.createElement('option');
                    option.value = medico.IdMedico;
                    option.textContent = medico.nombre_completo;
                    select.appendChild(option);
                });
                
                select.addEventListener('change', function() {
                    selectedMedicoId = this.value;
                    loadTimeSlots(date, this.value);
                });
                
                if (data.length > 0) {
                    selectedMedicoId = data[0].IdMedico;
                    loadTimeSlots(date, data[0].IdMedico);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    function loadTimeSlots(date, medicoId) {
    fetch('get_available_times.php?date=' + date + '&medicoId=' + medicoId)
        .then(response => response.json())
        .then(data => {
            const timeSlotsContainer = document.getElementById('timeSlots');
            timeSlotsContainer.innerHTML = '';
            
            timeSlots.forEach(time => {
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                timeSlot.textContent = time;
                timeSlot.setAttribute('data-time', time);
                
                if (data.bookedTimes.includes(time)) {
                    timeSlot.classList.add('booked');
                    timeSlot.title = 'Este horario ya está reservado';
                    timeSlot.style.pointerEvents = 'none';
                    timeSlot.style.opacity = '0.6';
                } else {
                    timeSlot.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot').forEach(slot => {
                            slot.classList.remove('selected');
                        });
                        
                        this.classList.add('selected');
                        selectedTime = this.getAttribute('data-time');
                    });
                }
                
                timeSlotsContainer.appendChild(timeSlot);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
    
    function saveAppointment() {
        if (!selectedTime) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor seleccione un horario',
                icon: 'error'
            });
            return;
        }
        
        const appointmentData = {
            primerNombre: document.getElementById('primerNombre').value,
            segundoNombre: document.getElementById('segundoNombre').value,
            primerApellido: document.getElementById('primerApellido').value,
            segundoApellido: document.getElementById('segundoApellido').value,
            correoElectronico: document.getElementById('correoElectronico').value,
            numeroCelular: document.getElementById('numeroCelular').value,
            fecha: selectedDate,
            hora: selectedTime,
            IdMedico: selectedMedicoId,
            estado: 'pendiente'
        };
        
        if (!appointmentData.primerNombre || !appointmentData.primerApellido || 
            !appointmentData.correoElectronico || !appointmentData.numeroCelular) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor complete todos los campos requeridos',
                icon: 'error'
            });
            return;
        }
        
        fetch('save_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(appointmentData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Éxito',
                    text: 'Cita agendada correctamente',
                    icon: 'success'
                }).then(() => {
                    closeModal();
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Error al agendar la cita',
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
    
    function closeModal() {
        document.getElementById('appointmentModal').style.display = 'none';
        selectedTime = '';
        selectedMedicoId = '';

        document.getElementById('primerNombre').value = '';
    document.getElementById('segundoNombre').value = '';
    document.getElementById('primerApellido').value = '';
    document.getElementById('segundoApellido').value = '';
    document.getElementById('correoElectronico').value = '';
    document.getElementById('numeroCelular').value = '';

    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    }

function openDeleteModal() {
    document.getElementById('deleteCitasModal').style.display = 'block';
    loadCitas();
}

function closeDeleteModal() {
    document.getElementById('deleteCitasModal').style.display = 'none';
}

document.getElementById('delete-citas-btn').addEventListener('click', openDeleteModal);

document.getElementById('searchCitasBtn').addEventListener('click', function() {
    loadCitas(searchInput.value);
});

document.getElementById('clearSearchBtn').addEventListener('click', function() {
    document.getElementById('searchCitasInput').value = '';
    this.style.display = 'none';
    loadCitas();
});

function loadCitas(searchTerm = '') {
    const tbody = document.getElementById('citasTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="loading-text"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>';
    
    fetch('get_citas.php?search=' + encodeURIComponent(searchTerm))
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="no-results">No se encontraron citas</td></tr>';
                return;
            }
            
            data.forEach(cita => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${cita.primer_nombre} ${cita.primer_apellido}</td>
                    <td>${cita.correo_electronico}</td>
                    <td>${cita.fecha}</td>
                    <td>${cita.hora}</td>
                    <td>${cita.nombre_medico}</td>
                    <td>
                        <button class="delete-btn" onclick="deleteCita(${cita.id})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="error-text">Error al cargar citas</td></tr>';
        });
}
function deleteCita(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('delete_cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        '¡Eliminada!',
                        'La cita ha sido eliminada.',
                        'success'
                    );
                    loadCitas(document.getElementById('searchCitasInput').value);
                } else {
                    Swal.fire(
                        'Error',
                        'No se pudo eliminar la cita.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error',
                    'Ocurrió un error al eliminar la cita.',
                    'error'
                );
            });
        }
    });
}

const searchInput = document.getElementById('searchCitasInput');
const clearBtn = document.getElementById('clearSearchBtn');
searchInput.addEventListener('input', function() {
    clearBtn.style.display = this.value ? 'block' : 'none';
});
clearBtn.addEventListener('click', function() {
    searchInput.value = '';
    clearBtn.style.display = 'none';
    loadCitas();
    searchInput.focus();
});

</script>
</body>
</html>