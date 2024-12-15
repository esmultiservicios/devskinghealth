<script>
$(document).ready(function() {
    getSexo();
    listar_pacientes();
    getStatus();
    getDepartamentos();
    getPais();
    getResponsable();
    getReferido();  

    $('#formulario_profesiones #profesionales_buscar').on('keyup', function() {
        paginationPorfesionales(1);
    });

    $('#form_main #estado').on('change', function() {
        listar_pacientes();
    });

    $('#formulario_agregar_expediente_manual #identidad_ususario_manual').on('keyup', function() {
        busquedaUsuarioManualIdentidad();
    });

    $('#formulario_agregar_expediente_manual #expediente_usuario_manual').on('keyup', function() {
        busquedaUsuarioManualExpediente();
    });
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function() {
    $("#modal_pacientes").on('shown.bs.modal', function() {
        $(this).find('#formulario_pacientes #name').focus();
    });
});

$(document).ready(function() {
    $("#modal_profesiones").on('shown.bs.modal', function() {
        $(this).find('#formulario_profesiones #profesionales_buscar').focus();
    });
});

$(document).ready(function() {
    $("#agregar_expediente_manual").on('shown.bs.modal', function() {
        $(this).find('#formulario_agregar_expediente_manual #identidad_ususario_manual').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$('#reg_manual').on('click', function(
    e) { // delete event clicked // We don't want this to act as a link so cancel the link action
    e.preventDefault();
    if ($('#formulario_agregar_expediente_manual #expediente_usuario_manual').val() != "" || $(
            '#formulario_agregar_expediente_manual #identidad_ususario_manual').val() != "") {
        registrarExpedienteManual();
    } else {
        swal({
            title: "Error",
            text: "Hay registros en blanco, por favor corregir",
            type: "error",
            confirmButtonClass: 'btn-danger'
        });
        return false;
    }
});

$('#convertir_manual').on('click', function(
    e) { // add event submit We don't want this to act as a link so cancel the link action
    e.preventDefault();
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 6) {
        convertirExpedientetoTemporal();
    } else {
        swal({
            title: 'Acceso Denegado',
            text: 'No tiene permisos para ejecutar esta acción',
            type: 'error',
            confirmButtonClass: 'btn-danger'
        });
    }
});

function modalPacientes(){
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 ||
        getUsuarioSistema() == 6) {
        $('#formulario_pacientes #reg').show();
        $('#formulario_pacientes #edi').hide();
        cleanPacientes();
        $('#formulario_pacientes #grupo_expediente').hide();
        $('#formulario_pacientes')[0].reset();
        $('#formulario_pacientes #pro').val('Registro');
        $("#formulario_pacientes #fecha").attr('readonly', false);
        $("#formulario_pacientes #identidad").attr('readonly', false);
        $("#formulario_pacientes #pais_id").val(1);
        $('#formulario_pacientes #validate').removeClass('bien_email');
        $('#formulario_pacientes #validate').removeClass('error_email');
        $("#formulario_pacientes #correo").css("border-color", "none");
        $('#formulario_pacientes #validate').html('');
        $('#formulario_pacientes').attr({
            'data-form': 'save'
        });
        $('#formulario_pacientes').attr({
            'action': '<?php echo SERVERURL; ?>php/pacientes/agregarPacientes.php'
        });
        $('#modal_pacientes').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
        return false;
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            type: "error",
            confirmButtonClass: 'btn-danger'
        });
    }    
}

function modalProfesion(){
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 ||
        getUsuarioSistema() == 6) {
        $('#formulario_profesiones #reg').show();
        $('#formulario_profesiones #edi').hide();
        $('#formulario_profesiones')[0].reset();
        $('#formulario_profesiones #proceso').val('Registro');
        paginationPorfesionales(1);
        $('#formulario_profesiones').attr({
            'data-form': 'save'
        });
        $('#formulario_profesiones').attr({
            'action': '<?php echo SERVERURL; ?>php/pacientes/agregar_profesional.php'
        });
        $('#modal_profesiones').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
        return false;
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            type: "error",
            confirmButtonClass: 'btn-danger'
        });
    }
}

