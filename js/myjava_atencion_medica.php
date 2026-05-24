<script>
$(document).ready(function() {
    evaluarRegistrosPendientes();
    evaluarRegistrosPendientesEmail();
    setInterval('pagination(1)', 22000);
    setInterval('pagination(1)', 22000);
    setInterval('evaluarRegistrosPendientes()', 1800000); //CADA MEDIA HORA
    getColaboradoresFacturacion();
    getPacientesFacturacion();
    getServiciosFacturacion();
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function() {
    $("#registro_transito_eviada").on('shown.bs.modal', function() {
        $(this).find('#formulario_transito_enviada #expediente').focus();
    });
});

$(document).ready(function() {
    $("#registro_transito_recibida").on('shown.bs.modal', function() {
        $(this).find('#formulario_transito_recibida #expediente').focus();
    });
});

$(document).ready(function() {
    $("#modal_registro_atenciones").on('shown.bs.modal', function() {
        $(this).find('#formulario_atenciones #expediente').focus();
    });
});

$(document).ready(function() {
    $("#buscar_atencion").on('shown.bs.modal', function() {
        $(this).find('#formulario_buscarAtencion #busqueda').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
    $('.footer').show();
    $('.footer1').hide();

    //LLAMADA A LAS FUNCIONES
    funcionesFormPacientes();

    //INICIO ABRIR VENTANA MODAL PARA EL REGISTRO DE ATENCIONES DE PACIENTES
    $('#form_main #nuevo_registro').on('click', function(e) {
        e.preventDefault();
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
            getUsuarioSistema() == 5) {
            $('#formulario_atenciones')[0].reset();
            limpiarFormPacientes();
            $('#reg_atencion').show();
            $('#edi_atencion').hide();
            $('#formulario_atenciones #consultorio_').hide();
            $("#formulario_atenciones #label_servicio").hide();
            $("#formulario_atenciones #servicio").hide();
            $("#formulario_atenciones #fecha").attr('readonly', false);
            $("#formulario_atenciones #paciente_consulta").attr('disabled', false);
            $("#reg_atencion").attr('disabled', false);
            $('#formulario_atenciones #consultorio_').show();
            $('#formulario_atenciones .nav-tabs li:eq(0) a').tab('show');

            //HABILITAR OBJETOS
            $('#formulario_atenciones #paciente_consulta').attr('disabled', false);

            $('#formulario_atenciones').attr({
                'data-form': 'save'
            });
            $('#formulario_atenciones').attr({
                'action': '<?php echo SERVERURL; ?>php/atencion_pacientes/agregar.php'
            });

            FormAtencionMedica();

            /*$('#modal_registro_atenciones').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });*/

            return false;
        } else {
            swal({
                title: "Acceso Denegado",
                text: "No tiene permisos para ejecutar esta acción",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    });
    //FIN ABRIR VENTANA MODAL PARA EL REGISTRO DE ATENCIONES DE PACIENTES

    //INICIO ABRIR VENTANA MODAL TRANSITO ENVIADA
    $('#form_main #transito_enviada').on('click', function() {
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
            getUsuarioSistema() == 5) {
            $('#formulario_transito_enviada #pro').val("Registro");
            $('#registro_transito_eviada').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
            limpiarTE();
            return false;
        } else {
            swal({
                title: "Acceso Denegado",
                text: "No tiene permisos para ejecutar esta acción",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    });
    //FIN ABRIR VENTANA MODAL TRANSITO ENVIADA

    //INICIO ABRIR VENTANA MODAL TRANSITO RECIBIDA
    $('#form_main #transito_recibida').on('click', function() {
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
            getUsuarioSistema() == 5) {
            $('#formulario_transito_recibida #pro').val("Registro");
            $('#registro_transito_recibida').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
            limpiarTR();
            return false;
        } else {
            swal({
                title: "Acceso Denegado",
                text: "No tiene permisos para ejecutar esta acción",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    });
    //FIN ABRIR VENTANA MODAL TRANSITO RECIBIDA

    //INICIO BOTON CERRAR METODO DE PAGO
    $('#formulario_metodoPago #boton_close_mp').on('click', function() {
        if ($('#formulario_metodoPago #nombre').val != "" && $('#formulario_metodoPago #tipo_tarifa')
            .val != "" && $('#formulario_metodoPago #monto').val != "" && $(
                '#formulario_metodoPago #neto').val != "") {
            swal({
                title: "Advertencia",
                text: "No puede cerrar esta venta, hay datos en el formulario, debe proceder con los datos de la facturación del paciente",
                icon: "warning",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        }
    });
    //FIN BOTON CERRAR METODO DE PAGO

    //INICIO REGISTRAR METODO DE PAGO PARA EL PACIENTE
    $('#formulario_metodoPago #reg').on('click', function(e) {
        if ($('#formulario_metodoPago #descuento').val() != "" && $('#formulario_metodoPago #tipo_pago')
            .val() != "") {
            e.preventDefault();
            agregarMetodoPago();
        } else {
            swal({
                title: "Error",
                text: "Hay registros en blanco, por favor llenar todos los datos del formulario antes de continuar",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        }
    });
    //FIN REGISTRAR METODO DE PAGO PARA EL PACIENTE	

    //INICIO CONSULTRAR USUARIOS ATENDIDOS
    $('#form_main #historial').on('click', function(
        e) { // add event submit We don't want this to act as a link so cancel the link action
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
            getUsuarioSistema() == 5) {
            e.preventDefault();
            paginationBusqueda(1);
            $('#formulario_buscarAtencion #pro').val("Búsqueda de Atenciones");
            $('#formulario_buscarAtencion #paciente_consulta').html("");
            $('#formulario_buscarAtencion #agrega_registros_busqueda_').html(
                '<td colspan="3" style="color:#C7030D">No se encontraron resultados, seleccione un paciente para visualizar sus datos</td>'
            );
            $('#buscar_atencion').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
        } else {
            swal({
                title: "Acceso Denegado",
                text: "No tiene permisos para ejecutar esta acción",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    });
    //FIN CONSULTRAR USUARIOS ATENDIDOS

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
    $('#form_main #bs_regis').on('keyup', function() {
        pagination(1);
    });

    $('#form_main #fecha_b').on('change', function() {
        pagination(1);
    });

    $('#form_main #fecha_f').on('change', function() {
        pagination(1);
    });

    $('#form_main #estado').on('change', function() {
        pagination(1);
    });

    $('#formulario_buscarAtencion #busqueda').on('keyup', function() {
        paginationBusqueda(1);
        $('#formulario_buscarAtencion #paciente_consulta').html('');
        $('#formulario_buscarAtencion #agrega_registros_busqueda_').html(
            '<td colspan="12" style="color:#C7030D">No se encontraron resultados</td>');
        $('#formulario_buscarAtencion #pagination_busqueda_').html('');
    });
    //FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)

});
//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/

//INICIO FUNCION PARA OBTENER LOS COLABORADORES
function getColaborador() {
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#registro_transito_eviada #enviada').html("");
            $('#registro_transito_eviada #enviada').html(data);
            $('#registro_transito_eviada #enviada').selectpicker('refresh');

            $('#formulario_transito_recibida #recibida').html("");
            $('#formulario_transito_recibida #recibida').html(data);
            $('#formulario_transito_recibida #recibida').selectpicker('refresh');
        }
    });
}

function editarRegistro(pacientes_id, agenda_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5) {
        if ($('#form_main #estado').val() == 0) {
            $('#formulario_atenciones')[0].reset();
            var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/editar.php';

            $.ajax({
                type: 'POST',
                url: url,
                data: 'pacientes_id=' + pacientes_id + '&agenda_id=' + agenda_id,
                success: function(valores) {
                    // Usar JSON.parse en lugar de eval
                    var array = JSON.parse(valores);

                    $('#reg_atencion').hide();
                    $('#edi_atencion').show();
                    $('#formulario_atenciones #pro').val('Registro');
                    $('#formulario_atenciones #pacientes_id').val(pacientes_id);
                    $('#formulario_atenciones #agenda_id').val(agenda_id);
                    $('#formulario_atenciones #identidad').val(array[0]);
                    $('#formulario_atenciones #nombre').val(array[1]);

                    // Imprime el valor de edad en consola para verificar
                    $('#formulario_atenciones #edad').val(array[2]);

                    $('#formulario_atenciones #procedencia').val(array[3]);
                    $('#formulario_atenciones #religion_id').val(array[4]);
                    $('#formulario_atenciones #religion_id').selectpicker('refresh');

                    $('#formulario_atenciones #profesion_id').val(array[5]);
                    $('#formulario_atenciones #profesion_id').selectpicker('refresh');

                    $('#formulario_atenciones #paciente_consulta').val(array[6]);
                    $('#formulario_atenciones #paciente_consulta').selectpicker('refresh');

                    $('#formulario_atenciones #fecha').val(array[7]);
                    $('#formulario_atenciones #fecha_nac').val(array[8]);

                    $('#formulario_atenciones #antecedentes').val(array[9]);
                    $('#formulario_atenciones #historia_clinica').val(array[10]);
                    $('#formulario_atenciones #exame_fisico').val(array[11]);
                    $('#formulario_atenciones #diagnostico').val(array[12]);
                    $('#formulario_atenciones #seguimiento_read').val(array[13]);
                    $('#formulario_atenciones #servicio_id').val(array[14]);
                    $('#formulario_atenciones #servicio_id').selectpicker('refresh');

                    $('#formulario_atenciones #estado_civil').val(array[15]);
                    $('#formulario_atenciones #estado_civil').selectpicker('refresh');

                    $('#formulario_atenciones #num_hijos').val(array[16]);
                    $("#formulario_atenciones #fecha").attr('readonly', true);
                    $("#edi_atencion").attr('disabled', false);
                    $("#formulario_atenciones #label_servicio").show();
                    $('#formulario_atenciones #consultorio_').hide();
                    $('#formulario_atenciones .nav-tabs li:eq(0) a').tab('show');

                    // DESHABILITAR OBJETOS
                    $('#formulario_atenciones #paciente_consulta').attr('disabled', true);
                    $('#formulario_atenciones #procedencia').attr('readonly', false);

                    $('#formulario_atenciones').attr({
                        'data-form': 'save'
                    });
                    $('#formulario_atenciones').attr({
                        'action': '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarRegistro.php'
                    });

                    inicializarContadores(limites); // Iniciar el contador de caracteres con los límites
                    inicializarSpeechRecognition(limites); // Inicializar reconocimiento de voz con los límites

                    FormAtencionMedica();
                    return false;
                }
            });
            return false;
        } else {
            swal({
                title: "Error",
                text: "Lo sentimos, este registro ya existe, no se puede agregar nuevamente su atención",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
        });
    }
}


//INICIO ABRIR VENTANA MODAL PARA EL METODO DE PAGO
function metodoPago(pacientes_id, agenda_id, colaborador_id, tipo_tarifa) {

    //CERRAMOS EL FORMULARIO DE PACIENTES
    //$("#modal_registro_atenciones .close").click()

    //MOSTRAMOS EL FORMULARIO PARA EL METODO DE PAGO
    $('#formulario_metodoPago #pro').val('Registro');
    $('#formulario_metodoPago #pacientes_id').val(pacientes_id);
    $('#formulario_metodoPago #agenda_id').val(agenda_id);
    $('#formulario_metodoPago #nombre').val(getNombrePaciente(pacientes_id));
    $('#formulario_metodoPago #monto').val(getMonto(getColaborador_id(), agenda_id, tipo_tarifa));
    $("#formulario_metodoPago #reg").attr('disabled', false);
    $('#metodoPago').modal({
        show: true,
        keyboard: false,
        backdrop: 'static'
    });
}
//FIN ABRIR VENTANA MODAL PARA EL METODO DE PAGO

//INICIO FUNCION AUSENCIA DE USUARIOS
function nosePresentoRegistro(pacientes_id, agenda_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 5) {
        if ($('#form_main #estado').val() == 0) {
            var nombre_usuario = consultarNombre(pacientes_id);
            var expediente_usuario = consultarExpediente(pacientes_id);
            var dato;

            if (expediente_usuario == 0) {
                dato = nombre_usuario;
            } else {
                dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
            }

            swal({
                title: "¿Esta seguro?",
                text: "¿Desea remover este usuario: " + dato + " que no se presento a su cita?",
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "Comentario",
                        type: "text",
                    },
                },
                icon: "warning",
                buttons: {
                    cancel: "Cancelar",
                    confirm: {
                        text: "¡Sí, remover el usuario!",
                        closeModal: false,
                    },
                },
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                 
            }).then((value) => {
                if (value === null || value.trim() === "") {
                    swal("¡Necesita escribir algo!", { icon: "error" });
                    return false;
                }
                eliminarRegistro(agenda_id, value);
            });
        } else {
            swal({
                title: "Error",
                text: "Error al ejecutar esta acción, el usuario debe estar en estatus pendiente",
                type: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
        });
    }
}

function eliminarRegistro(agenda_id, comentario, fecha) {
    var hoy = new Date();
    fecha_actual = convertDate(hoy);

    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/usuario_no_presento.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'agenda_id=' + agenda_id + '&fecha=' + fecha + '&comentario=' + comentario,
        success: function(registro) {
            if (registro == 1) {
                swal({
                    title: "Success",
                    text: "Ausencia almacenada correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-close
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                     
                });
                pagination(1);
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al remover este registro",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Este registro ya tiene almacenada una ausencia",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                });
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "Error al ejecutar esta acción",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                });
            }
        }
    });
    return false;
}
//FIN FUNCION AUSENCIA DE USUARIOS

//ATENCION A USUARIOS
function actualizarCaracteres(idCampo, idContador) {
    var max_chars = 3200;
    var chars = $('#' + idCampo).val().length;
    var diff = max_chars - chars;

    $('#' + idContador).html(diff + ' Caracteres');

    if (diff == 0) {
        return false;
    }
}

// Llama a la función para inicializar los contadores al cargar el DOM
// Definir los límites de caracteres globalmente
var limites = {
    'antecedentes': 3200,
    'historia_clinica': 3200,
    'exame_fisico': 3200,
    'diagnostico': 3200,
    'seguimiento': 3200,
};

$(function() {
    inicializarContadores(limites); // Iniciar el contador de caracteres con los límites
    inicializarSpeechRecognition(limites); // Inicializar reconocimiento de voz con los límites
});

function inicializarContadores(limites) {
    Object.keys(limites).forEach(function(campo) {
        $('#' + campo).on('input', function() {
            actualizarCaracteres(campo, 'charNum_' + campo, limites[campo]);
        });

        // Para inicializar el contador cuando se carga la página
        actualizarCaracteres(campo, 'charNum_' + campo, limites[campo]);
    });
}

function actualizarCaracteres(campo, contadorId, max_chars) {
    var texto = $('#' + campo).val();
    
    // Verificar si el campo tiene un valor
    if (texto !== undefined) {
        var longitudTexto = texto.length;

        // Si se supera el límite de caracteres, cortar el texto al límite
        if (longitudTexto > max_chars) {
            $('#' + campo).val(texto.substring(0, max_chars));
            longitudTexto = max_chars;
        }

        $('#' + contadorId).text(longitudTexto + '/' + max_chars); // Muestra el número de caracteres y el límite
    } else {
        console.error('El campo con id ' + campo + ' no tiene un valor definido.');
    }
}

function inicializarSpeechRecognition(limites) {
    Object.keys(limites).forEach(function(campo) {
        // Ocultar los botones de parada al iniciar
        $('#formulario_atenciones #search_' + campo + '_stop').hide();

        // Inicializar el reconocimiento de voz
        var recognition = new webkitSpeechRecognition();
        recognition.continuous = true;
        recognition.lang = "es";

        // Evento al hacer clic en el botón de inicio de reconocimiento
        $('#formulario_atenciones #search_' + campo + '_start').on('click', function(event) {
            $('#formulario_atenciones #search_' + campo + '_start').hide();
            $('#formulario_atenciones #search_' + campo + '_stop').show();
            recognition.start();

            recognition.onresult = function(event) {
                var finalResult = '';
                var valor_anterior = $('#formulario_atenciones #' + campo).val();
                for (var i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalResult = event.results[i][0].transcript;

                        // Combinar texto anterior con el nuevo resultado, respetando el límite de caracteres
                        var nuevoTexto = valor_anterior + ' ' + finalResult;
                        if (nuevoTexto.length > limites[campo]) {
                            nuevoTexto = nuevoTexto.substring(0, limites[campo]);
                        }
                        $('#formulario_atenciones #' + campo).val(nuevoTexto);
                        actualizarCaracteres(campo, 'charNum_' + campo, limites[campo]);
                    }
                }
            };

            return false;
        });

        // Evento al hacer clic en el botón de detener reconocimiento
        $('#formulario_atenciones #search_' + campo + '_stop').on('click', function(event) {
            recognition.stop();
            $('#formulario_atenciones #search_' + campo + '_stop').hide();
            $('#formulario_atenciones #search_' + campo + '_start').show();
            return false;
        });
    });
}

//TANSITO ENVIADA
$('#formulario_transito_enviada #motivo').keyup(function() {
    var max_chars = 255;
    var chars = $(this).val().length;
    var diff = max_chars - chars;

    $('#formulario_transito_enviada #charNumMotivoTE').html(diff + ' Caracteres');

    if (diff == 0) {
        return false;
    }
});

//TRANSITO RECIBIDA
$('#formulario_transito_recibida #motivo').keyup(function() {
    var max_chars = 255;
    var chars = $(this).val().length;
    var diff = max_chars - chars;

    $('#formulario_transito_recibida #charNumMotivoTR').html(diff + ' Caracteres');

    if (diff == 0) {
        return false;
    }
});

//INICIO BUSQUEDA DE VALORES PARA EL PACIENTE, SEGUN EL PACIENTE SELECCIONADO
$(document).ready(function(e) {
    $('#formulario_atenciones #paciente_consulta').on('change', function() {
        if ($('#formulario_atenciones #paciente_consulta').val() != "" || $(
                '#formulario_atenciones #servicio').val() != "") {
            var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
            var pacientes_id = $('#formulario_atenciones #paciente_consulta').val();
            $.ajax({
                type: 'POST',
                url: url,
                data: 'pacientes_id=' + pacientes_id,
                success: function(data) {
                    var array = eval(data);
                    $('#formulario_atenciones #identidad').val(array[0]);
                    $('#formulario_atenciones #nombre').val(array[1]);
                    $('#formulario_atenciones #edad').val(array[2]);
                    $('#formulario_atenciones #procedencia').val(array[3]);
                    $('#formulario_atenciones #religion_id').val(array[4]);
                    $('#formulario_atenciones #religion_id').selectpicker('refresh');

                    $('#formulario_atenciones #profesion_id').val(array[5]);
                    $('#formulario_atenciones #profesion_id').selectpicker('refresh');

                    $('#formulario_atenciones #estado_civil').val(array[13]);
                    $('#formulario_atenciones #estado_civil').selectpicker('refresh');

                    $('#formulario_atenciones #paciente_consulta').val(array[6]);
                    $('#formulario_atenciones #antecedentes_medicos_no_psiquiatricos').val(
                        array[7]);
                    $('#formulario_atenciones #hospitaliaciones').val(array[8]);
                    $('#formulario_atenciones #cirugias').val(array[9]);
                    $('#formulario_atenciones #seguimiento_read').val(array[10]);
                    $('#formulario_atenciones #alergias').val(array[11]);
                    $('#formulario_atenciones #fecha_nac').val(array[12]);
                    $("#reg_atencion").attr('disabled', false);
                    return false;
                }
            });
            return false;
        } else {
            $('#formulario_atenciones')[0].reset();
            $("#reg_atencion").attr('disabled', true);
        }
    });
});
//FIN BUSQUEDA DE VALORES PARA EL PACIENTE, SEGUN EL PACIENTE SELECCIONADO

//INICIO TRANSITO USUARIO
$(document).ready(function(e) {
    $('#formulario_transito_enviada #paciente_te').on('change', function() {
        if ($('#formulario_transito_enviada #paciente_te').val() != "") {
            var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
            var pacientes_id = $('#formulario_transito_enviada #paciente_te').val();
            $.ajax({
                type: 'POST',
                url: url,
                data: 'pacientes_id=' + pacientes_id,
                success: function(data) {
                    var array = eval(data);
                    $('#formulario_transito_enviada #identidad').val(array[0]);
                }
            });
            return false;
        } else {
            $('#formulario_transito_enviada')[0].reset();
            $('#formulario_transito_enviada #pro').val("Registro");
            $("#reg_transitoe").attr('disabled', true);
        }
    });
});

$(document).ready(function(e) {
    $('#formulario_transito_recibida #paciente_tr').on('change', function() {
        if ($('#formulario_transito_recibida #paciente_tr').val() != "") {
            var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/buscar_expediente.php';
            var pacientes_id = $('#formulario_transito_recibida #paciente_tr').val();
            $.ajax({
                type: 'POST',
                url: url,
                data: 'pacientes_id=' + pacientes_id,
                success: function(data) {
                    var array = eval(data);
                    $('#formulario_transito_recibida #identidad').val(array[0]);
                }
            });
            return false;
        } else {
            $('#formulario_transito_recibida')[0].reset();
            $('#formulario_transito_recibida #pro').val("Registro");
            $("#reg_transitor").attr('disabled', true);
        }
    });
});


$('#reg_transitoe').on('click', function(
    e) { // add event submit We don't want this to act as a link so cancel the link action
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
        getUsuarioSistema() == 5) {
        if ($('#formulario_transito_enviada #expediente').val() == "" && $(
                '#formulario_transito_enviada #motivo').val() == "" && $(
                '#formulario_agregar_referencias_recibidas #enviadaa').val() == "") {
            $('#formulario_transito_enviada')[0].reset();
            swal({
                title: 'Error',
                text: 'No se pueden enviar los datos, los campos estan vacíos',
                icon: 'error',
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        } else {
            e.preventDefault();
            agregarTransitoEnviadas();
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
        });
    }
});

$('#reg_transitor').on('click', function(
    e) { // add event submit We don't want this to act as a link so cancel the link action
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 ||
        getUsuarioSistema() == 5) {
        if ($('#formulario_transito_recibida #expediente').val() == "" && $(
                '#formulario_transito_recibida #motivo').val() == "" && $(
                '#formulario_agregar_referencias_recibidas #enviadaa').val() == "") {
            $('#formulario_transito_recibida')[0].reset();
            swal({
                title: 'Error',
                text: 'No se pueden enviar los datos, los campos estan vacíos',
                icon: 'error',
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        } else {
            e.preventDefault();
            agregarTransitoRecibidas();
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            icon: "error",
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
        });
    }
});
//FIN TRANSITO USUARIOS

//INICIO CAMBIAR PORCENTAJE SEGUN EL DESCUENTO SELECCIONADO
$(document).ready(function() {
    $('#formulario_metodoPago #descuento').on('change', function() {
        var descuento_id = $('#formulario_metodoPago #descuento').val();
        var agenda_id = $('#formulario_metodoPago #agenda_id').val();
        var tipo_tarifa = $('#formulario_metodoPago #tipo_tarifa').val();
        var porcentaje = getPorcentaje(descuento_id, agenda_id);
        var monto = getMonto(getColaborador_id(), agenda_id, tipo_tarifa);
        var neto = getNetoCobrar(monto, porcentaje);

        $('#formulario_metodoPago #porcentaje').val(porcentaje);
        $('#formulario_metodoPago #neto').val(neto);
    });
});

$(document).ready(function() {
    $('#formulario_metodoPago #tipo_tarifa').on('change', function() {
        var descuento_id = $('#formulario_metodoPago #descuento').val();
        var agenda_id = $('#formulario_metodoPago #agenda_id').val();
        var tipo_tarifa = $('#formulario_metodoPago #tipo_tarifa').val();

        var monto = getMonto(getColaborador_id(), agenda_id, tipo_tarifa);
        var neto = getNetoCobrar(monto, porcentaje);

        $('#formulario_metodoPago #monto').val(monto);
    });
});

$(document).ready(function() {
    $('#formulario_metodoPago #porcentaje').on('keyup', function() {
        var descuento_id = '';

        if ($('#formulario_metodoPago #descuento').val() == "" || $('#formulario_metodoPago #descuento')
            .val() == null) {
            swal({
                title: "Error",
                text: "Por favor seleccione un tipo de descuento antes de continuar",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            $('#formulario_metodoPago #descuento').focus();
        } else {
            var porcentaje = $('#formulario_metodoPago #porcentaje').val();
            var descuento_id = $('#formulario_metodoPago #descuento').val();
            var agenda_id = $('#formulario_metodoPago #agenda_id').val();
            var tipo_tarifa = $('#formulario_metodoPago #tipo_tarifa').val();
            var monto = getMonto(getColaborador_id(), agenda_id, tipo_tarifa);
            var neto = getNetoCobrar(monto, porcentaje);

            if (porcentaje != "") {
                $('#formulario_metodoPago #porcentaje').val(porcentaje);
                $('#formulario_metodoPago #neto').val(neto);
            } else {
                $('#formulario_metodoPago #porcentaje').val(0);
                $('#formulario_metodoPago #neto').val(monto);
            }
        }
    });
});
//FIN CAMBIAR PORCENTAJE SEGUN EL DESCUENTO SELECCIONADO

//INICIO PAGINACION DE HISTORIAL DE ATENCIONES
function paginationBusqueda(partida) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar_buscar.php';

    if ($('#formulario_buscarAtencion #busqueda').val() == "" || $('#formulario_buscarAtencion #busqueda').val() ==
        null) {
        dato = '';
    } else {
        dato = $('#formulario_buscarAtencion #busqueda').val();
    }

    $.ajax({
        type: 'POST',
        url: url,
        async: true,
        data: 'partida=' + partida + '&dato=' + dato,
        success: function(data) {
            var array = eval(data);
            $('#formulario_buscarAtencion #agrega_registros_busqueda').html(array[0]);
            $('#formulario_buscarAtencion #pagination_busqueda').html(array[1]);
        }
    });
    return false;
}
//FIN PAGINACION DE HISTORIAL DE ATENCIONES

//CONSULTAMOS TODAS LAS HISTORIAS CLINICAS DE ESTE USUARIO
function detallesAtencion(pacientes_id) {
    $('#formulario_buscarAtencion #pacientes_id').val(pacientes_id);
    paginarSeguimiento(1);
}

function paginarSeguimiento(partida) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar_historias_clinicas.php';

    var pacientes_id = $('#formulario_buscarAtencion #pacientes_id').val();

    $.ajax({
        type: 'POST',
        url: url,
        async: true,
        data: 'partida=' + partida + '&pacientes_id=' + pacientes_id,
        success: function(data) {
            var array = eval(data);
            $('#formulario_buscarAtencion #paciente_consulta').html('<b>Paciente:</b> ' + getNombrePaciente(
                pacientes_id));
            $('#formulario_buscarAtencion #agrega_registros_busqueda_').html(array[0]);
            $('#formulario_buscarAtencion #pagination_busqueda_').html(array[1]);
        }
    });
    return false;
}


//INICIO FUNCION PARA LIMPIAR EL FORMULARIO DE PACIENTES
function limpiarFormPacientes() {
    $('#formulario_atenciones #hospitaliaciones').val('');
    $('#formulario_atenciones #hospitaliaciones_read').val('');
    $('#formulario_atenciones #seguimiento').val('');
    $('#formulario_atenciones #seguimiento_read').val('');
    funcionesFormPacientes();
    $('#formulario_atenciones #pro').val('Registro');
}
//FIN FUNCION PARA LIMPIAR EL FORMULARIO DE PACIENTES

//INICIO FUNCION PARA LIMPIAR EL FORMULARIO DE PACIENTES
function limpiarFormMetodoPago() {
    funcionesMetodoPago();
    $('#formulario_metodoPago #pro').val('Registro');
    $("#formulario_metodoPago #reg").attr('disabled', true);
}
//FIN FUNCION PARA LIMPIAR EL FORMULARIO DE PACIENTES

//INICIO FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES ALMACENADOS EN LA AGENDA
function agregaRegistro() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarRegistro.php';

    var hoy = new Date();
    fecha_actual = convertDate(hoy);

    var pacientes_id = $('#formulario_atenciones #paciente_consulta').val();
    var fecha = $('#formulario_atenciones #fecha').val();
    var tipo_tarifa = $('#formulario_atenciones #tipo_tarifa').val();

    if (getMes(fecha) == 2) {
        swal({
            title: 'Error',
            text: 'No se puede agregar/modificar registros fuera de este periodo',
            icon: 'error',
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
        });
        return false;
    } else {
        if ($('#formulario_atenciones #fecha_nac').val() == fecha_actual || $('#formulario_atenciones #fecha_nac')
            .val() > fecha_actual) {
            swal({
                title: 'Error',
                text: 'Debe seleccionar una fecha de nacimiento válida',
                icon: 'error',
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: url,
                data: $('#formulario_atenciones').serialize(),
                success: function(registro) {
                    if (registro == 1) {
                        metodoPago(pacientes_id, getAgendaID(pacientes_id, fecha, getColaborador_id()),
                            tipo_tarifa);
                        $("#edi_atencion").attr('disabled', true);
                        $('#formulario_atenciones .nav-tabs li:eq(0) a').tab('show');
                        pagination(1);
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: "Error",
                            text: "Error al completar esta acción, no se puedo almacenar el registro",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Lo sentimos, este registro ya ha sido almacenado",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                        });
                        return false;
                    }
                }
            });
            return false;
        }
    }
}
//FIN FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES ALMACENADOS EN LA AGENDA

//INICIO FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES QUE NO ESTAN ALMACENADOS EN LA AGENDA
function agregar() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregar.php';

    var hoy = new Date();
    fecha_actual = convertDate(hoy);

    var pacientes_id = $('#formulario_atenciones #paciente_consulta').val();
    var fecha = $('#formulario_atenciones #fecha').val();
    var tipo_tarifa = $('#formulario_atenciones #tipo_tarifa').val();

    if (getMes(fecha) == 2) {
        swal({
            title: 'Error',
            text: 'No se puede agregar/modificar registros fuera de este periodo',
            icon: 'error',
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        });
        return false;
    } else {
        if ($('#formulario_atenciones #fecha_nac').val() == fecha_actual || $('#formulario_atenciones #fecha_nac')
            .val() > fecha_actual) {
            swal({
                title: 'Error',
                text: 'Debe seleccionar una fecha de nacimiento válida',
                icon: 'error',
                dangerMode: true
            });
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: url,
                data: $('#formulario_atenciones').serialize(),
                success: function(registro) {
                    if (registro == 1) {
                        metodoPago(pacientes_id, getAgendaID(pacientes_id, fecha, getColaborador_id()),
                            tipo_tarifa);
                        $("#reg_atencion").attr('disabled', true);
                        $('#formulario_atenciones .nav-tabs li:eq(0) a').tab('show');
                        pagination(1);
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: "Error",
                            text: "Error al completar esta acción, no se puedo almacenar el registro",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Lo sentimos, este registro ya ha sido almacenado",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                        });
                        return false;
                    } else if (registro == 4) {
                        swal({
                            title: "Error",
                            text: "Lo sentimos, este usuario ya se encuentra almacenado para este día, por favor verifique los registros de este paciente que aun está disponible en la agenda",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                        });
                        return false;
                    }
                }
            });
            return false;
        }
    }
}
//FIN FUNCION QUE GUARDA LOS REGISTROS DE PACIENTES QUE NO ESTAN ALMACENADOS EN LA AGENDA

