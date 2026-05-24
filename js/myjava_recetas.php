<script>
/*INICIO DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
$(document).ready(function(){
    $("#eliminar").on('shown.bs.modal', function(){
        $(this).find('#form_eliminar #motivo').focus();
    });
});

$(document).ready(function(){
    $("#cobros").on('shown.bs.modal', function(){
        $(this).find('#formCobros #comentario').focus();
    });
});
/*FIN DE FUNCIONES PARA ESTABLECER EL FOCUS PARA LAS VENTANAS MODALES*/
/****************************************************************************************************************************************************************/
//INICIO CONTROLES DE ACCION
$(document).ready(function() {
	//LLAMADA A LAS FUNCIONES
	funciones();
	//FIN ABRIR VENTANA MODAL PARA EL REGISTRO DE LAS FACTURAS

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
  $('#form_main_receta_main #estado').on('change',function(){
    listar_recetas();
  });

  $('#form_main_receta_main #clientes').on('change',function(){
    listar_recetas();
  });

  $('#form_main_receta_main #profesional').on('change',function(){
    listar_recetas();
  });

  $('#form_main_receta_main #fecha_b').on('change',function(){
    listar_recetas();
  });

  $('#form_main_receta_main #fecha_f').on('change',function(){
    listar_recetas();
  });
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});

function funciones(){
  getEstado();
  getClientes();
  getProfesionales();
  listar_recetas();
}

//FIN CONTROLES DE ACCION
/****************************************************************************************************************************************************************/

$('#form_eliminar #Si').on('click', function(e){ // add event submit We don't want this to act as a link so cancel the link action
if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 4){
	e.preventDefault();
	if($('#form_eliminar #motivo').val() != ""){
		rollback();
	}else{
		swal({
			title: "Error",
			text: "Hay registros en blanco, por favor corregir",
			icon: "error",
            dangerMode: true,
            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});
		return false;
	}
}else{
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

//INICIO OBTENER COLABORADOR CONSULTA
function getColaboradorConsulta(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getMedicoConsulta.php';
	var colaborador_id;
	$.ajax({
	    type:'POST',
		url:url,
		async: false,
		success:function(data){
		  var datos = eval(data);
          colaborador_id = datos[0];
		}
	});
	return colaborador_id;
}
//FIN OBTENER COLABORADOR CONSULTA

//INICIO ROLLBACK
async function modal_rollback(receta_id, pacientes_id) {
    try {
        const nombrePaciente = await consultarNombre(pacientes_id); // Espera a que se resuelva la promesa
        const numeroReceta = await getNumeroReceta(receta_id); // Espera a que se resuelva la promesa
        
        swal({
            title: "¿Esta seguro?",
            text: "¿Desea anular la factura para este registro: Paciente: " + nombrePaciente + ". Receta N°: " + numeroReceta + "?",
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
                    text: "¡Sí, anular la receta!",
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
            rollback(receta_id, value);
        });
    } catch (error) {
        console.log(error); // Si alguna promesa falla, se captura el error
    }
}

async function rollback(receta_id, comentario) {
    try {
        // Espera a que se resuelva la promesa de getFechaReceta
        var fecha = await getFechaReceta(receta_id);
        
        var hoy = new Date();
        var fecha_actual = convertDate(hoy);

        var url = '<?php echo SERVERURL; ?>php/recetas/rollback.php';

        if (fecha <= fecha_actual) {
            $.ajax({
                type: 'POST',
                url: url,
				data:{
					receta_id: receta_id,
					comentario: comentario
				},
                success: function(registro) {
                    if (registro == 1) {
                        listar_recetas();
                        swal({
                            title: "Success",
                            text: "Receta anulada correctamente",
                            icon: "success",
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera                            
                        });
                        return false;
                    } else if (registro == 2) {
                        swal({
                            title: "Error",
                            text: "Error al anular la receta",
                            icon: "error",
                            dangerMode: true,
                            closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                            closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
                        });
                        return false;
                    } else if (registro == 3) {
                        swal({
                            title: "Error",
                            text: "Error al receta ya esta anulada, no se puede procesar esta solicituda",
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
        } else {
            swal({
                title: "Error",
                text: "No se puede ejecutar esta acción fuera de esta fecha",
                icon: "error",
                dangerMode: true,
                closeOnEsc: false, // Desactiva el cierre con la tecla Esc
                closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera 
            });
        }
    } catch (error) {
        console.log(error); // Captura cualquier error que pueda ocurrir durante la ejecución de la promesa
    }
}

function consultarNombre(pacientes_id) {
    return new Promise((resolve, reject) => {
        var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';

        $.ajax({
            type: 'POST',
            url: url,
            data: { 
				pacientes_id: pacientes_id 

			},
            success: function(data) {
                resolve(data); // Resolución con el dato recibido
            },
            error: function(xhr, status, error) {
                reject("Error: " + error); // Rechazo si hay error
            }
        });
    });
}

function getNumeroReceta(receta_id) {
    return new Promise((resolve, reject) => {
        var url = '<?php echo SERVERURL; ?>php/recetas/getNumeroReceta.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: { 
				receta_id: receta_id 

			},
            success: function(data) {
                var datos = JSON.parse(data); // Convierte el JSON a objeto
                resolve(datos[0]); // Devuelve el primer elemento del array
            },
            error: function(xhr, status, error) {
                reject("Error: " + error); // Rechazo si hay error
            }
        });
    });
}

function getFechaReceta(receta_id) {
    return new Promise((resolve, reject) => {
        var url = '<?php echo SERVERURL; ?>php/recetas/getFechaReceta.php';

        $.ajax({
            type: 'POST',
            url: url,
            data: { 
				receta_id: receta_id 
			},
            success: function(data) {
                var datos = eval(data); // Evalúa la respuesta y extrae la fecha
                resolve(datos[0]); // Resuelve la promesa con la fecha
            },
            error: function(xhr, status, error) {
                reject("Error: " + error); // Rechaza la promesa si ocurre un error
            }
        });
    });
}
//INICIO ROLLBACK

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}
/******************************************************************************************************************************************************************************/
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/recetas/getEstado.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main_receta_main #estado').html("");
			$('#form_main_receta_main #estado').html(data);
       	 	$('#form_main_receta_main #estado').selectpicker('refresh');
        }
     });
}

