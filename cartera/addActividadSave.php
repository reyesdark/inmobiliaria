<?php
	require_once '../conexion.php';
	$conexion = conectar();

	$id_cartera = $_POST['id_carteraActividad'];
	$tipoActividad = $_POST['tipoActividad'] ;
	$opcionActividad = $_POST['opcionActividad'] ;
	$fechaActividad = $_POST['fechaActividad'] ;
	$comentarioAcitividad = $_POST['comentarioAcitividad'] ;


	$sql = "INSERT INTO actividades (id_cartera,id_tipo_cat,opcion,fecha,comentario) VALUES ('".$id_cartera."','".$tipoActividad."','".$opcionActividad."','".$fechaActividad."','".$comentarioAcitividad."') ";

	$resultado = $conexion->query($sql);

	if ($resultado) {
		$result = array('msj' => true);
	} else {
		$result = array('msj' => false );
	}

	echo json_encode($result);
?>