//INICIO FUNCION QUE GUARDA EL METODO DE PAGO DEL PACIENTE
function agregarMetodoPago() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarMetodoPago.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: $('#formulario_metodoPago').serialize(),
        success: function(registro) {
            if (registro == 1) {
                $('#formulario_metodoPago')[0].reset();
                swal({
                    title: "Success",
                    text: "Registro almacenado correctamente",
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: "¡Bien Hecho!",
                        }
                    },
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                }).then((willConfirm) => {
                    if (willConfirm === true) {
                        $('#metodoPago').modal('hide');
                    }
                });

                limpiarFormMetodoPago();
                $("#formulario_metodoPago #reg").attr('disabled', true);
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al completar esta acción, no se puedo almacenar el registro",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Lo sentimos, este registro ya ha sido almacenado",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
                return false;
            }
        }
    });
    return false;
}
//FIN FUNCION QUE GUARDA EL METODO DE PAGO DEL PACIENTE

//INICIO TRANSITO DE PACIENTES
function agregarTransitoEnviadas() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarTransitoEnviadas.php';

    var fecha = $('#formulario_transito_enviada #fecha').val();
    var hoy = new Date();
    fecha_actual = convertDate(hoy);

    if (getMes(fecha) == 2) {
        swal({
            title: 'Error',
            text: 'No se puede agregar/modificar registros fuera de este periodo',
            icon: 'error',
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        });
        return false;
    } else {
        if (fecha <= fecha_actual) {
            $.ajax({
                type: 'POST',
                url: url,
                data: $('#formulario_transito_enviada').serialize(),
                success: function(registro) {
                    if (registro == 1) {
                        $('#formulario_transito_enviada')[0].reset();
                        $('#formulario_transito_enviada #pro').val('Registro');
                        swal({
                            title: 'Almacenado',
                            text: 'Registro almacenado correctamente',
                            icon: 'success',
                            timer: 3000,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                            
                        });
                        limpiarTE();
                        $('#registro_transito_eviada').modal('hide');
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: 'Error',
                            text: 'Error al intentar almacenar este registro',
                            icon: 'error',
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Este registro no cuenta con atencion almacenada",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else if (registro == 4) {
                        swal({
                            title: "Error",
                            text: "Este registro ya existe",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al completar el registro",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    }
                }
            });
        } else {
            swal({
                title: 'Error',
                text: 'No se puede agregar/modificar registros fuera de esta fecha',
                icon: 'error',
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        }
    }
}

function agregarTransitoRecibidas() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/agregarTransitoRecibidas.php';

    var fecha = $('#formulario_transito_recibida #fecha').val();
    var hoy = new Date();
    fecha_actual = convertDate(hoy);

    if (getMes(fecha) == 2) {
        swal({
            title: 'Error',
            text: 'No se puede agregar/modificar registros fuera de este periodo',
            icon: 'error',
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
        });
        return false;
    } else {
        if (fecha <= fecha_actual) {
            $.ajax({
                type: 'POST',
                url: url,
                data: $('#formulario_transito_recibida').serialize(),
                success: function(registro) {
                    if (registro == 1) {
                        $('#formulario_transito_recibida')[0].reset();
                        $('#pro').val('Registro');
                        swal({
                            title: 'Almacenado',
                            text: 'Registro almacenado correctamente',
                            icon: 'success',
                            timer: 3000,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                             
                        });
                        $('#registro_transito_recibida').modal('hide');
                        limpiarTR();
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: 'Error',
                            text: 'Error al intentar almacenar este registro',
                            icon: 'error',
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: 'Error',
                            text: 'Este registro no cuenta con atencion almacenada',
                            icon: 'error',
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else if (registro == 4) {
                        swal({
                            title: 'Error',
                            text: 'Este registro ya existe',
                            icon: 'error',
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else {
                        swal({
                            title: 'Error',
                            text: 'Error al completar el registro',
                            icon: 'error',
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    }
                }
            });
        } else {
            swal({
                title: 'Error',
                text: 'No se puede agregar/modificar registros fuera de esta fecha',
                icon: 'error',
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
            return false;
        }
    }
}
//FIN TRANSITO DE PACIENTES

//INICIO OBTENER EL AGENDA ID, DE LA ENTIDAD AGENDA DE PACIENTES
function getAgendaID(pacientes_id, fecha, servicio) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getAgendaID.php';
    var agenda_id;

    $.ajax({
        type: 'POST',
        url: url,
        async: false,
        data: 'pacientes_id=' + pacientes_id + '&fecha=' + fecha + '&servicio=' + servicio,
        success: function(data) {
            agenda_id = data;
        }
    });
    return agenda_id;
}
//FIN OBTENER EL AGENDA ID, DE LA ENTIDAD AGENDA DE PACIENTES

//INICIO AGRUPAR FUNCIONES DE PACIENTES
function funcionesFormPacientes() {
    getServicioTransito();
    getServicioAtencion();
    getEstado();
    getPacientes();
    getProfesion();
    getReligion();
    getEstadoCivl();
    getConsultorio();
    pagination(1);
}
//FIN AGRUPAR FUNCIONES DE PACIENTES

//INICIO OBTENER EL NOMBRE DEL PACIENTE
function getNombrePaciente(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getNombrePaciente.php';
    var paciente;
    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: false,
        success: function(data) {
            paciente = data;
        }
    });
    return paciente;
}
//FIN OBTENER EL NOMBRE DEL PACIENTE

//INICIO OBTENER EL MONTO QUE COBRA EL PROFESIONAL EN LOS SERVICIOS QUE ATIENDE
function getMonto(colaborador_id, agenda_id, tipo_tarifa) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getMonto.php';
    var monto;
    $.ajax({
        type: 'POST',
        url: url,
        data: 'colaborador_id=' + colaborador_id + '&agenda_id=' + agenda_id + '&tipo_tarifa=' + tipo_tarifa,
        async: false,
        success: function(data) {
            monto = data;
        }
    });
    return monto;
}
//FIN OBTENER EL MONTO QUE COBRA EL PROFESIONAL EN LOS SERVICIOS QUE ATIENDE

//INICIO OBTENER EL PORCENTAJE
function getPorcentaje(descuento_id, agenda_id) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getDescuentoPorcentaje.php';
    var porcentaje;
    $.ajax({
        type: 'POST',
        url: url,
        data: 'descuento_id=' + descuento_id + '&agenda_id=' + agenda_id,
        async: false,
        success: function(data) {
            porcentaje = data;
        }
    });
    return porcentaje;
}
//FIN OBTENER EL PORCENTAJE

//INICIO FUNCION NETO A COBRAR
function getNetoCobrar(monto, porcentaje) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getNetoCobrar.php';
    var monto;
    $.ajax({
        type: 'POST',
        url: url,
        data: 'monto=' + monto + '&porcentaje=' + porcentaje,
        async: false,
        success: function(data) {
            monto = data;
        }
    });
    return monto;
}
//FIN FUNCION NETO A COBRAR

//INICIO PARA OBTENER EL COLABORADOR_ID
function getColaborador_id() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getColaborador.php';
    var colaborador_id;
    $.ajax({
        type: 'POST',
        url: url,
        async: false,
        success: function(data) {
            colaborador_id = data;
        }
    });
    return colaborador_id;
}
//FIN PARA OBTENER EL COLABORADOR_ID

//INICIO PARA OBTENER EL SERVICIO DEL TRANSITO DE USUARIOS	
function getServicioTransito() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/servicios_transito.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_transito_enviada #servicio').html("");
            $('#formulario_transito_enviada #servicio').html(data);
            $('#formulario_transito_enviada #servicio').selectpicker('refresh');

            $('#formulario_transito_recibida #servicio').html("");
            $('#formulario_transito_recibida #servicio').html(data);
            $('#formulario_transito_recibida #servicio').selectpicker('refresh');
        }
    });
}
//FIN PARA OBTENER EL SERVICIO DEL TRANSITO DE USUARIOS