function getStatus() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getStatus.php';

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

function getReferido() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getReferido.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #referido_id').html("");
            $('#formulario_pacientes #referido_id').html(data);
            $('#formulario_pacientes #referido_id').selectpicker('refresh');
        }
    });
}

function showExpediente(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getExpediente.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Error",
                    text: "Por favor intentelo de nuevo más tarde",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
            } else {
                $('#mensaje_show').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                $('#mensaje_mensaje_show').html(data);
                $('#bad').hide();
                $('#okay').show();
            }
        }
    });
}

function modal_eliminarProfesional(profesional_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
                title: "¿Estas seguro?",
                text: "¿Desea eliminar este registro?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-warning",
                confirmButtonText: "¡Sí, eliminar el registro!",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            },
            function() {
                eliminarProfesional(profesional_id);
            });
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            type: "error",
            confirmButtonClass: 'btn-danger'
        });
    }
}

function modal_eliminar(pacientes_id) {
    if (consultarExpediente(pacientes_id) != 0 && (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 ||
            getUsuarioSistema() == 5 || getUsuarioSistema() == 6)) {
        var nombre_usuario = consultarNombre(pacientes_id);
        var expediente_usuario = consultarExpediente(pacientes_id);
        var dato;

        if (expediente_usuario == 0) {
            dato = nombre_usuario;
        } else {
            dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
        }

        swal({
                title: "¿Estas seguro?",
                text: "¿Desea eliminar este registro: " + dato + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-warning",
                confirmButtonText: "¡Sí, eliminar el registro!",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            },
            function() {
                eliminarRegistro(pacientes_id);
            });
    } else if (consultarExpediente(pacientes_id) == 0 && (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 ||
            getUsuarioSistema() == 5 || getUsuarioSistema() == 6)) {
        var nombre_usuario = consultarNombre(pacientes_id);
        var expediente_usuario = consultarExpediente(pacientes_id);
        var dato;

        if (expediente_usuario == 0) {
            dato = nombre_usuario;
        } else {
            dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
        }

        swal({
                title: "¿Estas seguro?",
                text: "¿Desea eliminar este registro: " + dato + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-warning",
                confirmButtonText: "¡Sí, eliminar el registro!",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            },
            function() {
                eliminarRegistro(pacientes_id);
            });
    } else {
        swal({
            title: 'Acceso Denegado',
            text: 'No tiene permisos para ejecutar esta acción',
            type: 'error',
            confirmButtonClass: 'btn-danger'
        });
        return false;
    }
}

function cleanPacientes() {
    $('#formulario_pacientes #validate').removeClass('bien_email');
    $('#formulario_pacientes #validate').removeClass('error_email');
    $('#formulario_pacientes #validate').html('');
    $("#formulario #correo").css("border-color", "none");
}

