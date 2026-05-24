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

	//INICIO PARA EL REGISTRO DE COBROS A PROFESIONALES
	$('#formCobros #generar').on('click', function(e){
		 if ($('#formCobros #comentario').val() != ""){
			e.preventDefault();
			agregarCobros();
			return false;
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
	});
	//FIN PARA EL REGISTRO DE COBROS A PROFESIONALES

    //INICIO PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
  $('#form_main_facturacion_reportes #estado').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #clientes').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #profesional').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #fecha_b').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #fecha_f').on('change',function(){
    listar_reporte_facturacion();
  });
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});

function cierreBill(){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3 || getUsuarioSistema() == 4){
		if($('#form_main_facturacion_reportes #profesional').val() == "" || $('#form_main_facturacion_reportes #profesional').val() == null){
			profesional = getColaboradorConsultaID();
		}else{
			profesional = $('#form_main_facturacion_reportes #profesional').val();
		}

		$('#formCobros')[0].reset();
		$("#formCobros #generar").attr('disabled', false);
		$('#formCobros #colaborador_id').val(profesional);
		$('#formCobros #fechai').val($('#form_main_facturacion_reportes #fecha_b').val());
		$('#formCobros #fechaf').val($('#form_main_facturacion_reportes #fecha_f').val());
		$('#formCobros #profesional').val(getColaboradorNombre(profesional));
		$('#formCobros #pro').val("Registro");
		$('#cobros').modal({
			show:true,
			keyboard: false,
			backdrop:'static'
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});
		return false;
	}		
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

//INICIO AGRUPAR FUNCIONES DE PACIENTES
function funciones(){
  getEstado();
  getClientes();
  getProfesionales();
  listar_reporte_facturacion();
}
//FIN AGRUPAR FUNCIONES DE PACIENTES

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

//INICIO FUNCION PARA OBTENER LOS COLABORADORES
function getColaboradorConsultaID(){
	var url = '<?php echo SERVERURL; ?>php/facturacion/getMedicoConsulta.php';
	var colaborador_id = '';
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		success: function(valores){
			var datos = eval(valores);
			colaborador_id = datos[0];
		}
	});
	return colaborador_id;
}
//FIN FUNCION PARA OBTENER LOS COLABORADORES

//FUNCTION PARA OBTENER EL NOMBRE DEL COLABORADOR
function getColaboradorNombre(colaborador_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getColaboradorNombre.php';
    var colaborador_nombre = '';
	$.ajax({
		type:'POST',
		url:url,
		async: false,
		data:'colaborador_id='+colaborador_id,
		success: function(valores){
			colaborador_nombre = valores;
		}
	});
	return colaborador_nombre;
}
//FIN PARA OBTENER EL NOMBRE DEL COLABORADOR