//INICIO FUNCION LIMPIAR TRANSITO
function limpiarTE() {
    getPacientes();
    getColaborador();
    $('#formulario_transito_enviada #pro').val("Registro");
    $('#formulario_transito_enviada #motivo').val("");
    $("#reg_transitoe").attr('disabled', false);
}

function limpiarTR() {
    getPacientes();
    getColaborador();
    $('#formulario_transito_recibida #pro').val("Registro");
    $('#formulario_transito_recibida #motivo').val("");
    $("#reg_transitor").attr('disabled', false);
}
//FIN FUNCION LIMPIAR TRANSITO

//INICIO FUNCION PARA OBTENER LOS PACIENTES
function getPacientes() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getPacientes.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_atenciones #paciente_consulta').html("");
            $('#formulario_atenciones #paciente_consulta').html(data);
            $('#formulario_atenciones #paciente_consulta').selectpicker('refresh');

            $('#formulario_transito_enviada #paciente_te').html("");
            $('#formulario_transito_enviada #paciente_te').html(data);
            $('#formulario_transito_enviada #paciente_te').selectpicker('refresh');

            $('#formulario_transito_recibida #paciente_tr').html("");
            $('#formulario_transito_recibida #paciente_tr').html(data);
            $('#formulario_transito_recibida #paciente_tr').selectpicker('refresh');

            $('#form_receta #receta_select_pacientes_id').html("");
            $('#form_receta #receta_select_pacientes_id').html(data);
            $('#form_receta #receta_select_pacientes_id').selectpicker('refresh');            
        }
    });
}
//FIN FUNCION PARA OBTENER LOS PACIENTES

