/*
############################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################################
*/
$('.FormularioAjax').submit(function (e) {
	e.preventDefault();

	var form = $(this);

	var tipo = form.attr('data-form');
	var action = form.attr('action');
	var method = form.attr('method');
	var respuesta = form.children('.RespuestaAjax');

	// Deshabilitar el botón antes de hacer la solicitud AJAX
	form.find('button[type="submit"]').prop('disabled', true);

	var msjError = "<script></script>";
	var formdata = new FormData(this);

	var textoAlerta;
	var type;

	if (tipo == "save") {
		textoAlerta = "Los datos que enviaras quedaran almacenados en el sistema";
		type = "info";
	} else if (tipo == "delete") {
		textoAlerta = "Los datos serán eliminados completamente del sistema";
		type = "warning";
	} else if (tipo == "update") {
		textoAlerta = "Los datos del sistema serán actualizados";
		type = "info";
	} else {
		textoAlerta = "¿Quieres realizar la operación solicitada?";
		type = "warning";
	}

	swal({
		title: "¿Estás seguro?",
		text: textoAlerta,
		icon: type,
		buttons: {
			cancel: {
				text: "Cancelar",
				visible: true,
				closeModal: true
			},
			confirm: {
				text: "Aceptar",
				closeModal: false
			}
		},
		dangerMode: false,
		closeOnEsc: false, // Desactiva el cierre con la tecla Esc
		closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera		
	}).then(function (isConfirm) { // Usamos 'then' con la función de callback
		if (isConfirm) {
			$.ajax({
				type: method,
				url: action,
				data: formdata ? formdata : form.serialize(),
				cache: false,
				contentType: false,
				processData: false,
				xhr: function () {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							percentComplete = parseInt(percentComplete * 100);
							if (percentComplete < 100) {
								respuesta.html('<p class="text-center">Procesado... (' + percentComplete + '%)</p><div class="progress progress-striped active"><div class="progress-bar progress-bar-info" style="width: ' + percentComplete + '%;"></div></div>');
							} else {
								respuesta.html('<p class="text-center"></p>');
							}
						}
					}, false);
					return xhr;
				},
				success: function (data) {
					var datos = eval(data);
					swal.close();

					if (datos[0] == "Error") {
						showNotify(datos[2], datos[0], datos[1]);
					} else if (datos[0] == "Guardar") {
						showNotify(datos[2], datos[0], datos[1]);
					} else {
						showNotify(datos[2], datos[0], datos[1]);
					}

					if (datos[4] != "") {
						$('#' + datos[4])[0].reset();
						$('#' + datos[4] + ' #pro').val(datos[5]);
						$('input:first').focus();
					}

					llenarTabla(datos[6]);

					if (datos[6] == "AtencionMedica") {
						showFactura(datos[8]);
					}

					if (datos[6] == "Facturacion") {
						pago(datos[8]);
						pagination(1);
					}

					if (datos[6] == "GuardarFactura") {
						pagination(1);
					}

					if (datos[6] == "Pagos") {
						printBill(datos[8]);
						limpiarTabla();
						pagination(1);
						volver();
						setTimeout(sendMail(datos[8]), 5000);
					}

					if (datos[6] == "formCita") {
						reportePDF(datos[8]);
						sendEmailReprogramación(datos[8]);
					}

					if (datos[9] == "Eliminar") {
						$('#' + datos[7]).modal('hide');
					}

					if (datos[9] == "Guardar") {
						$('#' + datos[7]).modal('hide');
					}

					// Habilitar el botón después de completar la transacción
					form.find('button[type="submit"]').prop('disabled', false);

					return false;
				},
				error: function () {
					respuesta.html(msjError);
					swal.close();
				}
			});
		} else {
			// Si el usuario hizo clic en "Cancelar", habilita el botón del formulario
			form.find('button[type="submit"]').prop('disabled', false);
			swal.close();
		}
	});
});

