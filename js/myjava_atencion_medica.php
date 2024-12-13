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
    $('#form_main #nuevo_registro').on('click', function() {
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
                type: "error",
                confirmButtonClass: 'btn-danger'
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
                type: "error",
                confirmButtonClass: 'btn-danger'
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
                type: "error",
                confirmButtonClass: 'btn-danger'
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
                type: "warning",
                confirmButtonClass: "btn-warning"
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
                type: "error",
                confirmButtonClass: 'btn-danger'
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
                type: "error",
                confirmButtonClass: 'btn-danger'
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
                    $('#formulario_atenciones #antecedentes_medicos_no_psiquiatricos').val(array[9]);
                    $('#formulario_atenciones #hospitaliaciones').val(array[10]);
                    $('#formulario_atenciones #cirugias').val(array[11]);
                    $('#formulario_atenciones #alergias').val(array[12]);
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
                    inicializarSpeechRecognition(
                    limites); // Inicializar reconocimiento de voz con los límites

                    FormAtencionMedica();

                    /*$('#modal_registro_atenciones').modal({
                        show: true,
                        keyboard: false,
                        backdrop: 'static'
                    });*/
                    return false;
                }
            });
            return false;
        } else {
            swal({
                title: "Error",
                text: "Lo sentimos, este registro ya existe, no se puede agregar nuevamente su atención",
                type: "error",
                confirmButtonClass: 'btn-danger'
            });
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            type: "error",
            confirmButtonClass: 'btn-danger'
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
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Comentario",
                cancelButtonText: "Cancelar",
                confirmButtonText: "¡Sí, remover el usuario!",
                confirmButtonClass: "btn-warning",
            }, function(inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("¡Necesita escribir algo!");
                    return false
                }
                eliminarRegistro(agenda_id, inputValue);
            });
        } else {
            swal({
                title: "Error",
                text: "Error al ejecutar esta acción, el usuario debe estar en estatus pendiente",
                type: "error",
                confirmButtonClass: 'btn-danger'
            });
        }
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            type: "error",
            confirmButtonClass: 'btn-danger'
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
                    type: "success",
                    timer: 3000, //timeOut for auto-close
                });
                pagination(1);
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al remover este registro",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Este registro ya tiene almacenada una ausencia",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "Error al ejecutar esta acción",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
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
    'alergias': 3200,
    'seguimiento': 3200,
    'antecedentes_medicos_psiquiatricos': 3200,
    'historia_gineco_obstetrica': 3200,
    'medicamentos_previos': 3200,
    'medicamentos_actuales': 3200,
    'legal': 3200,
    'sustancias': 3200,
    'rasgos_personalidad': 3200,
    'informacion_adicional': 3200,
    'pendientes': 3200,
    'diagnostico': 3200,
    'antecedentes_medicos_no_psiquiatricos': 3200,
    'hospitaliaciones': 3200,
    'cirugias': 3200
};