//INICIO FUNCION PARA OBTENER LA RELIGION
function getReligion() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getReligion.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_atenciones #religion_id').html("");
            $('#formulario_atenciones #religion_id').html(data);
            $('#formulario_atenciones #religion_id').selectpicker('refresh');
        }
    });
}
//FIN FUNCION PARA OBTENER LOS PACIENTES

//INICIO FUNCION PARA OBTENER EL ESTADO CIVIL
function getEstadoCivl() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getEstadoCivil.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_atenciones #estado_civil').html("");
            $('#formulario_atenciones #estado_civil').html(data);
            $('#formulario_atenciones #estado_civil').selectpicker('refresh');
        }
    });
}
//FIN FUNCION PARA OBTENER EL ESTADO CIVIL

//INICIO FUNCION PARA OBTENER LA PROFESION
function getProfesion() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getProfesion.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_atenciones #profesion_id').html("");
            $('#formulario_atenciones #profesion_id').html(data);
            $('#formulario_atenciones #profesion_id').selectpicker('refresh');
        }
    });
}
//FIN FUNCION PARA OBTENER LOS PACIENTES

//INICIO PARA OBTENER EL SERVICIO DEL FORMULARIO DE PACIENTES
function getServicioAtencion(agenda_id) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/servicios.php';

    var servicio_id;
    $.ajax({
        type: 'POST',
        data: 'agenda_id=' + agenda_id,
        url: url,
        async: false,
        success: function(data) {
            servicio_id = data;
        }
    });
    return servicio_id;
}
//FIN PARA OBTENER EL SERVICIO DEL FORMULARIO DE PACIENTES