function editarRegistro(pacientes_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6) {
        var url = '<?php echo SERVERURL; ?>php/pacientes/editar.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: 'pacientes_id=' + pacientes_id,
            success: function(valores) {
                var datos = eval(valores);
                $('#formulario_pacientes #reg').hide();
                $('#formulario_pacientes #edi').show();
                $('#formulario_pacientes #pro').val('Edición');
                $('#formulario_pacientes #grupo_expediente').show();
                $('#formulario_pacientes #pacientes_id').val(pacientes_id);
                $('#formulario_pacientes #name').val(datos[0]);
                $('#formulario_pacientes #lastname').val(datos[1]);
                $('#formulario_pacientes #telefono1').val(datos[2]);
                $('#formulario_pacientes #telefono2').val(datos[3]);
                $('#formulario_pacientes #sexo').val(datos[4]);
                $('#formulario_pacientes #sexo').selectpicker('refresh');
                $('#formulario_pacientes #correo').val(datos[5]);
                $('#formulario_pacientes #edad').val(datos[6]);
                $('#formulario_pacientes #expediente').val(datos[7]);
                $('#formulario_pacientes #direccion').val(datos[8]);
                $('#formulario_pacientes #fecha_nac').val(datos[9]);
                $('#formulario_pacientes #departamento_id').val(datos[10]);
                $('#formulario_pacientes #departamento_id').selectpicker('refresh');
                getMunicipioEditar(datos[10], datos[11]);
                $('#formulario_pacientes #pais_id').val(datos[12]);
                $('#formulario_pacientes #pais_id').selectpicker('refresh');
                $('#formulario_pacientes #responsable').val(datos[13]);
                $('#formulario_pacientes #responsable_id').val(datos[14]);
                $('#formulario_pacientes #responsable_id').selectpicker('refresh');
                $('#formulario_pacientes #referido_id').val(datos[15]);
                $('#formulario_pacientes #identidad').val(datos[16]);
                $("#formulario_pacientes #identidad").attr('readonly', true);
                $("#formulario_pacientes #fecha").attr('readonly', true);
                $("#formulario_pacientes #expediente").attr('disabled', true);
                $('#formulario_pacientes #validate').removeClass('bien_email');
                $('#formulario_pacientes #validate').removeClass('error_email');
                $("#formulario_pacientes #correo").css("border-color", "none");
                $('#formulario_pacientes #validate').html('');

                cleanPacientes();
                $('#formulario_pacientes').attr({
                    'data-form': 'update'
                });
                $('#formulario_pacientes').attr({
                    'action': '<?php echo SERVERURL; ?>php/pacientes/editarPacientes.php'
                });
                $('#modal_pacientes').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                return false;
            }
        });
    } else {
        swal({
            title: 'Acceso Denegado',
            text: 'No tiene permisos para ejecutar esta acción',
            type: 'error',
            confirmButtonClass: 'btn-danger'
        });
        return false;
    }
}

function eliminarProfesional(id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/eliminar_profesional.php';
    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        success: function(registro) {
            if (registro == 1) {
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    type: "success",
                    timer: 3000, //timeOut for auto-clos
                });
                paginationPorfesionales(1);
                $('#modal_profesiones').modal('hide');
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro, cuenta con información almacenada",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
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
    return false;
}

function eliminarRegistro(pacientes_id) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/eliminar.php';
    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + pacientes_id,
        success: function(registro) {
            if (registro == 1) {
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    type: "success",
                    timer: 3000, //timeOut for auto-clos
                });
                listar_pacientes();
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
                return false;
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar este registro, cuenta con información almacenada",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
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
    return false;
}

function convertirExpedientetoTemporal() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/convertirExpedienteTemporal.php';
    var pacientes_id = $('#formulario_agregar_expediente_manual #pacientes_id').val();

    $.ajax({
        type: "POST",
        url: url,
        data: 'pacientes_id=' + pacientes_id,
        async: true,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Usuario convertido",
                    text: "El usuario se ha convertido a temporal",
                    type: "success",
                    timer: 3000, //timeOut for auto-close
                });
                $('#agregar_expediente_manual').modal('hide');
                $('#formulario_agregar_expediente_manual #expediente_manual').val('TEMP');
                $('#formulario_agregar_expediente_manual #temporal').hide();
                $('#convertir_manual').hide();
                $('#reg_manual').show();
                listar_pacientes();
                return false;
            } else {
                swal({
                    title: "Error",
                    text: "No se puede procesar su solicitud",
                    type: "error",
                    confirmButtonClass: "btn-danger"
                });
                return false;
            }
        }
    });
}

function registrarExpedienteManual() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/agregarExpedienteManual.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: $('#formulario_agregar_expediente_manual').serialize(),
        success: function(registro) {
            if (registro == 1) {
                $('#formulario_agregar_expediente_manual #pro_manual').val('Registro');
                swal({
                    title: "Success",
                    text: "Registro completado correctamente",
                    type: "success",
                    timer: 3000, //timeOut for auto-clos
                });
                $('#agregar_expediente_manual').modal('hide');
                listar_pacientes();
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se pudo guardar el registro, por favor verifique la información",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Error al ejecutar esta acción",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
            } else if (registro == 4) {
                swal({
                    title: "Error",
                    text: "Error en los datos",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
            } else {
                swal({
                    title: "Error",
                    text: "Error al guardar el registro",
                    type: "error",
                    confirmButtonClass: 'btn-danger'
                });
            }
        }
    });
    return false;
}