function getClientes(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main_receta_main #clientes').html("");
			$('#form_main_receta_main #clientes').html(data);
			$('#form_main_receta_main #clientes').selectpicker('refresh');
		}
     });
}

function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getColaborador.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main_receta_main #profesional').html("");
			$('#form_main_receta_main #profesional').html(data);
			$('#form_main_receta_main #profesional').selectpicker('refresh');
		}
     });
}

var listar_recetas = function () {
	var fechai = $('#form_main_receta_main #fecha_b').val();
	var fechaf = $('#form_main_receta_main #fecha_f').val();
	var clientes = $('#form_main_receta_main #clientes').val() || '';
	var profesional = $('#form_main_receta_main #profesional').val() || '';
	var estado = $('#form_main_receta_main #estado').val() || 1;

	var limpiarTexto = function (texto) {
		if (texto === null || texto === undefined || texto === '') {
			return '';
		}

		return String(texto)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	};

	var sinDato = function (texto) {
		if (texto === null || texto === undefined || texto === '') {
			return '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background-color:#f1f1f1;">Sin dato</span>';
		}

		return limpiarTexto(texto);
	};

	var mensajeSinResultados = '' +
		'<div class="text-center py-5">' +
			'<div class="text-danger font-weight-bold" style="font-size:15px;">' +
				'<i class="fas fa-search mr-2"></i> No se encontraron resultados' +
			'</div>' +
			'<div class="text-muted mt-2" style="font-size:13px;">' +
				'Intente buscar por receta, identidad, paciente, medicamento o profesional.' +
			'</div>' +
		'</div>';

	var idioma_recetas = $.extend(true, {}, idioma_español, {
		emptyTable: mensajeSinResultados,
		zeroRecords: mensajeSinResultados
	});

	var table_reporte_recetas = $("#dataTableRecetasMain").DataTable({
		"destroy": true,
		"ajax": {
			"method": "POST",
			"url": "<?php echo SERVERURL; ?>php/recetas/llenarDataTableRecetas.php",
			"data": function (d) {
				d.fechai = fechai;
				d.fechaf = fechaf;
				d.clientes = clientes;
				d.profesional = profesional;
				d.estado = estado;
			}
		},
		"columns": [
			{
				"data": "receta_numero",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="badge px-3 py-2" style="font-size:13px; border-radius:8px; color:#005f8f; background:#f5fbff; border:1px solid #b8dfff;">' +
							'<i class="fas fa-prescription-bottle-alt mr-1"></i> ' + sinDato(data) +
						'</span>';
				}
			},
			{
				"data": "fecha",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">' +
							'<i class="fas fa-calendar-alt text-primary mr-1"></i> ' + sinDato(data) +
						'</span>';
				}
			},
			{
				"data": "identidad",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					if (data === null || data === undefined || data === '') {
						return '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background-color:#f1f1f1;">Sin identidad</span>';
					}

					return '' +
						'<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">' +
							'<i class="fas fa-id-card text-muted mr-1"></i> ' + limpiarTexto(data) +
						'</span>';
				}
			},
			{
				"data": "paciente",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<div style="line-height:1.35;">' +
							'<div class="font-weight-bold text-dark">' +
								'<i class="fas fa-user text-info mr-1"></i> ' + sinDato(data) +
							'</div>' +
						'</div>';
				}
			},
			{
				"data": "receta_id",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#8a5a00; background:#fff8e5; border:1px solid #f0ad4e;">' +
							'<i class="fas fa-hashtag mr-1"></i> ' + sinDato(data) +
						'</span>';
				}
			},
			{
				"data": "producto_nombre",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<div style="line-height:1.35;">' +
							'<div class="font-weight-bold text-dark">' +
								'<i class="fas fa-pills text-success mr-1"></i> ' + sinDato(data) +
							'</div>' +
						'</div>';
				}
			},
			{
				"data": "cantidad",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#0b7a32; background:#ecfff4; border:1px solid #b9ebcc;">' +
							'<i class="fas fa-capsules mr-1"></i> ' + sinDato(data) +
						'</span>';
				}
			},
			{
				"data": "descripcion",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					if (data === null || data === undefined || data === '') {
						return '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background-color:#f1f1f1;">Sin indicación</span>';
					}

					return '' +
						'<span class="text-dark">' +
							'<i class="fas fa-notes-medical text-primary mr-1"></i> ' + limpiarTexto(data) +
						'</span>';
				}
			},
			{
				"data": "descripcion",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					if (data === null || data === undefined || data === '') {
						return '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background-color:#f1f1f1;">Sin detalle</span>';
					}

					return '' +
						'<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">' +
							'<i class="fas fa-clipboard-list text-secondary mr-1"></i> Ver detalle' +
						'</span>';
				}
			},
			{
				"data": null,
				"orderable": false,
				"searchable": false,
				"defaultContent":
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-primary dropdown-toggle shadow-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius:6px; padding:7px 14px; font-size:13px;">' +
							'<i class="fas fa-cog mr-1"></i> Acciones' +
						'</button>' +
						'<div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius:8px;">' +
							'<a class="dropdown-item printReceta" href="#"><i class="fas fa-print text-info mr-2"></i> Imprimir</a>' +
							'<div class="dropdown-divider"></div>' +
							'<a class="dropdown-item deleteReceta text-danger" href="#"><i class="fas fa-ban mr-2"></i> Anular</a>' +
						'</div>' +
					'</div>'
			}
		],
		"lengthMenu": lengthMenu20,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_recetas,
		"dom": dom,
		"order": [[1, "desc"]],
		"buttons": [
			{
				text: '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Recetas',
				className: 'btn btn-info',
				action: function () {
					listar_recetas();
				}
			}
		],
		"drawCallback": function () {
			$('#dataTableRecetasMain tbody tr td.dataTables_empty').attr('style', 'padding: 0 !important; background:#f3f3f3;');
		}
	});

	table_reporte_recetas.search('').draw();
	$('#buscar').focus();

	print_recetas_dataTable("#dataTableRecetasMain tbody", table_reporte_recetas);
	delete_recetas_dataTable("#dataTableRecetasMain tbody", table_reporte_recetas);
}

var print_recetas_dataTable = function(tbody, table){
	$(tbody).off("click", "a.printReceta");
	$(tbody).on("click", "a.printReceta", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
			
		var params = {
			"id": data.receta_id,
			"type": "RecetaSkin",
			"db": "<?php echo DB; ?>"
		};

		viewReport(params);		
	});
}

var delete_recetas_dataTable = function(tbody, table){
	$(tbody).off("click", "a.deleteReceta");
	$(tbody).on("click", "a.deleteReceta", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_rollback(data.receta_id, data.pacientes_id)
	});
}
</script>