//INICIO PARA OBTENER EL ESTADO DE LOS PACIENTES (ATENDIDOS, AUSENTES)
function getEstado() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getEstado.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#form_main #estado').html("");
            $('#form_main #estado').html(data);
            $('#form_main #estado').selectpicker('refresh');
        }
    });
}
//FIN PARA OBTENER EL ESTADO DE LOS PACIENTES (ATENDIDOS, AUSENTES)

//INICIO PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL
function evaluarRegistrosPendientes() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/evaluarPendientes.php';
    var string = '';

    $.ajax({
        type: 'POST',
        data: 'fecha=' + fecha,
        url: url,
        success: function(valores) {
            var datos = eval(valores);
            if (datos[0] > 0) {
                if (datos[0] == 1 || datos[0] == 0) {
                    string = 'Registro pendiente';
                } else {
                    string = 'Registros pendientes';
                }

                swal({
                    title: 'Advertencia',
                    text: "Se le recuerda que tiene " + datos[0] + " " + string +
                        " de subir en las Atenciones Medicas en este mes de " + datos[1] +
                        ". Debe revisar sus registros pendientes.",
                    icon: 'warning',
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                });
            }

        }
    });
}
//FIN PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL

//INICIO PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL Y ENVIARLOS POR CORREO ELECTRONICO COMO RECORDATORIO
function evaluarRegistrosPendientesEmail() {
    var url = '<?php echo SERVERURL; ?>php/mail/evaluarPendientes_atencionesMedicas.php';

    $.ajax({
        type: 'POST',
        url: url,
        success: function(valores) {

        }
    });
}
//FIN PARA EVALUAR SI HAY REGISTROS PENDIENTES PARA EL PROFESIONAL Y ENVIARLOS POR CORREO ELECTRONICO COMO RECORDATORIO

function getConsultorio() {
    var url = '<?php echo SERVERURL; ?>php/citas/getServicioFacturas.php';

    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_atenciones #servicio_id').html("");
            $('#formulario_atenciones #servicio_id').html(data);
            $('#formulario_atenciones #servicio_id').selectpicker('refresh');          
        }
    });
    return false;
}

function getConsultorioReceta() {
    var url = '<?php echo SERVERURL; ?>php/citas/getServicioFacturas.php';

    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#form_receta #servicio_id_receta').html("");
            $('#form_receta #servicio_id_receta').html(data);
            $('#form_receta #servicio_id_receta').selectpicker('refresh');            
        }
    });
    return false;
}

$('#form_receta #servicio_id_receta').on('change', function() {
    var servicio_id_receta = $('#form_receta #servicio_id_receta').val();
    
    $('#form_receta #receta_servicioId').val(servicio_id_receta);
});

function convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-');
}

function getMes(fecha) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getMes.php';
    var resp;

    $.ajax({
        type: 'POST',
        data: 'fecha=' + fecha,
        url: url,
        async: false,
        success: function(data) {
            resp = data;
        }
    });
    return resp;
}

function consultarNombre(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';
    var resp;

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: false,
        success: function(data) {
            resp = data;
        }
    });
    return resp;
}

function consultarExpediente(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getExpedienteInformacion.php';
    var resp;

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: false,
        success: function(data) {
            resp = data;
        }
    });
    return resp;
}
/**********************************************************************************************************************************************************************************************/
/**********************************************************************************************************************************************************************************************/

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function() {
    $("#modal_busqueda_profesion").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_profesion #buscar').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_religion").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_religion #buscar').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_pacientes").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_pacientes #buscar').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$('#form_main #nueva_factura').on('click', function(e) {
    e.preventDefault();
    formFactura();
});

var accion = false;

function formFactura() {
    $('#formulario_facturacion')[0].reset();
    $('#main_facturacion').hide();
    $('.recetaMedica').hide();
    $('#facturacion').show();

    $('#label_acciones_volver').html("Volver");
    $('#acciones_atras').removeClass("active");
    $('#acciones_factura').addClass("active");
    $('#label_acciones_factura').html("Factura");

    // Actualizar el breadcrumb
    actualizarBreadcrumb("Atenciones Médicas / Factura");

    $('#formulario_facturacion #fecha').attr('readonly', true);
    $('#formulario_facturacion #colaborador_id').val(getColaborador_id());
    $('#formulario_facturacion #colaborador_id').selectpicker('refresh');

    $('#formulario_facturacion').attr({
        'data-form': 'save'
    });
    $('#formulario_facturacion').attr({
        'action': '<?php echo SERVERURL; ?>php/facturacion/addPreFactura.php'
    });
    limpiarTabla();
    $('.footer').hide();
    $('.footer1').show();
    $('#formulario_facturacion #validar').hide();
    $('#formulario_facturacion #guardar1').hide();

    accion = true;
}

function FormAtencionMedica() {
    $('#main_facturacion').hide();
    $('#facturacion').hide();
    $('.recetaMedica').hide();
    $('#atencionMedica').show();

    $('#label_acciones_volver').html("Volver");
    $('#acciones_atras').removeClass("active");
    $('#acciones_factura').addClass("active");
    $('#label_acciones_factura').html("Historia Clínica");

    // Actualizar el breadcrumb
    actualizarBreadcrumb("Atenciones Médicas / Receta Médica");

    accion = false;
}

var accion = false;