//INICIO PARA AGREGAR LA FACTURACION DE LOS USUARIOS DE FORMA MANUAL
function agregarCobros(){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/agregarCargos.php';

	$.ajax({
		type:'POST',
		url:url,
		data:$('#formCobros').serialize(),
		success: function(registro){
			if(registro == 1){
				swal({
					title: "Success",
					text: "Valores generados correctamente",
					icon: "success",
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});
				$('#formCobros #comentario').val("");
				$("#formCobros #generar").attr('disabled', true);
				listar_reporte_facturacion();
				return false;
			}else if(registro == 2){
				swal({
					title: "Error",
					text: "Error, no se puedieron generar los valores, por favor corregir",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				return false;
			}else if(registro == 3){
				swal({
					title: "Error",
					text: "Error, este registro ya existe",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
				return false;
			}else{
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
//FIN PARA AGREGAR LA FACTURACION DE LOS USUARIOS DE FORMA MANUAL

//INICIO DETALLES DE FACTURA
function invoicesDetails(facturas_id){
	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/detallesFactura.php';

	$.ajax({
		type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		success:function(data){
		   $('#mensaje_show').modal({
				show:true,
				keyboard: false,
				backdrop:'static'
		   });
		   $('#mensaje_mensaje_show').html(data);
		   $('#bad').hide();
		   $('#okay').show();
		}
	});
}
//FIN DETALLES DE FACTURA

//INICIO ROLLBACK
function modal_rollback(facturas_id, pacientes_id){
	if (getUsuarioSistema() == 1 || getUsuarioSistema() == 2 || getUsuarioSistema() == 3){
		swal({
			title: "¿Esta seguro?",
		  	text: "¿Desea anular la factura para este registro: Paciente: " + consultarNombre(pacientes_id) + ". Factura N°:  " + getNumeroFactura(facturas_id) + "?",
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
				text: "¡Sí, anular la factura!",
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
			rollback(facturas_id, value);
		});
	}else{
		swal({
			title: "Acceso Denegado",
			text: "No tiene permisos para ejecutar esta acción",
			icon: "error",
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});
		return false;
	}
}

function rollback(facturas_id,comentario){
	var fecha = getFechaFactura(facturas_id);
    var hoy = new Date();
    fecha_actual = convertDate(hoy);

	var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/rollback.php';

	if ( fecha <= fecha_actual){
	   $.ajax({
		  type:'POST',
		  url:url,
		  data:'facturas_id='+facturas_id+'&comentario='+comentario,
		  success: function(registro){
			  if(registro == 1){
			    listar_reporte_facturacion();
				swal({
					title: "Success",
					text: "Factura anulada correctamente",
					icon: "success",
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera					
				});
			    return false;
			  }else if(registro == 2){
				swal({
					title: "Error",
					text: "Error al anular la factura",
					icon: "error",
					dangerMode: true,
					closeOnEsc: false, // Desactiva el cierre con la tecla Esc
					closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
				});
			    return false;
			  }else{
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
	}else{
		swal({
			title: "Error",
			text: "No se puede ejecutar esta acción fuera de esta fecha",
			icon: "error",
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});
	}
}

function consultarNombre(pacientes_id){
    var url = '<?php echo SERVERURL; ?>php/pacientes/getNombre.php';
	var resp;

	$.ajax({
	    type:'POST',
		url:url,
		data:'pacientes_id='+pacientes_id,
		async: false,
		success:function(data){
          resp = data;
		}
	});
	return resp;
}

function getNumeroFactura(facturas_id){
    var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getNumeroFactura.php';
	var resp;

	$.ajax({
	    type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		async: false,
		success:function(data){
          resp = data;
		}
	});
	return resp;
}
//INICIO ROLLBACK

//INICIO GET FECHA FACTURA
function getFechaFactura(facturas_id){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getFechaFactura.php';
	var fecha;
	$.ajax({
	    type:'POST',
		url:url,
		data:'facturas_id='+facturas_id,
		async: false,
		success:function(data){
		  var datos = eval(data);
		  fecha = datos[0];
		}
	});

	return fecha;
}
//FIN GET FECHA FACTURA

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

/******************************************************************************************************************************************************************************/
function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/reporte_facturacion/getEstado.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main_facturacion_reportes #estado').html("");
			  $('#form_main_facturacion_reportes #estado').html(data);
        $('#form_main_facturacion_reportes #estado').selectpicker('refresh');
        }
     });
}

function getClientes(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getPacientes.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main_facturacion_reportes #clientes').html("");
			$('#form_main_facturacion_reportes #clientes').html(data);
			$('#form_main_facturacion_reportes #clientes').selectpicker('refresh');
		}
     });
}

function getProfesionales(){
    var url = '<?php echo SERVERURL; ?>php/facturacion/getColaborador.php';

	$.ajax({
		type: "POST",
		url: url,
		success: function(data){
			$('#form_main_facturacion_reportes #profesional').html("");
			$('#form_main_facturacion_reportes #profesional').html(data);
			$('#form_main_facturacion_reportes #profesional').selectpicker('refresh');
		}
     });
}

var listar_reporte_facturacion = function () {
	var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
	var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();
	var clientes = $('#form_main_facturacion_reportes #clientes').val() || '';
	var profesional = $('#form_main_facturacion_reportes #profesional').val() || '';
	var estado = $('#form_main_facturacion_reportes #estado').val() || 1;

	var formatMoney = function (valor) {
		valor = parseFloat(valor) || 0;

		return new Intl.NumberFormat('es-HN', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		}).format(valor);
	};

	var limpiarTexto = function (texto) {
		if (texto === null || texto === undefined) {
			return '';
		}

		return String(texto)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	};

	var table_reporte_facturacion = $("#dataTableReporteFacturacionMain").DataTable({
		"destroy": true,
		"ajax": {
			"method": "POST",
			"url": "<?php echo SERVERURL; ?>php/reporte_facturacion/llenarDataTableReporteFacturas.php",
			"data": function (d) {
				d.fechai = fechai;
				d.fechaf = fechaf;
				d.clientes = clientes;
				d.profesional = profesional;
				d.estado = estado;
				d.buscar = $('#buscar').val() || $('#dataTableReporteFacturacionMain_filter input').val() || '';
			}
		},
		"columns": [
			{
				"data": "fecha",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<a href="#" class="showInvoiceDetail badge badge-light border text-primary px-2 py-2" style="font-size: 12px; border-radius: 8px;">' +
							'<i class="fas fa-calendar-alt mr-1"></i>' + limpiarTexto(row.fecha1) +
						'</a>';
				}
			},
			{
				"data": "tipo_documento",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					var clase = data === 'Contado' ? 'badge-warning text-dark' : 'badge-primary';
					var icono = data === 'Contado' ? 'fas fa-money-bill-wave' : 'fas fa-hand-holding-usd';

					return '' +
						'<span class="badge ' + clase + ' px-3 py-2" style="font-size: 12px; border-radius: 20px;">' +
							'<i class="' + icono + ' mr-1"></i>' + limpiarTexto(data) +
						'</span>';
				}
			},
			{
				"data": "identidad",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="badge badge-light border px-2 py-2" style="font-size: 12px; border-radius: 8px;">' +
							'<i class="fas fa-id-card mr-1 text-muted"></i>' + limpiarTexto(data) +
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
						'<span class="font-weight-bold text-dark" style="font-size: 12px;">' +
							'<i class="fas fa-user text-info mr-1"></i>' + limpiarTexto(data) +
						'</span>';
				}
			},
			{
				"data": "factura",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="badge badge-light px-3 py-2" style="font-size: 12px; border-radius: 8px; border: 1px solid #f0ad4e; color: #8a5a00; background-color: #fff8e5;">' +
							'<i class="fas fa-file-invoice mr-1"></i>' + limpiarTexto(data) +
						'</span>';
				}
			},
			{
				"data": "precio",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return parseFloat(data) || 0;
					}

					return '<span class="font-weight-bold text-dark">' + formatMoney(data) + '</span>';
				}
			},
			{
				"data": "isv_neto",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return parseFloat(data) || 0;
					}

					return '<span class="font-weight-bold text-info">' + formatMoney(data) + '</span>';
				}
			},
			{
				"data": "descuento",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return parseFloat(data) || 0;
					}

					return '<span class="font-weight-bold text-danger">' + formatMoney(data) + '</span>';
				}
			},
			{
				"data": "total",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return parseFloat(data) || 0;
					}

					return '' +
						'<span class="badge badge-light border border-success text-success px-3 py-2" style="font-size: 13px; border-radius: 8px;">' +
							formatMoney(data) +
						'</span>';
				}
			},
			{
				"data": "servicio",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '<span class="text-dark">' + limpiarTexto(data) + '</span>';
				}
			},
			{
				"data": "profesional",
				"render": function (data, type, row) {
					if (type !== 'display') {
						return data;
					}

					return '' +
						'<span class="font-weight-bold text-dark" style="font-size: 12px;">' +
							'<i class="fas fa-user-md text-primary mr-1"></i>' + limpiarTexto(data) +
						'</span>';
				}
			},
			{
				"data": null,
				"orderable": false,
				"searchable": false,
				"defaultContent":
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-primary btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 6px;">' +
							'<i class="fas fa-cog mr-1"></i> Acciones' +
						'</button>' +
						'<div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 8px;">' +
							'<a class="dropdown-item printBill" href="#"><i class="fas fa-print text-info mr-2"></i> Imprimir</a>' +
							'<a class="dropdown-item closeBill" href="#"><i class="fas fa-calculator text-primary mr-2"></i> Cierre</a>' +
							'<div class="dropdown-divider"></div>' +
							'<a class="dropdown-item deleteBill text-danger" href="#"><i class="fas fa-ban mr-2"></i> Anular</a>' +
						'</div>' +
					'</div>'
			}
		],
		"footerCallback": function (row, data, start, end, display) {
			var api = this.api();

			$('#footer-importe').html('');
			$('#footer-isv').html('');
			$('#footer-descuento').html('');
			$('#footer-neto').html('');
			$('#tipo_pago').html('');
			$('#total_pago').html('');

			var sumaColumna = function (index) {
				return api.column(index, { page: 'current' }).data().reduce(function (a, b) {
					return (parseFloat(a) || 0) + (parseFloat(b) || 0);
				}, 0);
			};

			var totalImporte = sumaColumna(5);
			var totalISV = sumaColumna(6);
			var totalDescuento = sumaColumna(7);
			var totalNeto = sumaColumna(8);

			var formatter = new Intl.NumberFormat('es-HN', {
				style: 'currency',
				currency: 'HNL',
				minimumFractionDigits: 2
			});

			$('#footer-importe').html(formatter.format(totalImporte));
			$('#footer-isv').html(formatter.format(totalISV));
			$('#footer-descuento').html(formatter.format(totalDescuento));
			$('#footer-neto').html(formatter.format(totalNeto));
		},
		"lengthMenu": lengthMenu20,
		"stateSave": true,
		"bDestroy": true,
		"language": idioma_español,
		"dom": dom,
		"order": [[0, "desc"]],
		"buttons": [
			{
				text: '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Facturas',
				className: 'btn btn-info',
				action: function () {
					$('#buscar').val('');
					listar_reporte_facturacion();
				}
			},
			{
				text: '<i class="fas fa-calculator fa-lg"></i> Cierre',
				titleAttr: 'Cierre de Caja',
				className: 'btn btn-primary',
				action: function () {
					cierreBill();
				}
			},
			{
				text: '<i class="fas fa-file-pdf fa-lg"></i> Reporte PDF',
				titleAttr: 'Reporte de Facturación PDF',
				className: 'btn btn-danger',
				action: function () {
					reporteFacturacion();
				}
			},
			{
				text: '<i class="fas fa-file-excel fa-lg"></i> Reporte Excel',
				titleAttr: 'Reporte de Facturación Excel',
				className: 'btn btn-success',
				action: function () {
					reporteFacturacionExcel();
				}
			}
		]
	});

	table_reporte_facturacion.search('').draw();

	$('#buscar').focus();

	var timerBuscar = null;

	$(document).off('keyup', '#buscar');
	$(document).on('keyup', '#buscar', function () {
		clearTimeout(timerBuscar);

		timerBuscar = setTimeout(function () {
			table_reporte_facturacion.search('').ajax.reload();
		}, 400);
	});

	show_invoice_detail_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
	print_bill_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
	close_bill_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
	delete_bill_dataTable("#dataTableReporteFacturacionMain tbody", table_reporte_facturacion);
}

