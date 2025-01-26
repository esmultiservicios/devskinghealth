<script>
$(document).ready(function() {
    getVigencia();
    getColaborador();
    getEstado();
	$('#label_acciones_volver').html("Cotización");
	$('#acciones_atras').addClass("active");
	$('#label_acciones_factura').html("");	
	$('.footer').show();
    $('.footer1').hide();	
});	

$('#acciones_atras').on('click', function(e){
	 e.preventDefault();
	 if($('#quoteForm #cliente').val() != "" || $('#quoteForm #cliente').val() != ""){
		swal({
		  title: "Tiene datos en la Cotización",
		  text: "¿Esta seguro que desea volver, recuerde que tiene información en la cotización la perderá?",
		  icon: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-warning",
		  confirmButtonText: "¡Si, deseo volver!",
		  closeOnConfirm: false
		},
		function(){
			$('#main_cotizacion').show();
			$('#label_acciones_factura').html("");
			$('#ctemplateCotizaciono').hide();
			$('#acciones_atras').addClass("breadcrumb-item active");
			$('#acciones_factura').removeClass("active");
			$('#quoteForm')[0].reset();
			swal.close();
			$('.footer').show();
     		$('.footer1').hide();				
		});		 			 	
	 }else{	 
		 $('#main_cotizacion').show();
		 $('#label_acciones_factura').html("");
		 $('#templateCotizacion').hide();
		 $('#acciones_atras').addClass("breadcrumb-item active");
		 $('#acciones_factura').removeClass("active");
		 $('.footer').show();
     	 $('.footer1').hide();			 	 
	 }
});

$('#form_main_cotizacion #Crearcotizacion').on('click', function(e){
	e.preventDefault();
	formCotizacion();
});

function formCotizacion(){
	 $('#quoteForm')[0].reset();
	 $('#main_cotizacion').hide();	
	 $('#templateCotizacion').show();	
	 $('#label_acciones_volver').html("Cotización");
	 $('#acciones_atras').removeClass("active");
	 $('#acciones_cotizacion').addClass("active");
	 $('#label_acciones_cotizacion').html("Cotización");
	 //$('#quoteForm #fecha').attr('disabled', false);	 
	 //limpiarTabla();
	 $('.footer').hide();
     $('.footer1').show(); 		  	 
}

function getVigencia(){
    var url = '<?php echo SERVERURL; ?>php/cotizacion/getVigencia.php';

	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#quoteForm #vigencia_quote').html("");
			$('#quoteForm #vigencia_quote').html(data);			
		}
     });
}

function getColaborador(){
    var url = '<?php echo SERVERURL; ?>php/citas/getMedico.php';		
		
	$.ajax({
        type: "POST",
        url: url,
        success: function(data){	
		    $('#form_main_cotizacion #profesional').html("");
			$('#form_main_cotizacion #profesional').html(data);		
		}			
     });	
}	

function getEstado(){
    var url = '<?php echo SERVERURL; ?>php/cotizacion/getEstado.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){
		    $('#form_main_cotizacion #estado').html("");
			$('#form_main_cotizacion #estado').html(data);		
        }
     });		
}
</script>