function formFactura() {
    $('#formulario_facturacion')[0].reset();
    $('#main_facturacion').hide();
    $('.recetaMedica').hide();
    $('#facturacion').show();

    $('#label_acciones_volver').html("Volver");
    $('#acciones_atras').removeClass("active");
    $('#acciones_factura').addClass("active");
    $('#label_acciones_factura').html("Factura");

    // Actualizar el breadcrumb
    actualizarBreadcrumb("Atenciones Médicas / Factura");

    $('#formulario_facturacion #fecha').attr('readonly', true);
    $('#formulario_facturacion #colaborador_id').val(getColaborador_id());
    $('#formulario_facturacion #colaborador_id').selectpicker('refresh');

    $('#formulario_facturacion').attr({
        'data-form': 'save'
    });
    $('#formulario_facturacion').attr({
        'action': '<?php echo SERVERURL; ?>php/facturacion/addPreFactura.php'
    });
    limpiarTabla();
    $('.footer').hide();
    $('.footer1').show();
    $('#formulario_facturacion #validar').hide();
    $('#formulario_facturacion #guardar1').hide();    

    accion = true;
}

function FormAtencionMedica() {
    $('#main_facturacion').hide();
    $('#facturacion').hide();
    $('#atencionMedica').show();
    $('.recetaMedica').hide();
    $('#label_acciones_volver').html("Volver");
    $('#acciones_atras').removeClass("active");
    $('#acciones_factura').addClass("active");
    $('#label_acciones_factura').html("Historia Clínica");

    // Actualizar el breadcrumb
    actualizarBreadcrumb("Atenciones Médicas");

    accion = false;
}

$("#nueva_receta_medica").on("click", (e) => {
    e.preventDefault();
    mostrarRecetaMedica("");
});

function mostrarRecetaMedica(pacientes_id, colaboradorId, servicioId, colaboradorNombre, pacienteNombre) {
    // Ocultar el elemento con ID main_facturacion
    $('#main_facturacion').hide();
    // Mostrar el elemento con clase recetaMedica
    $('.recetaMedica').show();

    // Actualizar el breadcrumb
    actualizarBreadcrumb("Atenciones Médicas / Receta Médica");

    if (pacientes_id != "") {
        // Si pacientes_id no está vacío, asignarlo al campo oculto y ocultar el select
        $('#form_receta #receta_pacientes_id').val(pacientes_id);
        $('#form_receta #receta_colaboradorId').val(colaboradorId);
        $('#form_receta #receta_servicioId').val(servicioId);
        $('#form_receta #receta_pacienteNombre').val(pacienteNombre);

        $('#form_receta #datos_paciente').show();
        $('#form_receta #datos_paciente').html("<b>Paciente:</b> " + pacienteNombre + " <b>Medico Tratante:</b> " + colaboradorNombre);

        $('#form_receta #receta_datos_generales').hide();
        $('#form_receta #grupo_paciente_receta').hide();
    } else {
        // Si pacientes_id está vacío, mostrar el select
        $('#form_receta #receta_pacientes_id').val('');
        $('#form_receta #grupo_paciente_receta').show();
        $('#form_receta #datos_paciente').hide();
    }

    // Limpiar todas las filas de la tabla
    $('#tablaReceta tbody').empty();

    // Añadir una nueva fila llamando a la función agregarFila
    agregarFila();

    // Log para depuración
    console.log("Paciente ID: " + (pacientes_id || "Seleccionar desde el select"));   
}

function actualizarBreadcrumb(texto) {
    $('#ancla_volver').text(texto.split(" / ")[0]); // Primera parte del breadcrumb
    $('#label_acciones_factura').text(texto.split(" / ")[1] || ""); // Segunda parte opcional
}

function volver() {
    $('#main_facturacion').show();
    $('#atencionMedica').hide();
    $('.recetaMedica').hide();
    $('#label_acciones_factura').html("");
    $('#facturacion').hide();
    $('#acciones_atras').addClass("breadcrumb-item active");
    $('#acciones_factura').removeClass("active");
    $('.footer').show();
    $('.footer1').hide();

    // Restablecer el breadcrumb al nivel inicial
    actualizarBreadcrumb("Atenciones Médicas");
}

$('#acciones_atras').on('click', function(e) {

     // Comprobación de campos específicos en el formulario de facturación
     if ($('#formulario_facturacion #pacientes_id').val() !== "" && 
        $('#formulario_facturacion #colaborador_id').val() !== "") {

        let formData;
        let title;
        let message;

        // Función para convertir una cadena URL-encoded a un objeto
        function serializeToObject(serializedString) {
            const obj = {};
            const pairs = serializedString.split('&');
            pairs.forEach(function(pair) {
                const [key, value] = pair.split('=');
                if (key) {
                    obj[decodeURIComponent(key)] = decodeURIComponent(value || '');
                }
            });
            return obj;
        }

        // Serialización del formulario según la variable 'accion'
        if (accion) {
            formData = serializeToObject($('#formulario_facturacion').serialize());
            title = "Tiene datos en la factura";
            message =
                "¿Está seguro que desea volver? Recuerde que tiene información en la factura que perderá.";
        } else {
            formData = serializeToObject($('#formulario_atenciones').serialize());
            title = "Tiene datos en la historia clínica";
            message =
                "¿Está seguro que desea volver? Recuerde que tiene información en la historia clínica que perderá.";
        }

        // Lista de campos a excluir
        const camposAExcluir = [
            'fecha',
            'agenda_id',
            'pacientes_id',
            'colaborador_id',
            'paciente_consulta',
            'edad',
            'pro',
            'servicio_id'
        ];

        // Eliminar los campos especificados
        camposAExcluir.forEach(campo => {
            if (campo in formData) {
                delete formData[campo];
            }
        });

        // Imprimir el contenido de formData después de excluir campos
        console.log('Contenido de formData después de excluir campos:', formData);

        // Verificar si hay datos relevantes en el formulario
        const tieneDatos = Object.keys(formData).some(key => {
            const valor = formData[key];
            // Chequear si el valor no es vacío, "0", null, o "undefined"
            return valor.trim() !== "" && valor !== "0" && valor !== "null" && valor !== "undefined";
        });

        console.log('¿Tiene datos relevantes?:', tieneDatos);

        if (tieneDatos) {
            // Mostrar SweetAlert si el formulario tiene datos relevantes
            swal({
                title: title,
                text: message,
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancelar",
                        visible: true
                    },
                    confirm: {
                        text: "¡Si, deseo volver!",
                    }
                },
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            }).then((willConfirm) => {
                if (willConfirm === true) {
                    $('#main_facturacion').show();
                    $('#atencionMedica').hide();
                    $('.recetaMedica').hide();
                    $('#label_acciones_factura').html("");
                    $('#facturacion').hide();
                    $('#acciones_atras').addClass("breadcrumb-item active");
                    $('#acciones_factura').removeClass("active");
                    $('#formulario_facturacion')[0].reset();
                    $('.footer').show();
                    $('.footer1').hide();
                }
            });
        } else {
            // Lógica para cuando no hay datos relevantes
            $('#main_facturacion').show();
            $('#atencionMedica').hide();
            $('.recetaMedica').hide();
            $('#label_acciones_factura').html("");
            $('#facturacion').hide();
            $('#acciones_atras').addClass("breadcrumb-item active");
            $('#acciones_factura').removeClass("active");
            $('.footer').show();
            $('.footer1').hide();
        }
    } else {
        // Lógica para cuando no hay datos en los campos de nombre
        $('#main_facturacion').show();
        $('#atencionMedica').show();
        $('#label_acciones_factura').html("");
        $('#facturacion').hide();
        $('#atencionMedica').hide();
        $('.recetaMedica').hide();
        $('#acciones_atras').addClass("breadcrumb-item active");
        $('#acciones_factura').removeClass("active");
        $('.footer').show();
        $('.footer1').hide();
    }
});

function getProfesional() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getProfeisonal.php';
    var profesional
    $.ajax({
        type: 'POST',
        url: url,
        async: false,
        success: function(data) {
            profesional = data;
        }
    });
    return profesional;
}

function showFactura(atencion_id) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/editarFactura.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'atencion_id=' + atencion_id,
        success: function(data) {
            var datos = eval(data);
            $('#formulario_facturacion')[0].reset();
            $('#formulario_facturacion #pro').val("Registro");
            $('#formulario_facturacion #pacientes_id').val(datos[0]);
            $('#formulario_facturacion #pacientes_id').selectpicker('refresh');

            $('#formulario_facturacion #fecha').val(getFechaActual());
            $('#formulario_facturacion #colaborador_id').val(datos[3]);
            $('#formulario_facturacion #colaborador_id').selectpicker('refresh');

            $('#formulario_facturacion #servicio_id').val(datos[5]);
            $('#formulario_facturacion #servicio_id').selectpicker('refresh');

            $('#label_acciones_volver').html("ATA");
            $('#label_acciones_receta').html("Receta");

            $('#formulario_facturacion #fecha').attr("readonly", true);
            $('#formulario_facturacion #validar').attr("disabled", false);
            $('#formulario_facturacion #addRows').attr("disabled", false);
            $('#formulario_facturacion #removeRows').attr("disabled", false);
            $('#formulario_facturacion #validar').show();
            $('#formulario_facturacion #editar').hide();
            $('#formulario_facturacion #eliminar').hide();
            limpiarTabla();

            $('#main_facturacion').hide();
            $('#atencionMedica').hide();
            $('#facturacion').show();

            $('#formulario_facturacion').attr({
                'data-form': 'save'
            });
            $('#formulario_facturacion').attr({
                'action': '<?php echo SERVERURL; ?>php/facturacion/addPreFactura.php'
            });

            $('#formulario_facturacion #validar').hide();
            $('#formulario_facturacion #guardar1').hide();

            $('.footer').hide();
            $('.footer1').show();

            cleanFooterValueBill();
        }
    });
}

$(document).ready(function() {
    $('#formulario_atenciones #fecha_nac').on('change', function() {
        var fecha_nac = $('#formulario_atenciones #fecha_nac').val();
        var url = '<?php echo SERVERURL; ?>php/pacientes/getEdad.php';

        $.ajax({
            type: "POST",
            url: url,
            async: true,
            data: 'fecha_nac=' + fecha_nac,
            success: function(data) {
                var array = eval(data);
                $('#formulario_atenciones #edad').val(array[3]);
            }
        });
    });
});