var show_invoice_detail_dataTable = function(tbody, table){
	$(tbody).off("click", "a.showInvoiceDetail");
	$(tbody).on("click", "a.showInvoiceDetail", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		swal({
			title: "Información",
			text: "Esta opción se encuentra en desarrollo",
			icon: "warning",
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});		
		//invoicesDetails(data.pacientes_id)
	});
}

var print_bill_dataTable = function(tbody, table){
	$(tbody).off("click", "a.printBill");
	$(tbody).on("click", "a.printBill", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		printBill(data.facturas_id);	
	});
}

var close_bill_dataTable = function(tbody, table){
	$(tbody).off("click", "a.closeBill");
	$(tbody).on("click", "a.closeBill", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		swal({
			title: "Información",
			text: "Esta opción se encuentra en desarrollo",
			icon: "warning",
			dangerMode: true,
			closeOnEsc: false, // Desactiva el cierre con la tecla Esc
			closeOnClickOutside: false // Desactiva el cierre al hacer clic fuera
		});
	});
}

var delete_bill_dataTable = function(tbody, table){
	$(tbody).off("click", "a.deleteBill");
	$(tbody).on("click", "a.deleteBill", function(e){
		e.preventDefault();
		var data = table.row( $(this).parents("tr") ).data();
		
		modal_rollback(data.facturas_id, data.pacientes_id)
	});
}