function busquedaUsuarioManualIdentidad() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarIdentidad.php';

    var identidad = $('#formulario_agregar_expediente_manual #identidad_ususario_manual').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'identidad=' + identidad,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Error",
                    text: "Este numero de Identidad ya existe, por favor corriga el numero e intente nuevamente",
                    type: "error",
                    confirmButtonClass: "btn-danger"
                });
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', true);
                return false;
            } else {
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', false);
            }
        }
    });
}

function busquedaUsuarioManualExpediente() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/consultarExpediente.php';

    var expediente = $('#formulario_agregar_expediente_manual #expediente_usuario_manual').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'expediente=' + expediente,
        success: function(data) {
            if (data == 1) {
                swal({
                    title: "Error",
                    text: "Este numero de Expediente ya existe, por favor corriga el numero e intente nuevamente",
                    type: "error",
                    confirmButtonClass: "btn-danger"
                });
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', true);
                return false;
            } else {
                $("#formulario_agregar_expediente_manual #reg").attr('disabled', false);
            }
        }
    });
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

function modal_agregar_expediente_manual(id, expediente) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6) {
        $('#formulario_agregar_expediente_manual')[0].reset();
        var url = '<?php echo SERVERURL; ?>php/pacientes/buscarUsuario.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: 'id=' + id,
            success: function(valores) {
                var datos = eval(valores);
                if (expediente == 0) {
                    $("#formulario_agregar_expediente_manual #temporal").hide();
                } else {
                    $("#formulario_agregar_expediente_manual #temporal").show();
                }
                $("#formulario_agregar_expediente_manual #pacientes_id").val(id);
                $("#formulario_agregar_expediente_manual #expediente").val(expediente);
                $("#formulario_agregar_expediente_manual #name_manual").val(datos[0]);
                $("#formulario_agregar_expediente_manual #identidad_manual").val(datos[1]);
                $('#formulario_agregar_expediente_manual #sexo_manual').val(datos[2]);
                $('#formulario_agregar_expediente_manual #sexo_manual').selectpicker('refresh');
                $('#formulario_agregar_expediente_manual #sexo_manual').attr('disabled', true);
                $("#formulario_agregar_expediente_manual #fecha_manual").val(datos[3]);
                $("#formulario_agregar_expediente_manual #edad_manual").val(datos[6]);
                $("#formulario_agregar_expediente_manual #expediente_manual").val(datos[5]);
                $("#formulario_agregar_expediente_manual #edad_manual").show();
                $('#formulario_agregar_expediente_manual #pro').val('Registrar');
                $("#reg_manual").show();
                $("#convertir_manual").hide();
                $('#agregar_expediente_manual').modal({
                    show: true,
                    keyboard: false,
                    backdrop: 'static'
                });
                return false;
            }
        });
        return false;
    } else {
        swal({
            title: "Acceso Denegado",
            text: "No tiene permisos para ejecutar esta acción",
            type: "error",
            confirmButtonClass: 'btn-danger'
        });
    }
}

