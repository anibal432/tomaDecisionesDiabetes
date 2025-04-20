/**
 * Funciones para el manejo de pacientes
 * Versión optimizada y consolidada
 */

// Objeto global para manejar el estado
const PacientesApp = {
    modals: {},
    init: function() {
        this.setupModals();
        this.setupEventListeners();
    },
    
    setupModals: function() {
        this.modals = {
            editarPaciente: new bootstrap.Modal('#editarPacienteModal'),
            datosVitales: new bootstrap.Modal('#datosVitalesModal'),
            antecedentesPersonales: new bootstrap.Modal('#antecedentesPersonalesModal'),
            antecedentesFamiliares: new bootstrap.Modal('#antecedentesFamiliaresModal'),
            responsable: new bootstrap.Modal('#responsableModal'),
            historiaClinica: new bootstrap.Modal('#historiaClinicaModal')
        };
    },
    
    setupEventListeners: function() {
        // Manejo de formularios
        $('form[id^="form"]').on('submit', this.handleFormSubmit);
        
        // Buscador de pacientes
        $('#searchPaciente').on('input', this.buscarPaciente);
    },
    
    handleFormSubmit: function(e) {
        e.preventDefault();
        const form = $(this);
        const action = form.attr('action');
        const method = form.attr('method') || 'POST';
        
        $.ajax({
            url: action,
            type: method,
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Operación exitosa');
                    form.closest('.modal').modal('hide');
                    if (response.reload) {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    alert(response.error || 'Error en la operación');
                }
            },
            error: function(xhr, status, error) {
                alert(`Error: ${error}`);
            }
        });
    },
    
    buscarPaciente: function() {
        const searchValue = $(this).val().toLowerCase();
        $('tbody tr').each(function() {
            const name = $(this).find('td:eq(1)').text().toLowerCase();
            $(this).toggle(name.includes(searchValue));
        });
    },
    
    // Funciones para abrir modales
    abrirModalEditarPaciente: function(idPaciente) {
        $.getJSON('obtenerPaciente.php', { id_paciente: idPaciente })
            .done(function(paciente) {
                if (paciente) {
                    $('#id_paciente_editar').val(paciente.IdPaciente);
                    $('#primer_nombre').val(paciente.NombreUno);
                    // Resto de campos...
                    
                    PacientesApp.modals.editarPaciente.show();
                } else {
                    alert('No se encontró el paciente');
                }
            })
            .fail(function() {
                alert('Error al cargar datos del paciente');
            });
    },
    
    abrirModalDatosVitales: function(idPaciente) {
        $('#id_paciente').val(idPaciente);
        PacientesApp.modals.datosVitales.show();
    },
    
    abrirModalEditarDatosVitales: function(idPaciente) {
        $.getJSON('obtenerDatosVitales.php', { id_paciente: idPaciente })
            .done(function(datos) {
                if (datos) {
                    $('#id_paciente').val(idPaciente);
                    $('#peso').val(datos.Peso);
                    // Resto de campos...
                    
                    $('#btnGuardarDatosVitales').text('Guardar Cambios');
                    PacientesApp.modals.datosVitales.show();
                } else {
                    alert('No se encontraron datos');
                }
            })
            .fail(function() {
                alert('Error al cargar datos vitales');
            });
    },
    
    // Resto de funciones para otros modales...
};

// Inicializar la aplicación cuando el DOM esté listo
$(document).ready(function() {
    PacientesApp.init();
});