const notyf = new Notyf({
    position: {
        x: 'right',
        y: 'top',
    },
    dismissible: true, // Permite cerrar manualmente
    closeOnClick: true, // Cierra al hacer clic
    types: [
        {
            type: 'warning',
            background: 'orange',
            duration: 5000, // 5 segundos
            icon: {
                className: 'fas fa-exclamation-triangle fa-lg',
                tagName: 'i',
                color: 'white',
            },
            closeIcon: {
                className: 'fas fa-times',
                color: 'white',
                tagName: 'span',
                position: 'right',
				style: 'margin-right: 15px;'
            }
        },
        {
            type: 'error',
            background: 'indianred',
            duration: 10000, // 10 segundos (más tiempo para errores)
            dismissible: true,
            icon: {
                className: 'fas fa-times-circle fa-lg',
                tagName: 'i',
                color: 'white'
            },
            closeIcon: {
                className: 'fas fa-times',
                color: 'white',
                tagName: 'span',
                position: 'right'
            }
        },
        {
            type: 'info',
            background: '#1e88e5',
            duration: 5000, // 5 segundos
            dismissible: true,
            icon: {
                className: 'fas fa-info-circle fa-lg',
                tagName: 'i',
                color: 'white'
            },
            closeIcon: {
                className: 'fas fa-times',
                color: 'white',
                tagName: 'span',
                position: 'right'
            }
        },
        {
            type: 'success',
            background: '#4caf50',
            duration: 5000, // 5 segundos
            dismissible: true,
            icon: {
                className: 'fas fa-check-circle fa-lg',
                tagName: 'i',
                color: 'white'
            },
            closeIcon: {
                className: 'fas fa-times',
                color: 'white',
                tagName: 'span',
                position: 'right'
            }
        },
        {
            type: 'loading',
            background: '#3498db',  // Azul profesional
            duration: 5000,         //5 segundos
            icon: {
                className: 'fas fa-circle-notch fa-spin', // Icono giratorio
                tagName: 'i',
                color: 'white'
            },
            dismissible: false,     // No se puede cerrar manualmente
            closeIcon: false        // Sin botón de cerrar
        }        
    ]
});

// Variable para controlar la notificación de loading
let loadingNotification = null;

/**
 * Muestra un indicador de carga con Notyf
 * @param {string} [message='Procesando solicitud'] - Mensaje a mostrar
 */
function showLoading(message = 'Procesando solicitud') {
    // Cierra cualquier notificación de carga previa
    if (loadingNotification) {
        loadingNotification.dismiss();
    }

    loadingNotification = notyf.open({
        type: 'info',
        message: `<strong>Procesando</strong><br>${message}`, // Usar backticks (`) para template literal
        duration: 0, // Notificación persistente
        dismissible: false,
        icon: {
            className: 'fas fa-spinner fa-spin fa-xl', // Icono con animación de giro
            tagName: 'i',
            color: 'white'
        },
        settings: {
            allowHtml: true
        }
    });
}

function hideLoading() {
    if (loadingNotification) {
        notyf.dismiss(loadingNotification);
        loadingNotification = null;
    }
}

/**
 * Muestra una notificación estilizada al usuario.
 * @param {string} title - El título de la notificación (ej: 'Éxito', 'Error', 'Advertencia')
 * @param {string} message - El mensaje detallado a mostrar (ej: 'Los datos se guardaron correctamente')
 * @param {'success'|'error'|'warning'|'info'} type - Tipo de notificación (valores válidos: 'success', 'error', 'warning', 'info')
 * @example
 * // Muestra una notificación de éxito
 * showNotify('Éxito', 'Los datos se guardaron correctamente', 'success');
 * @example
 * // Muestra una notificación de error
 * showNotify('Error', 'No se pudo conectar al servidor', 'error');
 */