function modal_agregar_expediente(pacientes_id, expediente) {
    var nombre_usuario = consultarNombre(pacientes_id);
    var expediente_usuario = consultarExpediente(pacientes_id);
    var dato;

    if (expediente_usuario == 0) {
        dato = nombre_usuario;
    } else {
        dato = nombre_usuario + " (Expediente: " + expediente_usuario + ")";
    }

    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 5 || getUsuarioSistema() == 6) {
        if (expediente == "" || expediente == 0) {
            swal({
                    title: "¿Estas seguro?",
                    text: "¿Desea asignarle un número de expediente a este usuario:" + dato + "?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "¡Sí, Asignar el expediente!",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                },
                function() {
                    asignarExpedienteaRegistro(pacientes_id);
                });
        } else {
            swal({
                title: "Error",
                text: "Este usuario: " + dato + " ya tiene un expediente asignado",
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
        return false;
    }
}

function paginationPorfesionales(partida) {
    var url = '<?php echo SERVERURL; ?>php/pacientes/paginarProfesionales.php';
    var profesional = $('#formulario_profesiones #profesionales_buscar').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&profesional=' + profesional,
        success: function(data) {
            var array = eval(data);
            $('#agrega_registros_profesionales').html(array[0]);
            $('#pagination_profesionales').html(array[1]);
        }
    });
    return false;
}

function getSexo() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getSexo.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #sexo').html("");
            $('#formulario_pacientes #sexo').html(data);
            $('#formulario_pacientes #sexo').selectpicker('refresh');

            $('#formulario_agregar_expediente_manual #sexo_manual').html("");
            $('#formulario_agregar_expediente_manual #sexo_manual').html(data);
            $('#formulario_agregar_expediente_manual #sexo_manual').selectpicker('refresh');
        }
    });
}

/*INICIO AUTO COMPLETAR*/
/*INICIO SUGGESTION NOMBRE*/
$(document).ready(function() {
    $('#formulario #name').on('keyup', function() {
        if ($('#formulario #name').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_name').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #name').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_name').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_name').fadeIn(1000).html("");
            $('#formulario #suggestions_name').fadeOut(1000);
        }
    });
});

//OCULTAR EL SUGGESTION
$(document).ready(function() {
    $('#formulario #name').on('blur', function() {
        $('#formulario #suggestions_name').fadeOut(1000);
    });
});

$(document).ready(function() {
    $('#formulario #name').on('click', function() {
        if ($('#formulario #name').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_name').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #name').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_name').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_name').fadeIn(1000).html("");
            $('#formulario #suggestions_name').fadeOut(1000);
        }
    });
});
/*FIN SUGGESTION NOMBRE*/

/*INICIO SUGGESTION APELLIDO*/
$(document).ready(function() {
    $('#formulario #lastname').on('keyup', function() {
        if ($('#formulario #lastname').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_apellido').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #lastname').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_apellido').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_apellido').fadeIn(1000).html("");
            $('#formulario #suggestions_apellido').fadeOut(1000);
        }
    });
});

//OCULTAR EL SUGGESTION
$(document).ready(function() {
    $('#formulario #lastname').on('blur', function() {
        $('#formulario #suggestions_apellido').fadeOut(1000);
    });
});

$(document).ready(function() {
    $('#formulario #lastname').on('cli', function() {
        if ($('#formulario #lastname').val() != "") {
            var key = $(this).val();
            var dataString = 'key=' + key;
            var url = '<?php echo SERVERURL; ?>php/pacientes/autocompletarNombre.php';

            $.ajax({
                type: "POST",
                url: url,
                data: dataString,
                success: function(data) {
                    //Escribimos las sugerencias que nos manda la consulta
                    $('#formulario #suggestions_apellido').fadeIn(1000).html(data);
                    //Al hacer click en algua de las sugerencias
                    $('.suggest-element').on('click', function() {
                        //Obtenemos la id unica de la sugerencia pulsada
                        var id = $(this).attr('id');
                        //Editamos el valor del input con data de la sugerencia pulsada
                        $('#formulario #lastname').val($('#' + id).attr('data'));
                        //Hacemos desaparecer el resto de sugerencias
                        $('#formulario #suggestions_apellido').fadeOut(1000);
                        return false;
                    });
                }
            });
        } else {
            $('#formulario#suggestions_apellido').fadeIn(1000).html("");
            $('#formulario #suggestions_apellido').fadeOut(1000);
        }
    });
});
/*FIN SUGGESTION APELLIDO*/
/*FIN AUTO COMPLETAR*/

function convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-');
}

//SÍ
$(document).ready(function() {
    $('#formulario_agregar_expediente_manual #respuestasi').on('click', function() {
        $("#convertir_manual").show();
        $("#reg_manual").hide();
    });
});

//NO
$(document).ready(function() {
    $('#formulario_agregar_expediente_manual #respuestano').on('click', function() {
        $("#convertir_manual").hide();
        $("#reg_manual").show();
    });
});