function getFechaActual() {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/getFechaActual.php';
    var fecha_actual;

    $.ajax({
        type: 'POST',
        url: url,
        async: false,
        success: function(data) {
            fecha_actual = data;
        }
    });
    return fecha_actual;
}

//INICIO PAGINACION DE REGISTROS
function pagination(partida) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar.php';
    var fechai = $('#form_main #fecha_b').val();
    var fechaf = $('#form_main #fecha_f').val();
    var dato = $('#form_main #bs_regis').val() || '';
    var estado = $('#form_main #estado').val() || 0;

    function limpiarTexto(texto) {
        if (texto === null || texto === undefined || texto === '') {
            return '';
        }

        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function limpiarJS(texto) {
        if (texto === null || texto === undefined) {
            return '';
        }

        return String(texto)
            .replace(/\\/g, '\\\\')
            .replace(/'/g, "\\'")
            .replace(/"/g, '\\"')
            .replace(/\r/g, '')
            .replace(/\n/g, ' ');
    }

    function sinDato(texto) {
        return '<span class="badge px-3 py-2" style="border-radius:20px; color:#777; background:#f1f1f1; font-size:12px;">' +
            limpiarTexto(texto) +
        '</span>';
    }

    function identidadUI(valor) {
        if (!valor) {
            return sinDato('Sin identidad');
        }

        return '<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">' +
            '<i class="fas fa-id-card text-muted mr-1"></i> ' + limpiarTexto(valor) +
        '</span>';
    }

    function pacienteUI(valor) {
        if (!valor) {
            return sinDato('Sin paciente');
        }

        return '<div style="line-height:1.35;">' +
            '<div class="font-weight-bold text-dark">' +
                '<i class="fas fa-user text-info mr-1"></i> ' + limpiarTexto(valor) +
            '</div>' +
        '</div>';
    }

    function fechaUI(valor) {
        if (!valor) {
            return sinDato('Sin fecha');
        }

        return '<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">' +
            '<i class="fas fa-calendar-alt text-primary mr-1"></i> ' + limpiarTexto(valor) +
        '</span>';
    }

    function formatearHora(hora) {
        if (!hora) {
            return '';
        }

        hora = String(hora).trim();

        if (hora.toUpperCase().indexOf('AM') >= 0 || hora.toUpperCase().indexOf('PM') >= 0) {
            return hora;
        }

        var partes = hora.split(':');

        if (partes.length >= 2) {
            var h = parseInt(partes[0], 10);
            var m = partes[1];
            var periodo = h >= 12 ? 'PM' : 'AM';

            if (h === 0) {
                h = 12;
            } else if (h > 12) {
                h = h - 12;
            }

            return String(h).padStart(2, '0') + ':' + m + ' ' + periodo;
        }

        return hora;
    }

    function horaUI(valor) {
        if (!valor) {
            return sinDato('Sin hora');
        }

        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#8a5a00; background:#fff8e5; border:1px solid #f0ad4e;">' +
            '<i class="fas fa-clock mr-1"></i> ' + limpiarTexto(formatearHora(valor)) +
        '</span>';
    }

    function tipoPacienteUI(valor) {
        if (!valor) {
            return sinDato('Sin tipo');
        }

        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#005f73; background:#e9fbff; border:1px solid #9de2ef;">' +
            '<i class="fas fa-user-tag mr-1"></i> ' + limpiarTexto(valor) +
        '</span>';
    }

    function servicioUI(valor) {
        if (!valor) {
            return sinDato('Sin servicio');
        }

        return '<span class="text-dark">' +
            '<i class="fas fa-clinic-medical text-info mr-1"></i> ' + limpiarTexto(valor) +
        '</span>';
    }

    function telefonoUI(valor) {
        if (!valor) {
            return sinDato('Sin teléfono');
        }

        return '<a style="text-decoration:none;" title="Teléfono Usuario" href="tel:9' + limpiarTexto(valor) + '">' +
            '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#088143; background:#ecfff4; border:1px solid #b9ebcc;">' +
                '<i class="fas fa-phone mr-1"></i> ' + limpiarTexto(valor) +
            '</span>' +
        '</a>';
    }

    function observacionUI(valor) {
        if (!valor) {
            return sinDato('Sin observación');
        }

        return '<span class="text-dark">' +
            '<i class="fas fa-notes-medical text-primary mr-1"></i> ' + limpiarTexto(valor) +
        '</span>';
    }

    function comentarioUI(valor) {
        if (!valor) {
            return sinDato('Sin comentario');
        }

        return '<span class="text-dark">' +
            '<i class="fas fa-comment-medical text-secondary mr-1"></i> ' + limpiarTexto(valor) +
        '</span>';
    }

    function estadoUI(valor) {
        if (valor === 'Atendido') {
            return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#0b7a32; background:#ecfff4; border:1px solid #b9ebcc;">' +
                '<i class="fas fa-check-circle mr-1"></i> Atendido' +
            '</span>';
        }

        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#8a5a00; background:#fff8e5; border:1px solid #f0ad4e;">' +
            '<i class="fas fa-clock mr-1"></i> Pendiente' +
        '</span>';
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: {
            partida: partida,
            fechai: fechai,
            fechaf: fechaf,
            dato: dato,
            estado: estado
        },
        dataType: 'json',
        success: function(response) {
            var registros = response.registros || [];
            var pagination = response.pagination || '';
            var total = response.total || 0;

            var tabla = '' +
                '<div class="table-responsive">' +
                    '<table class="table table-striped table-hover mb-0" style="font-size:13px;">' +
                        '<thead>' +
                            '<tr style="background:#1297a5; color:#fff;">' +
                                '<th class="text-center align-middle py-3" width="4%">No.</th>' +
                                '<th class="text-center align-middle py-3" width="10%">Identidad</th>' +
                                '<th class="align-middle py-3" width="15%">Nombre</th>' +
                                '<th class="text-center align-middle py-3" width="8%">Fecha</th>' +
                                '<th class="text-center align-middle py-3" width="7%">Hora</th>' +
                                '<th class="text-center align-middle py-3" width="7%">Paciente</th>' +
                                '<th class="align-middle py-3" width="9%">Servicio</th>' +
                                '<th class="text-center align-middle py-3" width="9%">Teléfono</th>' +
                                '<th class="align-middle py-3" width="10%">Observación</th>' +
                                '<th class="align-middle py-3" width="10%">Comentario</th>' +
                                '<th class="text-center align-middle py-3" width="8%">Estado</th>' +
                                '<th class="text-center align-middle py-3" width="8%">Receta</th>' +
                                '<th class="text-center align-middle py-3" width="8%">Registrar</th>' +
                                '<th class="text-center align-middle py-3" width="8%">Ausencia</th>' +
                            '</tr>' +
                        '</thead>' +
                        '<tbody>';

            if (registros.length > 0) {
                registros.forEach(function(registro, index) {
                    tabla += '' +
                        '<tr style="height:58px;">' +
                            '<td class="text-center align-middle font-weight-bold py-3">' + (index + 1) + '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                identidadUI(registro.identidad) +
                            '</td>' +

                            '<td class="align-middle py-3">' +
                                pacienteUI(registro.paciente) +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                fechaUI(registro.fecha_cita) +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                horaUI(registro.hora) +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                tipoPacienteUI(registro.tipo_paciente) +
                            '</td>' +

                            '<td class="align-middle py-3">' +
                                servicioUI(registro.servicio) +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                telefonoUI(registro.telefono) +
                            '</td>' +

                            '<td class="align-middle py-3">' +
                                observacionUI(registro.observacion) +
                            '</td>' +

                            '<td class="align-middle py-3">' +
                                comentarioUI(registro.comentario) +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                estadoUI(registro.estatus) +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                '<a class="btn btn-secondary shadow-sm d-inline-flex align-items-center justify-content-center" ' +
                                   'style="border-radius:6px; padding:8px 14px; font-size:13px; min-width:95px; white-space:nowrap;" ' +
                                   'title="Receta Médica" ' +
                                   'href="javascript:mostrarRecetaMedica(' + registro.pacientes_id + ', ' + registro.colaborador_id + ', ' + registro.servicio_id + ', \'' + limpiarJS(registro.colaborador) + '\', \'' + limpiarJS(registro.paciente) + '\');void(0);">' +
                                    '<i class="fas fa-prescription-bottle-alt mr-2"></i> Receta' +
                                '</a>' +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                '<a class="btn btn-primary shadow-sm d-inline-flex align-items-center justify-content-center" ' +
                                   'style="border-radius:6px; padding:8px 14px; font-size:13px; min-width:105px; white-space:nowrap;" ' +
                                   'title="Agregar Atención a Paciente" ' +
                                   'href="javascript:editarRegistro(' + registro.pacientes_id + ',' + registro.agenda_id + ');void(0);">' +
                                    '<i class="fas fa-book-medical mr-2"></i> Atención' +
                                '</a>' +
                            '</td>' +

                            '<td class="text-center align-middle py-3">' +
                                '<a class="btn btn-danger shadow-sm d-inline-flex align-items-center justify-content-center" ' +
                                   'style="border-radius:6px; padding:8px 14px; font-size:13px; min-width:105px; white-space:nowrap;" ' +
                                   'title="Marcar Ausencia" ' +
                                   'href="javascript:nosePresentoRegistro(' + registro.pacientes_id + ',' + registro.agenda_id + ',' + registro.fecha + ');void(0);">' +
                                    '<i class="fas fa-times-circle mr-2"></i> Ausencia' +
                                '</a>' +
                            '</td>' +
                        '</tr>';
                });

                tabla += '' +
                    '<tr>' +
                        '<td colspan="14" class="text-center py-4">' +
                            '<span class="badge badge-light border px-4 py-2" style="font-size:14px; border-radius:20px;">' +
                                '<i class="fas fa-clipboard-check text-info mr-1"></i> ' +
                                'Total de Registros Encontrados: <strong>' + total + '</strong>' +
                            '</span>' +
                        '</td>' +
                    '</tr>';
            } else {
                tabla += '' +
                    '<tr>' +
                        '<td colspan="14" class="text-center py-5">' +
                            '<div class="text-danger font-weight-bold" style="font-size:15px;">' +
                                '<i class="fas fa-search mr-2"></i> No se encontraron resultados' +
                            '</div>' +
                            '<div class="text-muted mt-1">' +
                                'Intente buscar por expediente, nombre o identidad.' +
                            '</div>' +
                        '</td>' +
                    '</tr>';
            }

            tabla += '' +
                        '</tbody>' +
                    '</table>' +
                '</div>';

            $('#agrega-registros').html(tabla);
            $('#pagination').html(pagination);
        },
        error: function() {
            swal({
                title: "Error",
                text: "Ocurrió un error al obtener los datos",
                icon: "error",
                button: "Aceptar",
                type: "error",
                dangerMode: true,
                closeOnEsc: false,
                closeOnClickOutside: false
            });
        }
    });

    return false;
}
//FIN PAGINACION DE REGISTROS

//INICIO RECETA MEDICA
// Función para agregar fila dinámicamente
//INICIO RECETA MEDICA
const agregarFila = () => {
    const nuevaFila = $(`
        <tr>
            <td style="width: 40%;">
                <div class="mb-2">
                    <select class="form-select selectpicker producto producto-select" 
                        name="producto[]" 
                        data-size="7" 
                        data-width="100%" 
                        data-live-search="true" 
                        title="Seleccione un producto">
                    </select>
                </div>

                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input producto-manual-check" id="manual_${Date.now()}_${Math.floor(Math.random() * 1000)}">
                    <label class="form-check-label">Medicamento manual</label>
                </div>

                <div>
                    <input type="text" class="form-control producto-manual-input" name="producto_manual[]" placeholder="Escriba el medicamento" disabled />
                </div>
            </td>

            <td style="width: 15%;">
                <input type="number" class="form-control cantidad" name="cantidad[]" placeholder="Cantidad" required step="0.01" min="0.01" />
            </td>

            <td style="width: 35%;">
                <input type="text" class="form-control" name="descripcion[]" placeholder="Descripción" required />
            </td>

            <td style="width: 10%;">
                <button type="button" class="btn btn-danger eliminarFila">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </td>
        </tr>`);

    $('#tablaReceta tbody').append(nuevaFila);

    obtenerProductos(nuevaFila.find('.selectpicker'));

    nuevaFila.find('.cantidad').on('input', function () {
        let valor = $(this).val();
        if (/^\d+\.\d{3,}$/.test(valor)) {
            $(this).val(valor.slice(0, valor.indexOf('.') + 3));
        }
    });
};

// Función para obtener productos y llenar el select
const obtenerProductos = (selectElement) => {
    $.ajax({
        url: '<?php echo SERVERURL; ?>php/atencion_pacientes/obtener_productos.php',
        type: 'GET',
        dataType: 'json',
        success: (productos) => {
            selectElement.empty();
            selectElement.append('<option value="">Seleccione un producto</option>');

            productos.forEach(producto => {
                const option = `<option value="${producto.productos_id}">${producto.nombre}</option>`;
                selectElement.append(option);
            });

            selectElement.selectpicker('refresh');
        },
        error: () => {
            swal({
                title: "Error",
                text: "Ocurrió un error al obtener los productos",
                icon: "error",
                button: "Aceptar",
                type: "error",
                dangerMode: true,
                closeOnEsc: false,
                closeOnClickOutside: false
            });
        }
    });
};

$(document).on('change', '.producto-manual-check', function () {
    const fila = $(this).closest('tr');
    const esManual = $(this).is(':checked');
    const selectProducto = fila.find('.producto-select');
    const inputManual = fila.find('.producto-manual-input');

    if (esManual) {
        selectProducto.val('');
        selectProducto.prop('disabled', true);
        inputManual.prop('disabled', false).focus();
    } else {
        inputManual.val('');
        inputManual.prop('disabled', true);
        selectProducto.prop('disabled', false);
    }

    selectProducto.selectpicker('refresh');
});

// Inicializar selectpicker de Bootstrap solo para los select de productos
$('.selectpicker.producto').selectpicker();

// Llenar el select de productos al cargar la página
obtenerProductos($('.selectpicker.producto'));

getPacientes();
getConsultorioReceta();

// Evento para agregar fila
$('#agregarFila').on('click', () => {
    agregarFila();
});

// Eliminar fila dinámica
$(document).on('click', '.eliminarFila', function () {
    $(this).closest('tr').remove();
});

// Agregar fila al presionar Enter en el campo de descripción
$(document).on('keypress', 'input[name="descripcion[]"]', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        agregarFila();
    }
});