$(document).ready(function() {
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
    var longitudTexto = texto.length;

    // Si se supera el límite de caracteres, cortar el texto al límite
    if (longitudTexto > max_chars) {
        $('#' + campo).val(texto.substring(0, max_chars));
        longitudTexto = max_chars;
    }

    $('#' + contadorId).text(longitudTexto + '/' + max_chars); // Muestra el número de caracteres y el límite
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
                type: 'error',
                confirmButtonClass: 'btn-danger'
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
            type: "error",
            confirmButtonClass: 'btn-danger'
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
                type: 'error',
                confirmButtonClass: 'btn-danger'
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
            type: "error",
            confirmButtonClass: 'btn-danger'
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
                type: "error",
                confirmButtonClass: 'btn-danger'
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

//INICIO PAGINACION DE REGISTROS
function pagination(partida) {
    var url = '<?php echo SERVERURL; ?>php/atencion_pacientes/paginar.php';
    var fechai = $('#form_main #fecha_b').val();
    var fechaf = $('#form_main #fecha_f').val();
    var dato = '';
    var estado = '';

    if ($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null) {
        estado = 0;
    } else {
        estado = $('#form_main #estado').val();
    }

    if ($('#form_main #bs_regis').val() == "" || $('#form_main #bs_regis').val() == null) {
        dato = '';
    } else {
        dato = $('#form_main #bs_regis').val();
    }

    $.ajax({
        type: 'POST',
        url: url,
        async: true,
        data: 'partida=' + partida + '&fechai=' + fechai + '&fechaf=' + fechaf + '&dato=' + dato + '&estado=' +
            estado,
        success: function(data) {
            var array = eval(data);
            $('#agrega-registros').html(array[0]);
            $('#pagination').html(array[1]);
        }
    });
    return false;
}
//FIN PAGINACION DE REGISTROS

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
            type: 'error',
            confirmButtonClass: 'btn-danger'
        });
        return false;
    } else {
        if ($('#formulario_atenciones #fecha_nac').val() == fecha_actual || $('#formulario_atenciones #fecha_nac')
            .val() > fecha_actual) {
            swal({
                title: 'Error',
                text: 'Debe seleccionar una fecha de nacimiento válida',
                type: 'error',
                confirmButtonClass: 'btn-danger'
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
                            type: "error",
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Lo sentimos, este registro ya ha sido almacenado",
                            type: "error",
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
                            type: "error",
                            confirmButtonClass: 'btn-danger'
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
            type: 'error',
            confirmButtonClass: 'btn-danger'
        });
        return false;
    } else {
        if ($('#formulario_atenciones #fecha_nac').val() == fecha_actual || $('#formulario_atenciones #fecha_nac')
            .val() > fecha_actual) {
            swal({
                title: 'Error',
                text: 'Debe seleccionar una fecha de nacimiento válida',
                type: 'error',
                confirmButtonClass: 'btn-danger'
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
                            type: "error",
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Lo sentimos, este registro ya ha sido almacenado",
                            type: "error",
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else if (registro == 4) {
                        swal({
                            title: "Error",
                            text: "Lo sentimos, este usuario ya se encuentra almacenado para este día, por favor verifique los registros de este paciente que aun está disponible en la agenda",
                            type: "error",
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
                            type: "error",
                            confirmButtonClass: 'btn-danger'
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
                        type: "success",
                        confirmButtonText: "¡Bien Hecho!",
                        closeOnConfirm: false
                    },
                    function() {
                        $('#metodoPago').modal('hide');
                        swal.close();
                    });
                limpiarFormMetodoPago();
                $("#formulario_metodoPago #reg").attr('disabled', true);
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al completar esta acción, no se puedo almacenar el registro",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Lo sentimos, este registro ya ha sido almacenado",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "Error al procesar su solicitud, por favor intentelo de nuevo mas tarde",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
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
            type: 'error',
            confirmButtonClass: 'btn-danger'
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
                            type: 'success',
                            timer: 3000,
                        });
                        limpiarTE();
                        $('#registro_transito_eviada').modal('hide');
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: 'Error',
                            text: 'Error al intentar almacenar este registro',
                            type: 'error',
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Este registro no cuenta con atencion almacenada",
                            type: "error",
                            confirmButtonClass: "btn-danger"
                        });
                        return false;
                    } else if (registro == 4) {
                        swal({
                            title: "Error",
                            text: "Este registro ya existe",
                            type: "error",
                            confirmButtonClass: "btn-danger"
                        });
                        return false;
                    } else {
                        swal({
                            title: "Error",
                            text: "Error al completar el registro",
                            type: "error",
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    }
                }
            });
        } else {
            swal({
                title: 'Error',
                text: 'No se puede agregar/modificar registros fuera de esta fecha',
                type: 'error',
                confirmButtonClass: 'btn-danger'
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
            type: 'error',
            confirmButtonClass: 'btn-danger'
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
                            type: 'success',
                            timer: 3000,
                        });
                        $('#registro_transito_recibida').modal('hide');
                        limpiarTR();
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: 'Error',
                            text: 'Error al intentar almacenar este registro',
                            type: 'error',
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: 'Error',
                            text: 'Este registro no cuenta con atencion almacenada',
                            type: 'error',
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else if (registro == 4) {
                        swal({
                            title: 'Error',
                            text: 'Este registro ya existe',
                            type: 'error',
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    } else {
                        swal({
                            title: 'Error',
                            text: 'Error al completar el registro',
                            type: 'error',
                            confirmButtonClass: 'btn-danger'
                        });
                        return false;
                    }
                }
            });
        } else {
            swal({
                title: 'Error',
                text: 'No se puede agregar/modificar registros fuera de esta fecha',
                type: 'error',
                confirmButtonClass: 'btn-danger'
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
                    type: 'warning',
                    confirmButtonClass: 'btn-warning'
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
    $('#facturacion').show();

    $('#label_acciones_volver').html("Volver");
    $('#acciones_atras').removeClass("active");
    $('#acciones_factura').addClass("active");
    $('#label_acciones_factura').html("Factura");

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

    $('#label_acciones_volver').html("Volver");
    $('#acciones_atras').removeClass("active");
    $('#acciones_factura').addClass("active");
    $('#label_acciones_factura').html("Historia Clinica");

    accion = false;
}

function volver() {
    $('#main_facturacion').hide();
    $('#atencionMedica').hide();
    $('#label_acciones_factura').html("");
    $('#facturacion').hide();
    $('#acciones_atras').addClass("breadcrumb-item active");
    $('#acciones_factura').removeClass("active");
    $('.footer').show();
    $('.footer1').hide();
}

$('#acciones_atras').on('click', function(e) {
    e.preventDefault();

    // Comprobación de campos específicos en el formulario de facturación
    if ($('#formulario_facturacion #cliente_nombre').val() !== "" ||
        $('#formulario_facturacion #colaborador_nombre').val() !== "") {

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
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-warning",
                confirmButtonText: "¡Sí, deseo volver!",
                closeOnConfirm: false
            }, function() {
                $('#main_facturacion').show();
                $('#atencionMedica').hide();
                $('#label_acciones_factura').html("");
                $('#facturacion').hide();
                $('#acciones_atras').addClass("breadcrumb-item active");
                $('#acciones_factura').removeClass("active");
                $('#formulario_facturacion')[0].reset();
                swal.close();
                $('.footer').show();
                $('.footer1').hide();
            });
        } else {
            // Lógica para cuando no hay datos relevantes
            $('#main_facturacion').show();
            $('#atencionMedica').hide();
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
</script>