function showNotify(type, title, message) {
    const validTypes = ['success', 'error', 'warning', 'info', 'loading'];
    
    if (validTypes.includes(type)) {
        notyf.open({
            type: type,
            message: `<strong>${title}</strong><br>${message}`,
            settings: {
                ripple: true,
                allowHtml: true,
            }
        });
    } else {
        console.error('Tipo de notificación no válido');
    }
}
/*##########################################################################################################################################################################################################################################################################################################################*/
/*##########################################################################################################################################################################################################################################################################################################################*/
//INICIO IDIOMA
var idioma_español = {
	"processing": "Procesando...",
	"lengthMenu": "Mostrar _MENU_ registros",
	"zeroRecords": "No se encontraron resultados",
	"emptyTable": "Ningún dato disponible en esta tabla",
	"info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
	"infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
	"infoFiltered": "(filtrado de un total de _MAX_ registros)",
	"search": "Buscar:",
	"infoThousands": ",",
	"loadingRecords": "Cargando...",
	"paginate": {
		"first": "Primero",
		"last": "Último",
		"next": "Siguiente",
		"previous": "Anterior"
	},
	"aria": {
		"sortAscending": ": Activar para ordenar la columna de manera ascendente",
		"sortDescending": ": Activar para ordenar la columna de manera descendente"
	},
	"buttons": {
		"copy": "Copiar",
		"colvis": "Visibilidad",
		"collection": "Colección",
		"colvisRestore": "Restaurar visibilidad",
		"copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
		"copySuccess": {
			"1": "Copiada 1 fila al portapapeles",
			"_": "Copiadas %d fila al portapapeles"
		},
		"copyTitle": "Copiar al portapapeles",
		"csv": "CSV",
		"excel": "Excel",
		"pageLength": {
			"-1": "Mostrar todas las filas",
			"1": "Mostrar 1 fila",
			"_": "Mostrar %d filas"
		},
		"pdf": "PDF",
		"print": "Imprimir"
	},
	"autoFill": {
		"cancel": "Cancelar",
		"fill": "Rellene todas las celdas con <i>%d<\/i>",
		"fillHorizontal": "Rellenar celdas horizontalmente",
		"fillVertical": "Rellenar celdas verticalmentemente"
	},
	"decimal": ",",
	"searchBuilder": {
		"add": "Añadir condición",
		"button": {
			"0": "Constructor de búsqueda",
			"_": "Constructor de búsqueda (%d)"
		},
		"clearAll": "Borrar todo",
		"condition": "Condición",
		"conditions": {
			"date": {
				"after": "Despues",
				"before": "Antes",
				"between": "Entre",
				"empty": "Vacío",
				"equals": "Igual a",
				"not": "No",
				"notBetween": "No entre",
				"notEmpty": "No Vacio"
			},
			"moment": {
				"after": "Despues",
				"before": "Antes",
				"between": "Entre",
				"empty": "Vacío",
				"equals": "Igual a",
				"not": "No",
				"notBetween": "No entre",
				"notEmpty": "No vacio"
			},
			"number": {
				"between": "Entre",
				"empty": "Vacio",
				"equals": "Igual a",
				"gt": "Mayor a",
				"gte": "Mayor o igual a",
				"lt": "Menor que",
				"lte": "Menor o igual que",
				"not": "No",
				"notBetween": "No entre",
				"notEmpty": "No vacío"
			},
			"string": {
				"contains": "Contiene",
				"empty": "Vacío",
				"endsWith": "Termina en",
				"equals": "Igual a",
				"not": "No",
				"notEmpty": "No Vacio",
				"startsWith": "Empieza con"
			}
		},
		"data": "Data",
		"deleteTitle": "Eliminar regla de filtrado",
		"leftTitle": "Criterios anulados",
		"logicAnd": "Y",
		"logicOr": "O",
		"rightTitle": "Criterios de sangría",
		"title": {
			"0": "Constructor de búsqueda",
			"_": "Constructor de búsqueda (%d)"
		},
		"value": "Valor"
	},
	"searchPanes": {
		"clearMessage": "Borrar todo",
		"collapse": {
			"0": "Paneles de búsqueda",
			"_": "Paneles de búsqueda (%d)"
		},
		"count": "{total}",
		"countFiltered": "{shown} ({total})",
		"emptyPanes": "Sin paneles de búsqueda",
		"loadMessage": "Cargando paneles de búsqueda",
		"title": "Filtros Activos - %d"
	},
	"select": {
		"1": "%d fila seleccionada",
		"_": "%d filas seleccionadas",
		"cells": {
			"1": "1 celda seleccionada",
			"_": "$d celdas seleccionadas"
		},
		"columns": {
			"1": "1 columna seleccionada",
			"_": "%d columnas seleccionadas"
		}
	},
	"thousands": "."
}
//FIN IDIOMA

//INICIO CONVETIR IMAGEN BASE 64
function toDataURL(src, callback, outputFormat) {
	var img = new Image();
	img.crossOrigin = 'Anonymous';
	img.onload = function () {
		var canvas = document.createElement('CANVAS');
		var ctx = canvas.getContext('2d');
		var dataURL;
		canvas.height = this.naturalHeight;
		canvas.width = this.naturalWidth;
		ctx.drawImage(this, 0, 0);
		dataURL = canvas.toDataURL(outputFormat);
		callback(dataURL);
	};
	img.src = src;
	if (img.complete || img.complete === undefined) {
		img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
		img.src = src;
	}
}
//FIN CONVERTIR IMAGEN BASE 64

var imagen;
toDataURL(
	'../img/logo.png',
	function (dataUrl) {
		imagen = dataUrl;
	}
)

//INICIO DATATABLE OPCIONES
var lengthMenu = [[5, 10, 20, 50, 100, -1], [5, 10, 20, 50, 100, 'Todos']];
var lengthMenu20 = [[20, 50, 100, -1], [20, 50, 100, 'Todos']];

var dom = "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
	"<'row'<'col-sm-12'tr>>" +
	"<'row'<'col-sm-5'i><'col-sm-7'p>>";
//FIN DATATABLE OPCIONES	
/*##########################################################################################################################################################################################################################################################################################################################*/

