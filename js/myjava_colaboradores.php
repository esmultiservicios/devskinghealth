<script>
$(document).ready(pagination(1));
puesto();
empresa();
servicio();
pagination_jornada_colaboradores(1);
pagination_servicio(1);
pagination_puestos(1);
puestoServcioColaborador();
getJornadaColaborador();
getEstatus();
puesto();
getJornadaColaborador();
servicio();
getServicioAsignacion();
paginationAsignacionServiciosColaboradores(1);
$(function() {
    $('#nuevo-registro-colaboradores').on('click', function(e) {
        e.preventDefault();
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
            $('#formulario_colaboradores')[0].reset();
            $('#pro').val('Registro');
            $('#edi').hide();
            $('#reg').show();
            empresa();
            puesto();
            getEstatus();
            $('#formulario_colaboradores').attr({
                'data-form': 'save'
            });
            $('#formulario_colaboradores').attr({
                'action': '<?php echo SERVERURL; ?>php/colaboradores/agregar.php'
            });

            $('#registrar_colaboradores').modal({
                show: true,
                keyboard: false,
                backdrop: 'static'
            });
            puesto();
            empresa();
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


    $('#nuevo-registro-puestos').on('click', function(e) {
        e.preventDefault();
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
            $('#formulario_puestos')[0].reset();
            $('#formulario_puestos #pro').val('Registro');
            $('#formulario_puestos #edi').hide();
            $('#formulario_puestos #reg').show();
            pagination_puestos(1);

            $('#formulario_puestos').attr({
                'data-form': 'save'
            });
            $('#formulario_puestos').attr({
                'action': '<?php echo SERVERURL; ?>php/colaboradores/agregar_puestos.php'
            });

            $('#registrar_puestos').modal({
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

    $('#nuevo-registro-servicios').on('click', function(e) {
        e.preventDefault();
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
            $('#formulario_servicios')[0].reset();
            $('#formulario_servicios #pro').val('Registro');
            $('#formulario_servicios #edi').hide();
            $('#formulario_servicios #reg').show();
            pagination_servicio(1);

            $('#formulario_servicios').attr({
                'data-form': 'save'
            });
            $('#formulario_servicios').attr({
                'action': '<?php echo SERVERURL; ?>php/colaboradores/agregar_servicios.php'
            });

            $('#registrar_servicios').modal({
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

    $('#asignar_servicios').on('click', function(e) {
        e.preventDefault();
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
            $('#formulario_asignacion_servicios_colaboradores')[0].reset();
            $('#formulario_asignacion_servicios_colaboradores #pro').val('Registro');
            $('#formulario_asignacion_servicios_colaboradores #edi').hide();
            $('#formulario_asignacion_servicios_colaboradores #reg_asignacion').show();
            pagination_servicio(1);
            getServicioAsignacion();

            $('#formulario_asignacion_servicios_colaboradores').attr({
                'data-form': 'save'
            });
            $('#formulario_asignacion_servicios_colaboradores').attr({
                'action': '<?php echo SERVERURL; ?>php/colaboradores/agregarAsignacionServicio.php'
            });

            $('#asignar_servicio_colaborador').modal({
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


    $('#nuevo-registro-colaborador-servicios').on('click', function(e) {
        e.preventDefault();
        if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
            $('#formulario_servicios_colaboradores')[0].reset();
            $('#formulario_servicios_colaboradores #pro').val('Registro');
            $('#formulario_servicios_colaboradores #edi').hide();
            $('#formulario_servicios_colaboradores #reg').show();
            pagination_jornada_colaboradores(1);
            puestoServcioColaborador();
            getJornadaColaborador();
            servicio();
            $('#formulario_servicios_colaboradores #colaborador_id').val("");

            $('#formulario_servicios_colaboradores').attr({
                'data-form': 'save'
            });
            $('#formulario_servicios_colaboradores').attr({
                'action': '<?php echo SERVERURL; ?>php/colaboradores/agregar_jornada_colaboradores.php'
            });

            $('#registrar_servicios_colaboradores').modal({
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

    $('#main_form #bs-regis').on('keyup', function() {
        pagination(1);
        return false;
    });

    $('#main_form #status').on('change', function() {
        pagination(1);
        return false;
    });

    $('#formulario_puestos #puestosn').on('keyup', function() {
        pagination_puestos(1);
        return false;
    });

    $('#formulario_servicios #servicios').on('keyup', function() {
        pagination_servicio(1);
        return false;
    });
});

/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function() {
    $("#registrar_colaboradores").on('shown.bs.modal', function() {
        $(this).find('#formulario_colaboradores #nombre').focus();
    });
});

$(document).ready(function() {
    $("#registrar_puestos").on('shown.bs.modal', function() {
        $(this).find('#formulario_puestos #puestosn').focus();
    });
});

$(document).ready(function() {
    $("#registrar_servicios_colaboradores").on('shown.bs.modal', function() {
        $(this).find('#formulario_servicios_colaboradores #cantidad_nuevos').focus();
    });
});

$(document).ready(function() {
    $("#registrar_servicios").on('shown.bs.modal', function() {
        $(this).find('#formulario_servicios #servicios').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/

$('#registrar_servicios_colaboradores #clean_datos').on('click', function(e) {
    e.preventDefault();
    cleanPuestoServicios();
});

$(document).ready(function() {
    $('#registrar_servicios_colaboradores #colaborador_id').on('change', function() {
        pagination_jornada_colaboradores(1);
    });
});

function modal_eliminar(id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
            title: "¿Estas seguro?",
            text: "¿Desea eliminar este colaborador: " + getColaboradorNombre(id) + "?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",
                }
            },
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        }).then((willConfirm) => {
            if (willConfirm === true) {
                eliminarRegistro(id);
            }
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
}

function modal_eliminarAsignacionColaborador(servicios_colaboradores_id, colaborador_id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
            title: "¿Estas seguro?",
            text: "¿Desea eliminar este servicio para el colaborador: " + getColaboradorNombre(colaborador_id) + "?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",
                }
            },
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        }).then((willConfirm) => {
            if (willConfirm === true) {
                eliminarRegistroAsignacionColaborador(servicios_colaboradores_id, colaborador_id);
            }
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
}

function getColaboradorNombre(id) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/getColaboradorNombre.php';
    var dato;
    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        async: false,
        success: function(data) {
            dato = data;
        }
    });
    return dato;
}

function modal_eliminarPuesto(id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
            title: "¿Estas seguro?",
            text: "¿Desea eliminar este puesto",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",
                }
            },
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        }).then((willConfirm) => {
            if (willConfirm === true) {
                eliminarPuesto(id);
            }
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
}

function modal_eliminarServicio(id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
            title: "¿Estas seguro?",
            text: "¿Desea eliminar este servicio",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",
                }
            },
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        }).then((willConfirm) => {
            if (willConfirm === true) {
                eliminarServicio(id);
            }
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
}

function modal_eliminarJornadaColaboradores(colaborador_id, id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        swal({
            title: "¿Estas seguro?",
            text: "¿Desea eliminar esta jornada para el profesional: " + getColaboradorNombre(id) + "?",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    visible: true
                },
                confirm: {
                    text: "¡Sí, eliminar el registro!",
                }
            },
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        }).then((willConfirm) => {
            if (willConfirm === true) {
                eliminarJornadaColaboradores(id);
            }
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
}

function eliminarPuesto(id) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/eliminarPuesto.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        success: function(registro) {
            if (registro == 1) {
                pagination(1);
                pagination_puestos(1);
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-close
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                    
                });
                $('#registrar_puestos').modal('hide');
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al eliminar el registro",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar el registro, existen valores almacenados, por favor corregir",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else {
                swal({
                    title: "Error",
                    text: "No se pudo procesar su solicitud",
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

function eliminarServicio(id) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/eliminarServicio.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        success: function(registro) {
            if (registro == 1) {
                pagination(1);
                pagination_servicio(1);
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-close
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                    
                });
                $('#registrar_servicios').modal('hide');
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al eliminar el registro",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "No se puede eliminar el registro, existen valores almacenados, por favor corregir",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else {
                swal({
                    title: "Error",
                    text: "No se pudo procesar su solicitud",
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

function eliminarJornadaColaboradores(id) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/eliminarJornadaColaboradores.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        success: function(registro) {
            if (registro == 1) {
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-close
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                    
                });
                $('#registrar_servicios_colaboradores').modal('hide');
                pagination_jornada_colaboradores(1);
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "Error al eliminar el registro",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else {
                swal({
                    title: "Error",
                    text: "No se puede procesar su solicitud",
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

function eliminarRegistro(id) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/eliminar.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'id=' + id,
        success: function(registro) {
            if (registro == 1) {
                pagination(1);
                $('#bs-regis').val("");
                swal({
                    title: "Success",
                    text: "Registro eliminado correctamente",
                    icon: "success",
                    timer: 3000, //timeOut for auto-close
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
                return false;
            } else if (registro == 2) {
                swal({
                    title: "Error",
                    text: "No se puede procesar su solicitud",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else if (registro == 3) {
                swal({
                    title: "Error",
                    text: "Lo sentimos el colaborador tiene asignado un usuario, no se puede eliminar",
                    icon: "error",
                    dangerMode: true,
                    closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                    closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
                });
            } else {
                swal({
                    title: "Error",
                    text: "No se puede procesar su solicitud",
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

function eliminarRegistroAsignacionColaborador(servicios_colaboradores_id, colaborador_id) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/eliminarAsignacionServicio.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: 'servicios_colaboradores_id=' + servicios_colaboradores_id + '&colaborador_id=' + colaborador_id,
        success: function(registro) {
            paginationAsignacionServiciosColaboradores(1);
            $('#bs-regis').val("");
            swal({
                title: "Success",
                text: "Registro eliminado correctamente",
                icon: "success",
                timer: 3000, //timeOut for auto-close
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                
            });
            return false;
        }
    });
    return false;
}

function editarRegistro(id) {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        $('#formulario_colaboradores')[0].reset();
        var url = '<?php echo SERVERURL; ?>php/colaboradores/editar.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: 'id=' + id,
            success: function(valores) {
                var datos = eval(valores);
                $('#reg').hide();
                $('#edi').show();
                $('#formulario_colaboradores #pro').val('Edicion');
                $('#formulario_colaboradores #id-registro').val(id);
                $('#formulario_colaboradores #nombre').val(datos[0]);
                $('#formulario_colaboradores #apellido').val(datos[1]);
                $('#formulario_colaboradores #empresa').val(datos[2]);
                $('#formulario_colaboradores #empresa').selectpicker('refresh');
                $('#formulario_colaboradores #puesto').val(datos[3]);
                $('#formulario_colaboradores #puesto').selectpicker('refresh');
                $('#formulario_colaboradores #identidad').val(datos[4]);
                $('#formulario_colaboradores #estatus').val(datos[5]);
                $('#formulario_colaboradores #estatus').selectpicker('refresh');

                $('#formulario_colaboradores').attr({
                    'data-form': 'update'
                });
                $('#formulario_colaboradores').attr({
                    'action': '<?php echo SERVERURL; ?>php/colaboradores/agregar_edicion.php'
                });

                $('#registrar_colaboradores').modal({
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
            icon: "error",
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
        });
    }
}

function reporteEXCEL() {
    if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2) {
        var dato = $('#bs-regis').val();
        var url = '<?php echo SERVERURL; ?>php/colaboradores/buscar_colaboradores_excel.php?dato=' + dato;
        window.open(url);
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

function pagination(partida) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/paginar.php';
    var dato = $('#main_form #bs-regis').val();
    var estatus;

    if ($('#main_form #status').val() == "" || $('#main_form #status').val() == null) {
        estatus = 1;
    } else {
        estatus = $('#main_form #status').val();
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&dato=' + dato + '&estatus=' + estatus,
        success: function(data) {
            var array = eval(data);
            $('#agrega-registros').html(array[0]);
            $('#pagination').html(array[1]);
        }
    });
    return false;
}

function pagination_jornada_colaboradores(partida) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/paginar_jornada_colaboradores.php';
    var colaborador_id = $('#registrar_servicios_colaboradores #colaborador_id').val();

    if (colaborador_id == null || colaborador_id == "") {
        colaborador_id = "";
    } else {
        colaborador_id = $('#registrar_servicios_colaboradores #colaborador_id').val();
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&colaborador_id=' + colaborador_id,
        success: function(data) {
            var array = eval(data);
            $('#agrega-registros_servicio_colaborador').html(array[0]);
            $('#pagination_jornada_colaboradores').html(array[1]);
        }
    });
    return false;
}

function paginationAsignacionServiciosColaboradores(partida) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/paginationAsignacionServicios.php';
    var puesto_id = "";
    var colaborador_id = "";

    if (puesto_id == null || colaborador_id == "") {
        puesto_id = "";
    } else {
        puesto_id = $('#formulario_asignacion_servicios_colaboradores #registrar_servicios_colaboradores #puesto_id')
            .val();
    }

    if (colaborador_id == null || colaborador_id == "") {
        colaborador_id = "";
    } else {
        colaborador_id = $(
                '#formulario_asignacion_servicios_colaboradores #registrar_servicios_colaboradores #colaborador_id')
            .val();
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&puesto_id=' + puesto_id + '&colaborador_id=' + colaborador_id,
        success: function(data) {
            var array = eval(data);
            $('#agrega-registros_asignacion_servicio_colaborador').html(array[0]);
            $('#pagination_asignacion_servicios').html(array[1]);
        }
    });
    return false;
}

function pagination_servicio(partida) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/paginar_servicios.php';
    var dato = $('#formulario_servicios #servicios').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&dato=' + dato,
        success: function(data) {
            var array = eval(data);
            $('#agrega-registros_servicio').html(array[0]);
            $('#pagination_servicio').html(array[1]);
        }
    });
    return false;
}


function pagination_puestos(partida) {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/paginar_puestos.php';
    var dato = $('#formulario_puestos #puestosn').val();

    $.ajax({
        type: 'POST',
        url: url,
        data: 'partida=' + partida + '&dato=' + dato,
        success: function(data) {
            var array = eval(data);
            $('#agrega-registros_puestos').html(array[0]);
            $('#pagination_puestos').html(array[1]);
        }
    });
    return false;
}

function puesto() {
    var url = '<?php echo SERVERURL; ?>php/selects/puestos.php';

    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_colaboradores #puesto').html("");
            $('#formulario_colaboradores #puesto').html(data);
            $('#formulario_colaboradores #puesto').selectpicker('refresh');
        }
    });
    return false;
}

function empresa() {
    var url = '<?php echo SERVERURL; ?>php/selects/empresa.php';

    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_colaboradores #empresa').html("");
            $('#formulario_colaboradores #empresa').html(data);
            $('#formulario_colaboradores #empresa').selectpicker('refresh');
        }
    });
    return false;
}

function servicio() {
    var url = '<?php echo SERVERURL; ?>php/selects/servicios.php';

    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_servicios_colaboradores #servicio_colaborador').html("");
            $('#formulario_servicios_colaboradores #servicio_colaborador').html(data);
            $('#formulario_servicios_colaboradores #servicio_colaborador').selectpicker('refresh');
        }
    });
    return false;
}

function puestoServcioColaborador() {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/getPuestos.php';
    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_servicios_colaboradores #puesto_id').html("");
            $('#formulario_servicios_colaboradores #puesto_id').html(data);
            $('#formulario_servicios_colaboradores #puesto_id').selectpicker('refresh');

            $('#formulario_asignacion_servicios_colaboradores #puesto_id').html("");
            $('#formulario_asignacion_servicios_colaboradores #puesto_id').html(data);
            $('#formulario_asignacion_servicios_colaboradores #puesto_id').selectpicker('refresh');
        }
    });
    return false;
}

$(document).ready(function() {
    $('#formulario_asignacion_servicios_colaboradores #puesto_id').on('change', function() {
        var url = '../php/colaboradores/getColaboradorAsignacion.php';

        var puesto_id = $('#formulario_asignacion_servicios_colaboradores #puesto_id').val();

        $.ajax({
            type: 'POST',
            url: url,
            data: 'puesto_id=' + puesto_id,
            success: function(data) {
                $('#formulario_asignacion_servicios_colaboradores #colaborador_id').html(
                    "");
                $('#formulario_asignacion_servicios_colaboradores #colaborador_id').html(
                    data);
                $('#formulario_asignacion_servicios_colaboradores #colaborador_id')
                    .selectpicker('refresh');
            }
        });
        return false;
    });
});

function getJornadaColaborador() {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/getJornadaColaborador.php';
    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_servicios_colaboradores #jornada_id').html("");
            $('#formulario_servicios_colaboradores #jornada_id').html(data);
            $('#formulario_servicios_colaboradores #jornada_id').selectpicker('refresh');
        }
    });
    return false;
}

function getServicioAsignacion() {
    var url = '<?php echo SERVERURL; ?>php/colaboradores/getServicio.php';
    $.ajax({
        type: 'POST',
        url: url,
        success: function(data) {
            $('#formulario_asignacion_servicios_colaboradores #servicio_id').html("");
            $('#formulario_asignacion_servicios_colaboradores #servicio_id').html(data);
            $('#formulario_asignacion_servicios_colaboradores #servicio_id').selectpicker('refresh');
        }
    });
    return false;
}

$(document).ready(function() {
    $('#registrar_servicios_colaboradores #puesto_id').on('change', function() {
        var puesto_id = $('#puesto_id').val();
        var url = '<?php echo SERVERURL; ?>php/colaboradores/getColaboradorporPuesto.php';
        $.ajax({
            type: "POST",
            url: url,
            async: true,
            data: 'puesto_id=' + puesto_id,
            success: function(data) {
                $('#formulario_servicios_colaboradores #colaborador_id').html("");
                $('#formulario_servicios_colaboradores #colaborador_id').html(data);
                $('#formulario_servicios_colaboradores #colaborador_id').selectpicker(
                    'refresh');
            }
        });

    });
});

function cleanPuestoServicios() {
    $('#formulario_servicios_colaboradores #colaborador_id').html("");
    $('#formulario_servicios_colaboradores #servicio_colaborador').html("");
    puestoServcioColaborador();
    servicio();
    pagination_jornada_colaboradores(1);
    getJornadaColaborador();
}

function getEstatus() {
    var url = '<?php echo SERVERURL; ?>php/users/getStatus.php';

    $.ajax({
        type: "POST",
        url: url,
        async: true,
        success: function(data) {
            $('#main_form #status').html("");
            $('#main_form #status').html(data);
            $('#main_form #status').selectpicker('refresh');

            $('#formulario_colaboradores #estatus').html("");
            $('#formulario_colaboradores #estatus').html(data);
            $('#formulario_colaboradores #estatus').selectpicker('refresh');
        }
    });
}

$('#main_form #reporte_excel').on('click', function(e) {
    e.preventDefault();
    reporteEXCEL();
});
</script>