function registarReceta() {
    const selectPaciente = $('#form_receta #receta_select_pacientes_id').val();

    if (selectPaciente) {
        $('#form_receta #receta_pacientes_id').val(selectPaciente);
    }

    let formularioValido = true;
    let totalFilas = $('#tablaReceta tbody tr').length;

    if (totalFilas === 0) {
        swal({
            title: "Error",
            text: "Debe agregar al menos un medicamento a la receta",
            icon: "error",
            button: "Aceptar",
            dangerMode: true,
            closeOnEsc: false,
            closeOnClickOutside: false
        });
        return;
    }

    $('#tablaReceta tbody tr').each(function () {
        const fila = $(this);
        const esManual = fila.find('.producto-manual-check').is(':checked');
        const producto = fila.find('select[name="producto[]"]').val();
        const productoManual = fila.find('input[name="producto_manual[]"]').val().trim();
        const cantidad = fila.find('input[name="cantidad[]"]').val();
        const descripcion = fila.find('input[name="descripcion[]"]').val().trim();

        if (!cantidad || !descripcion) {
            formularioValido = false;
            swal({
                title: "Error",
                text: "Por favor, complete cantidad y descripción en todas las filas",
                icon: "error",
                button: "Aceptar",
                dangerMode: true,
                closeOnEsc: false,
                closeOnClickOutside: false
            });
            return false;
        }

        if (esManual) {
            if (!productoManual) {
                formularioValido = false;
                swal({
                    title: "Error",
                    text: "Debe escribir el nombre del medicamento manual",
                    icon: "error",
                    button: "Aceptar",
                    dangerMode: true,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });
                return false;
            }
        } else {
            if (!producto || producto === 'undefined') {
                formularioValido = false;
                swal({
                    title: "Error",
                    text: "Debe seleccionar un producto de la lista o marcar medicamento manual",
                    icon: "error",
                    button: "Aceptar",
                    dangerMode: true,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });
                return false;
            }
        }
    });

    if (!formularioValido) {
        return;
    }

    const datosReceta = $('#form_receta').serialize();

    $.ajax({
        url: '<?php echo SERVERURL; ?>php/atencion_pacientes/guardar_receta.php',
        type: 'POST',
        data: datosReceta,
        dataType: 'json',
        success: (respuesta) => {
            if (respuesta.status === "success") {
                swal({
                    title: "Éxito",
                    text: respuesta.message,
                    icon: "success",
                    button: "Aceptar",
                    type: "success",
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });

                volver();
                getRecetaReporte(respuesta.receta_id);
            } else {
                swal({
                    title: "Error",
                    text: respuesta.message,
                    icon: "error",
                    button: "Aceptar",
                    type: "error",
                    dangerMode: true,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });
            }
        },
        error: () => {
            swal({
                title: "Error",
                text: "Ocurrió un error al guardar la receta",
                icon: "error",
                button: "Aceptar",
                type: "error",
                dangerMode: true,
                closeOnEsc: false,
                closeOnClickOutside: false
            });
        }
    });
}

// Guardar receta con AJAX
$('#form_receta').on('submit', (e) => {
    e.preventDefault();
    var pacienteNombre = $("#form_receta #receta_pacienteNombre").val();
    var pacienteId = $("#receta_select_pacientes_id").val();
    var servicioId = $("#servicio_id_receta").val();

        // Validar si los campos no están ocultos y están vacíos
    if (!$('#receta_select_pacientes_id').is(':hidden') && !pacienteId) {
        swal("Error", "Debes seleccionar un paciente.", "error");
        return;
    }

    if (!$('#servicio_id_receta').is(':hidden') && !servicioId) {
        swal("Error", "Debes seleccionar un servicio.", "error");
        return;
    }


    // Si está vacío, tomar el texto del select
    if (!pacienteNombre) {
        pacienteNombre = $("#receta_select_pacientes_id option:selected").text();
    }

	swal({
		title: "¿Estás seguro?",
		text: "¿Desea registrar la receta para el paciente: " + pacienteNombre + "?",
		icon: "info",
		buttons: {
			cancel: {
				text: "Cancelar",
				value: false,
				visible: true,
				className: "btn-info",
			},
			confirm: {
				text: "¡Sí, registrar la receta!",
				value: true,
				visible: true,
				className: "btn-primary",
				closeModal: false // Evita el cierre automático hasta completar la acción
			}
		},
        closeOnEsc: false, // Desactiva el cierre con la tecla Esc
        closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera         
	}).then((willRegister) => {
		if (willRegister) {
			registarReceta();
		}
	});  
});

function getRecetaReporte(receta_id) {
    // Añadir los parámetros al formulario
    var params = {
        "id": receta_id,
        "type": "Receta",
        "db": <?php echo DB; ?>
    };

    viewReport(params);	
}
//FIN RECETA MEDICA
</script>