function getResponsable() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getResponsable.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #responsable_id').html("");
            $('#formulario_pacientes #responsable_id').html(data);
            $('#formulario_pacientes #responsable_id').selectpicker('refresh');
        }
    });
}

function getDepartamentos() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getDepartamentos.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #departamento_id').html("");
            $('#formulario_pacientes #departamento_id').html(data);
            $('#formulario_pacientes #departamento_id').selectpicker('refresh');
        }
    });
}

function getMunicipio() {
    var url = '../php/pacientes/getMunicipio.php';

    var departamento_id = $('#formulario_pacientes #departamento_id').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'departamento_id=' + departamento_id,
        success: function(data) {
            $('#formulario_pacientes #municipio_id').html("");
            $('#formulario_pacientes #municipio_id').html(data);
            $('#formulario_pacientes #municipio_id').selectpicker('refresh');
        }
    });
}

$(document).ready(function() {
    $('#formulario_pacientes #departamento_id').on('change', function() {
        var url = '../php/pacientes/getMunicipio.php';

        var departamento_id = $('#formulario_pacientes #departamento_id').val();

        $.ajax({
            type: 'POST',
            url: url,
            data: 'departamento_id=' + departamento_id,
            success: function(data) {
                $('#formulario_pacientes #municipio_id').html("");
                $('#formulario_pacientes #municipio_id').html(data);
                $('#formulario_pacientes #municipio_id').selectpicker('refresh');
            }
        });
        return false;
    });
});

function getMunicipioEditar(departamento_id, municipio_id) {
    var url = '../php/pacientes/getMunicipio.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'departamento_id=' + departamento_id,
        success: function(data) {
            $('#formulario_pacientes #municipio_id').html("");
            $('#formulario_pacientes #municipio_id').html(data);
            $('#formulario_pacientes #municipio_id').selectpicker('refresh');
            $('#formulario_pacientes #municipio_id').val(municipio_id);
            $('#formulario_pacientes #municipio_id').selectpicker('refresh');
        }
    });
    return false;
}

function getPais() {
    var url = '<?php echo SERVERURL; ?>php/pacientes/getPais.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#formulario_pacientes #pais_id').html("");
            $('#formulario_pacientes #pais_id').html(data);
            $('#formulario_pacientes #pais_id').selectpicker('refresh');

            $('#formulario_pacientes #pais_id').val(1);
            $('#formulario_pacientes #pais_id').selectpicker('refresh');
        }
    });
}

$('#formulario_pacientes #buscar_pais_pacientes').on('click', function(e) {
    listar_pais_buscar();
    $('#modal_busqueda_pais').modal({
        show: true,
        keyboard: false,
        backdrop: 'static'
    });
});

$('#formulario_pacientes #buscar_departamento_pacientes').on('click', function(e) {
    listar_departamentos_buscar();
    $('#modal_busqueda_departamentos').modal({
        show: true,
        keyboard: false,
        backdrop: 'static'
    });
});

$('#formulario_pacientes #buscar_municipio_pacientes').on('click', function(e) {
    if ($('#formulario_pacientes #departamento_id').val() == "" || $('#formulario_pacientes #departamento_id')
        .val() == null) {
        swal({
            title: "Error",
            text: "Lo sentimos el departamento no debe estar vacío, antes de seleccionar esta opción por favor seleccione un departamento, por favor corregir",
            type: "error",
            confirmButtonClass: 'btn-danger'
        });
    } else {
        listar_municipios_buscar();
        $('#modal_busqueda_municipios').modal({
            show: true,
            keyboard: false,
            backdrop: 'static'
        });
    }
});

var listar_pais_buscar = function() {
    var table_pais_buscar = $("#dataTablePais").DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "../php/pacientes/getPaisTabla.php"
        },
        "columns": [{
                "defaultContent": "<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"
            },
            {
                "data": "nombre"
            }
        ],
        "pageLength": 5,
        "lengthMenu": lengthMenu,
        "stateSave": true,
        "bDestroy": true,
        "language": idioma_español,
    });
    table_pais_buscar.search('').draw();
    $('#buscar').focus();

    view_pais_busqueda_dataTable("#dataTablePais tbody", table_pais_buscar);
}