function reporteFacturacion() {
    var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
    var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();  
    var clientes = $('#form_main_facturacion_reportes #clientes').val();
    var profesional = $('#form_main_facturacion_reportes #profesional').val();
    var estado = $('#form_main_facturacion_reportes #estado').val() || 1;

    // Añadir los parámetros al formulario
    var params = {
        "estado": estado,
        "type": "Reporte_facturas_cami",
        "fechai": fechai,
        "fechaf": fechaf,
        "clientes": clientes,
        "profesional": profesional,
		"documento_id": 1,
        "db": "<?php echo DB; ?>"
    };

    viewReport(params);
}

function reporteFacturacionExcel() {
    var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
    var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();  
    var clientes = $('#form_main_facturacion_reportes #clientes').val();
    var profesional = $('#form_main_facturacion_reportes #profesional').val();
    var estado = $('#form_main_facturacion_reportes #estado').val() || 1;

    // Añadir los parámetros al formulario
    var params = {
        "estado": estado,
        "type": "Reporte_facturas_cami",
        "fechai": fechai,
        "fechaf": fechaf,
        "clientes": clientes,
        "profesional": profesional,
		"tipo": "Excel",
		"documento_id": 1,
        "db": "<?php echo DB; ?>"
    };

    viewReport(params);
}
</script>