/*##########################################################################################################################################################################################################################################################################################################################*/

//LLENADO DE TABLAS
$('#invoice-form #notes').keyup(function () {
	var max_chars = 255;
	var chars = $(this).val().length;
	var diff = max_chars - chars;

	$('#invoice-form #charNum_notas').html(diff + ' Caracteres');

	if (diff == 0) {
		return false;
	}
});

function llenarTabla(dato) {
	if (dato == "formPacientes") {
		listar_pacientes();
	}

	if (dato == "formCita") {
		pagination(1);
	}

	if (dato == "Almacen") {
		listar_almacen();
	}

	if (dato == "Preclinica") {
		pagination(1);
	}

	if (dato == "Colaboradores") {
		pagination(1);
		puesto();
		getJornadaColaborador();
		servicio();
	}

	if (dato == "Puestos") {
		pagination_puestos(1);
		puesto();
		getJornadaColaborador();
		servicio();
	}

	if (dato == "Servicios") {
		pagination_servicio(1);
		puesto();
		getJornadaColaborador();
		servicio();
	}

	if (dato == "servicioColaboradores") {
		pagination_jornada_colaboradores(1);
		puesto();
		getJornadaColaborador();
		servicio();
	}

	if (dato == "ReporteEnfermeria") {
		pagination_preclinica(1);
	}

	if (dato == "Ubicacion") {
		listar_ubicacion();
	}

	if (dato == "Almacen") {
		listar_almacen();
	}

	if (dato == "Productos") {
		listar_productos();
	}

	if (dato == "Facturacion") {
		pagination(1);
		$('#formulario_facturacion')[0].reset();
		getPacientesFacturacion();
		getColaboradoresFacturacion();
		getServiciosFacturacion();
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();
	}

	if (dato == "GuardarFactura") {
		pagination(1);
		$('#formulario_facturacion')[0].reset();
		getPacientesFacturacion();
		getColaboradoresFacturacion();
		getServiciosFacturacion();
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();
	}

	if (dato == "FacturaAtenciones") {
		pagination(1);
		$('#formulario_facturacion')[0].reset();
		getPacientesFacturacion();
		getColaboradoresFacturacion();
		getServiciosFacturacion();
		limpiarTabla();
		cleanFooterValueBill();
		volver();
		$('.footer').show();
		$('.footer1').hide();
	}

	if (dato == "Usuarios") {
		pagination(1);
	}

	if (dato == "configuracionVarios") {
		pagination(1);
	}

	if (dato == "formProfesionales") {
		paginationPorfesionales(1);
	}

	if (dato == "Medidas") {
		listar_medidas();
	}

	if (dato == "AtencionMedica") {
		pagination(1);
		listar_productos_facturas_buscar();
		$('.footer').show();
		$('.footer1').hide();
	}

	if (dato == "configuracionVariosemails") {
		pagination(1);
	}

	if (dato == "Movimientos") {
		listar_movimientos();
		agregarMovimientos();
		getCategoriaProductosMovimientos();
		getCategoriaProductos();
		getCategoriaOperacion();
		getProductos(1);
	}

	if (dato == "asignarServicioColaboradores") {
		paginationAsignacionServiciosColaboradores(1);
	}	
}

$(function () {
	$('[data-toggle="tooltip"]').tooltip({
		trigger: "hover"
	})
});

//INICIO MENU FORM PAGOS FACTURAS
$(document).ready(function () {
	$(".menu-toggle2").hide();

	//Menu Toggle Script
	$("#menu-toggle1").click(function (e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});

	$("#menu-toggle2").click(function (e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});

	// For highlighting activated tabs
	$("#tab1").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab1").addClass("active1");
		$("#tab1").removeClass("bg-light");
	});

	$("#tab2").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab2").addClass("active1");
		$("#tab2").removeClass("bg-light");
	});

	$("#tab3").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab3").addClass("active1");
		$("#tab3").removeClass("bg-light");
	});

	$("#tab4").click(function () {
		$(".tabs").removeClass("active1");
		$(".tabs").addClass("bg-light");
		$("#tab4").addClass("active1");
		$("#tab4").removeClass("bg-light");
	});
})
//FIN MENU FORM PAGOS FACTURAS

$(".menu-toggle1").on("click", function (e) {
	e.preventDefault();
	$(".menu-toggle1").hide();
	$(".menu-toggle2").show();
	$("#sidebar-wrapper").hide();
});

$(".menu-toggle2").on("click", function (e) {
	e.preventDefault();
	$(".menu-toggle2").hide();
	$(".menu-toggle1").show();
	$("#sidebar-wrapper").show();
});
//FIN MENU FACTURAS