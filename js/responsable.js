function abrirModalResponsablePaciente(idPaciente) {
    $('#formResponsablePaciente')[0].reset();
    $('#id_paciente_responsable').val(idPaciente);
    $('#responsablePacienteModal').modal('show');
}

function abrirModalEditarResponsablePaciente(idPaciente) {
    $.get('obtenerResponsablePaciente.php', { id_paciente: idPaciente })
        .done(function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success && data.data) {
                    const responsable = data.data;
                    $('#id_responsable').val(responsable.IdResponsable);
                    $('#id_paciente_responsable').val(idPaciente);
                    $('#primer_nombre').val(responsable.PrimerNombre);
                    $('#segundo_nombre').val(responsable.SegundoNombre);
                    $('#tercer_nombre').val(responsable.TercerNombre);
                    $('#primer_apellido').val(responsable.PrimerApellido);
                    $('#segundo_apellido').val(responsable.SegundoApellido);
                    $('#no_dpi').val(responsable.NoDpi);
                    $('#telefono').val(responsable.Telefono);
                    $('#email').val(responsable.Email);
                    $('#responsablePacienteModal').modal('show');
                } else {
                    alert('No hay datos del responsable registrados');
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Error al procesar los datos');
            }
        })
        .fail(function() {
            alert('Error al cargar los datos del responsable');
        });
}

$(document).ready(function() {
    $('#formResponsablePaciente').on('submit', function(e) {
        e.preventDefault();
        $.post('guardarResponsablePaciente.php', $(this).serialize())
            .done(function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        alert('Datos guardados correctamente');
                        $('#responsablePacienteModal').modal('hide');
                        location.reload();
                    } else {
                        alert(result.message || 'Error al guardar los datos');
                    }
                } catch (e) {
                    alert('Error al procesar la respuesta del servidor');
                }
            })
            .fail(function() {
                alert('Error al guardar los datos');
            });
    });
});