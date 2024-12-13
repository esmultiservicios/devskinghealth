<script>
$(document).ready(function() {
   pagination(1);
});

function getServicio(){
    var url = '<?php echo SERVERURL; ?>php/reporte_ausencias/getServicio.php';		
		
	$.ajax({
        type: "POST",
        url: url,
	    async: true,
        success: function(data){	
		    $('#form_main #servicio').html("");
			$('#form_main #servicio').html(data);
		}			
     });	
}

function pagination(partida){
	var url = '<?php echo SERVERURL; ?>php/reporte_refereridos/paginar.php';	

	$.ajax({
		type:'POST',
		url:url,
		data:'partida='+partida,	
		success:function(data){
			var array = eval(data);
			$('#agrega-registros').html(array[0]);
			$('#pagination').html(array[1]);			
		}
	});
	return false;	
}

function reporteEXCEL(){
	var url = '<?php echo SERVERURL; ?>php/reporte_refereridos/reporte.php';
	
	window.open(url);
}

function convertDate(inputFormat) {
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [d.getFullYear(), pad(d.getMonth()+1), pad(d.getDate())].join('-');
}

$('#form_main #reporte_excel').on('click', function(e){
    e.preventDefault();
    reporteEXCEL();
});
</script>