<?php  
	$sql = "SELECT DATE_FORMAT(a.fecha_inicio, '%d-%m-%Y') fecha, a.fecha_entrega FROM proceso_cartera a WHERE id_cartera='$id' ";
	$resultado = $conexion->query($sql);
	$row = $resultado->fetch_array();
	$sqlcat = "SELECT nom_cat FROM categoria WHERE id_cat='2' ";
	$resultcat = $conexion->query($sqlcat);
	$nomcat = $resultcat->fetch_array();
	if ($_SESSION['id_cat'] != 2 && $_SESSION['id_cat'] != 1) {
		echo  "<div class='col-md-12'><div class='alert alert-danger'>Espere hasta que la persona de <u><b>".$nomcat['nom_cat']."</b></u> rellenar este proceso!!</div></div>";
		$proceso = false;
	}
?>
<div class="col-xs-12 col-md-4">
	<form action="procesos/save_proceso_3.php" method="POST">
		<label for="acuerdo_previo">Acuerdo Previo a Contrato:</label>
		<br>
		<input type="checkbox" <?php if($proceso == false) { ?> disabled <?php } ?> name="acuerdo_previo" id="acuerdo_previo" value="1" required>
		<input type="hidden" name="id_cartera" id="id_cartera" value="<?php echo $id; ?>">
		<br>
		<label for="acuerdo_comment">Comentario:</label>
		<br>
		<textarea name="acuerdo_comment" <?php if($proceso == false) { ?> disabled <?php } ?> id="acuerdo_comment" cols="40" rows="5"></textarea>
		<input type="hidden" name="fecha_entrega" id="fecha_entregas" value="<?php echo $row['fecha_entrega']; ?>">
		<input type="hidden" name="id_user" value="<?php echo $_SESSION['uid']; ?>">
		<br>
		<input type="submit" <?php if($proceso == false) { ?> disabled <?php } ?> class="btn btn-primary" id="submit_proceso" value="Aceptar">
	</form>
</div>
<div class="col-xs-12 col-md-3 col-md-offset-2">
	<form action="update_fecha.php" method="POST" id="form_fecha" name="form_fecha">
		<label for="fecha_inicio">Fecha de Inicio</label>
		<br>
		<input type="text" class="form-control" name="fecha_inicio" id="fecha_inicio" readonly="readonly" value="<?php echo $row['fecha']; ?>">
		<br>
		<label for="fecha_entrega">Fecha de Entrega</label>
		<br>
		<input type="date" <?php if($proceso == false) { ?> disabled <?php } ?> class="form-control" name="fecha_entrega" id="fecha_entrega" value="<?php echo $row['fecha_entrega']; ?>">
		<input type="hidden" name="id_cartera" id="id_cartera" value="<?php echo $id; ?>">
		<br>
		<input type="submit" <?php if($proceso == false) { ?> disabled <?php } ?> class="btn btn-success" id="submit_fecha" value="Guardar Fecha de Entrega">
		<br>
		<div id="result_fecha"></div>
	</form>
</div>
<script src="../js/jquery-1.10.2.js"></script>
<script>
	$(function() {
		
		$("#submit_fecha").on('click', function(e) {
			e.preventDefault();
			var datos = $("#form_fecha").serialize();
			/* Act on the event */
			$.ajax({
				url: 'procesos/update_fecha.php',
				type: 'POST',
				dataType: 'json',
				data: datos,
				success: function(data){
					if(data.msj == true) {
					  $("#fecha_entregas").val(data.fecha_entrega);
		              $("#result_fecha").fadeIn('slow').html("<div class='alert alert-success'>Se Guardo Exitosamente!</div>");
		              $("#result_fecha").fadeOut('slow').html("<div class='alert alert-success'>Se Guardo Exitosamente!</div>");
		            }else{
		              $("#result_fecha").html("<div class='alert alert-danger'>No se pudo Guardar!</div>");
		            }
				},
	            beforeSend: function(){
	              $("#result_fecha").html("<div class='alert-info form-control'><img src='../../img/ajax-loader.gif' /> Loading...</div>");
	            }
			})
			.done(function() {
				console.log("success");
			})
			.fail(function() {
				console.log("error");
				$("#result_fecha").html("<div class='alert alert-danger'>ERROR!</div>");
			})
			.always(function() {
				console.log("complete");
			});
			
		});
	});
</script>