var view_pais_busqueda_dataTable = function(tbody, table) {
    $(tbody).off("click", "button.view");
    $(tbody).on("click", "button.view", function(e) {
        e.preventDefault();
        var data = table.row($(this).parents("tr")).data();
        $('#formulario_pacientes #pais_id').val(data.pais_id);
        $('#modal_busqueda_pais').modal('hide');
    });
}

var listar_departamentos_buscar = function() {
    var table_departamentos_buscar = $("#dataTableDepartamentos").DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "../php/pacientes/getDepartamentosTabla.php"
        },
        "columns": [{
                "defaultContent": "<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"
            },
            {
                "data": "nombre"
            }
        ],
        "pageLength": 5,
        "lengthMenu": lengthMenu,
        "stateSave": true,
        "bDestroy": true,
        "language": idioma_español,
    });
    table_departamentos_buscar.search('').draw();
    $('#buscar').focus();

    view_departamentos_busqueda_dataTable("#dataTableDepartamentos tbody", table_departamentos_buscar);
}

var view_departamentos_busqueda_dataTable = function(tbody, table) {
    $(tbody).off("click", "button.view");
    $(tbody).on("click", "button.view", function(e) {
        e.preventDefault();
        var data = table.row($(this).parents("tr")).data();
        $('#formulario_pacientes #departamento_id').val(data.departamento_id);
        getMunicipio();
        $('#modal_busqueda_departamentos').modal('hide');
    });
}

var listar_municipios_buscar = function() {
    var departamento = $('#formulario_pacientes #departamento_id').val();
    var table_municipios_buscar = $("#dataTableMunicipios").DataTable({
        "destroy": true,
        "ajax": {
            "method": "POST",
            "url": "../php/pacientes/getMunicipiosTabla.php",
            "data": {
                'departamento': departamento
            },
        },
        "columns": [{
                "defaultContent": "<button class='view btn btn-primary'><span class='fas fa-copy'></span></button>"
            },
            {
                "data": "municipio"
            },
            {
                "data": "departamento"
            }
        ],
        "pageLength": 5,
        "lengthMenu": lengthMenu,
        "stateSave": true,
        "bDestroy": true,
        "language": idioma_español,
    });
    table_municipios_buscar.search('').draw();
    $('#buscar').focus();

    view_municipios_busqueda_dataTable("#dataTableMunicipios tbody", table_municipios_buscar);
}

var view_municipios_busqueda_dataTable = function(tbody, table) {
    $(tbody).off("click", "button.view");
    $(tbody).on("click", "button.view", function(e) {
        e.preventDefault();
        var data = table.row($(this).parents("tr")).data();
        $('#formulario_pacientes #municipio_id').val(data.municipio_id);
        $('#modal_busqueda_municipios').modal('hide');
    });
}

$(document).ready(function() {
    $("#modal_busqueda_pais").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_pais #buscar').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_departamentos").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_departamentos #buscar').focus();
    });
});

$(document).ready(function() {
    $("#modal_busqueda_municipios").on('shown.bs.modal', function() {
        $(this).find('#formulario_busqueda_municipios #buscar').focus();
    });
});

$('#formulario_pacientes #grupo_editar_rtn').on('click', function(e) {
    e.preventDefault();

    modal_agregar_expediente_manual($('#formulario_pacientes #pacientes_id').val(), $(
        '#formulario_pacientes #expediente').val());
});

