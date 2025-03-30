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

  $('#form_main_facturacion_reportes #fecha_b').on('change',function(){
    listar_reporte_facturacion();
  });

  $('#form_main_facturacion_reportes #fecha_f').on('change',function(){
    listar_reporte_facturacion();
  });
	//FIN PAGINATION (PARA LAS BUSQUEDAS SEGUN SELECCIONES)
});
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
  listar_reporte_facturacion();
}
//FIN AGRUPAR FUNCIONES DE PACIENTES

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

var listar_reporte_facturacion = function(){
	var fechai = $('#form_main_facturacion_reportes #fecha_b').val();
	var fechaf = $('#form_main_facturacion_reportes #fecha_f').val();  
	var clientes = $('#form_main_facturacion_reportes #clientes').val() || '';
	var profesional = $('#form_main_facturacion_reportes #profesional').val() || '';
	var estado = $('#form_main_facturacion_reportes #estado').val() || 1;
	
	var table_reporte_facturacion  = $("#dataTableReporteFacturacionMain").DataTable({
		"destroy":true,	
		"ajax":{
			"method":"POST",
			"url": "<?php echo SERVERURL; ?>php/reporte_facturacion_combinado/llenarDataTableReporteFacturas.php",
            "data": function(d) {
                d.fechai = fechai;
                d.fechaf = fechaf;
                d.clientes = clientes;
                d.profesional = profesional;			
				d.estado = estado;
            }	
		},		
		"columns":[
			{
				"data": "Fecha",
				"render": function(data, type, row) {
					return '<a href="#" class="showInvoiceDetail">' + data + '</a>';
				}
			},			
			{
				"data": "Tipo",
				"render": function(data, type, row) {
					var color = data === 'Contado' ? '#FFA500' : '#9b59b6'; // Naranja para "Contado" y morado para "Crédito"
					return '<span class="tipo-documento" style="border: 2px solid ' + color + '; border-radius: 12px; padding: 5px 10px; color: ' + color + ';">' + data + '</span>';
				}
			},
			{"data": "Identidad"},			
			{"data": "Paciente"},	
			{"data": "Factura"},
			{"data": "Importe"},
			{"data": "ISV"},	
			{"data": "Descuento"},
			{"data": "Neto"},
			{"data": "origen"}
		],	
		"footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // Limpiar el contenido del footer
            $('#footer-importe').html('');
            $('#footer-isv').html('');
            $('#footer-descuento').html('');
            $('#footer-neto').html('');
            $('#tipo_pago').html('');
            $('#total_pago').html('');

            // Función para calcular la suma de una columna específica
            var sumaColumna = function(index) {
                return api.column(index, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return (parseFloat(a) || 0) + (parseFloat(b) || 0);
                    }, 0);
            };

            // Calcular totales para las columnas específicas
            var totalImporte = sumaColumna(5);
            var totalISV = sumaColumna(6);
            var totalDescuento = sumaColumna(7);
            var totalNeto = sumaColumna(8);

            var formatter = new Intl.NumberFormat('es-HN', {
                style: 'currency',
                currency: 'HNL',
                minimumFractionDigits: 2,
            });

            // Mostrar totales de las columnas
            $('#footer-importe').html(formatter.format(totalImporte));
            $('#footer-isv').html(formatter.format(totalISV));
            $('#footer-descuento').html(formatter.format(totalDescuento));
            $('#footer-neto').html(formatter.format(totalNeto));
        },
		"lengthMenu": lengthMenu20,
		"stateSave": true,
		"bDestroy": true,		
		"language": idioma_español,//esta se encuenta en el archivo main.js
		"dom": dom,			
		"buttons":[		
			{
				text:      '<i class="fas fa-sync-alt fa-lg"></i> Actualizar',
				titleAttr: 'Actualizar Facturas',
				className: 'btn btn-info',
				action: 	function(){
					listar_reporte_facturacion();
				}
			},		
			{
				text:      '<i class="fa-solid fa-file-pdf fa-lg"></i> Reporte',
				titleAttr: 'Reporte de Facturación',
				className: 'btn btn-danger',
				action: 	function(){
					reporteFacturacion();
				}
			}
		]		
	});	 
	table_reporte_facturacion.search('').draw();
	$('#buscar').focus();
	
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
    var estado = $('#form_main_facturacion_reportes #estado').val() || 1;

    // Añadir los parámetros al formulario
    var params = {
        "estado": estado,
        "type": "Reporte_facturas_skin_cami",
        "fechai": fechai,
        "fechaf": fechaf,
        "db": "<?php echo DB; ?>"
    };

    viewReport(params);
}
</script>