var listar_pacientes = function(){
    var estado = "";
    var paciente = "";
    var dato = $('#form_main #bs_regis').val();

    if ($('#form_main #estado').val() == "" || $('#form_main #estado').val() == null) {
        estado = 1;
    } else {
        estado = $('#form_main #estado').val();
    }

    if ($('#form_main #tipo').val() == "" || $('#form_main #tipo').val() == null) {
        paciente = 1;
    } else {
        paciente = $('#form_main #tipo').val();
    }

	var table_pacientes = $("#dataTablePacientesMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/pacientes/llenarDataTablePacientes.php",
            "data": function(d) {
                d.paciente = paciente;
				d.estado = estado;
            }		
		},		
		"columns":[
			{"data": "expediente"},
			{"data": "identidad"},
			{"data": "paciente"},			
			{"data": "genero"},
			{"data": "telefono1"},
			{"data": "telefono2"},			
            {"data": "email"},
            {"data": "localidad"},
            {"data": "estado"},
			{
				"data": null,
				"defaultContent": 
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
							'<i class="fas fa-cog"></i>' +
						'</button>' +
						'<div class="dropdown-menu">' +
							'<a class="dropdown-item editarPacientes" href="#"><i class="fas fa-user-edit fa-lg"></i> Editar</a>' +
                            '<a class="dropdown-item eliminarPacientes" href="#"><i class="fas fa-trash fa-lg"></i> Eliminar</a>' +
						'</div>' +
					'</div>'
			}
		],		
        "lengthMenu": lengthMenu20,
		"stateSave": true,
		"bDestroy": true,		
		"language": idioma_español,//esta se encuenta en el archivo main.js
		"dom": dom,			
		"buttons":[		
			{
				text:      '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Pago',
				className: 'btn btn-info',
				action: 	function(){
					listar_pacientes();
				}
			},		
			{
				text:      '<i class="fas fa-user-plus fa-lg"></i> Registrar Pacientes',
				titleAttr: 'Registrar Pacientes',
				className: 'btn btn-primary',
				action: 	function(){
					modalPacientes();
				}
			},	  
			{
				text:      '<i class="fas fa-user-plus fa-lg"></i> Registrar Profesion',
				titleAttr: 'Registrar Profesion',
				className: 'btn btn-primary',
				action: 	function(){
					modalProfesion();
				}
			},	                      			
			{
				extend:    'excelHtml5',
				text:      '<i class="fas fa-file-excel fa-lg"></i> Excel',
				titleAttr: 'Excel',
				footer: true,
				title: 'Reporte Pacientes',
				className: 'btn btn-success',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                },				
			},
			{
				extend: 'pdf',
				orientation: 'landscape',
				text: '<i class="fas fa-file-pdf fa-lg"></i> PDF',
				titleAttr: 'PDF',
				footer: true,
				title: 'Reporte Pacientes',
				className: 'btn btn-danger',
				exportOptions: {
					modifier: {
						page: 'current' // Solo exporta las filas visibles en la página actual
					},
					columns: [0, 1, 2, 3, 4, 5, 6,7,8] // Define las columnas a exportar
				},
				customize: function(doc) {
					// Asegúrate de que `imagen` contenga la cadena base64 de la imagen
					doc.content.splice(1, 0, {
						margin: [0, 0, 0, 12],
						alignment: 'left',
						image: imagen, // Usando la variable que ya tiene la imagen base64
						width: 100, // Ajusta el tamaño si es necesario
						height: 45 // Ajusta el tamaño si es necesario
					});
				}
			},
			{
				extend: 'print',
				text: '<i class="fas fa-print fa-lg"></i> Imprimir',  // Correcta colocación del icono
				titleAttr: 'Imprimir',
				footer: true,
				title: 'Reporte Pacientes',
				className: 'btn btn-secondary',
				exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                },
			}
		],
	});	 
	table_pacientes.search('').draw();
	$('#buscar').focus();
	
	editar_pacientes_dataTable("#dataTablePacientesMain tbody", table_pacientes);
    eliminar_pacientes_datataTable("#dataTablePacientesMain tbody", table_pacientes);
}

var editar_pacientes_dataTable = function(tbody, table){
	$(tbody).off("click", "a.editarPacientes");
	$(tbody).on("click", "a.editarPacientes", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		editarRegistro(data.pacientes_id);
	});
}

var eliminar_pacientes_datataTable = function(tbody, table){
	$(tbody).off("click", "a.eliminarPacientes");
	$(tbody).on("click", "a.eliminarPacientes", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_eliminar(data.pacientes_id);
	});
}
</script>