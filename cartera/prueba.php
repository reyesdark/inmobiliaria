<?php
  session_start();
  require_once '../conexion.php';
  require_once '../sesion.php';
  require_once 'funciones.php';
  $conexion = conectar();
  $sql ="SELECT * FROM usuario";
  $consulta = $conexion->query($sql);

  $sqlCartera = "
        select
          a.id_cartera,
          a.nom_cartera,
          DATE_FORMAT(a.fecha_entrega, '%d-%m-%Y') as fecha,
          datediff(a.fecha_entrega, a.fecha_inicio) as dias,
          a.id_proceso,
          a.recabar_doc_mls,
          a.firma_aviso_privacidad,
          a.nuevo_contrato,
          a.estatus,
          a.fecha_entrega,
          a.promesa,
          a.fechaEsperada,
          a.fechaCierre,
          a.coment_promesa,
          b.id_cat,
          c.nom_cat,
          concat(d.nombre, ' ', d.ap_paterno) as nombre
        from
          proceso_cartera a,
          procesos b,
          categoria c,
          usuario d
        where
          not exists (select bb.estatus from proceso_cartera bb where a.id_cartera=bb.id_cartera and bb.estatus >= 1 )
        and
          a.id_proceso = b.id_proceso
        and
          b.id_cat = c.id_cat
        and
          c.id_cat = d.id_cat
    ";

  $resultado = $conexion->query($sqlCartera);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>.: Inmobiliaria :.</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/dashboard.css">
  <link rel="stylesheet" href="../css/dataTables.bootstrap.css">
  <link rel="stylesheet" href="../css/ui-lightness/jquery-ui.css">
  <script src="../js/jquery.js"></script>
  <script src="../js/jquery-ui.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/jquery.dataTables.min.js"></script>
  <script src="../js/dataTables.bootstrap.js"></script>
  <script>
  $(document).ready(function() {
    $('#table_cartera').dataTable();
    $("#promesa").on("change", function() {
       var valor = $("#promesa").val();
       if (valor === "1" || valor === "2") {
          $("#fechaCierre").attr('disabled', false);
          $("#fechaEsperada").attr('disabled', true);
          $("#coment_promesa").attr('disabled', true);
       } else {
          // deshabilitamos
          $("#fechaCierre").attr('disabled', true);
          $("#fechaEsperada").attr('disabled', false);
          $("#coment_promesa").attr('disabled', false);
       }
    });
  });
  </script>
</head>
<body>
    <?php include_once 'menu_bar.php'; ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <?php include_once 'menu.php'; ?>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h2 class="sub-header">LISTADO DISPONIBLE</h2>
          <div class="table-responsive">
          <div id="table_lista_carteras"></div>
            <table border='0' class='table table-striped' id='table_cartera'>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Cartera</th>
                  <th>Vencimiento</th>
                  <th>Encargado</th>
                  <th>Estatus</th>
                  <th>Estatus MLS</th>
                  <th>Accion</th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
            <?php
              $num = 1;
              while ($row = $resultado->fetch_array()) {
                $fecha=daysDifference($row['fecha_entrega'], date("Y-m-d"));
                if($fecha <= 0){$dias = "<div class='label alert-danger'>".$fecha." Dias ATRASADO</div>";}else if($fecha == 1){$dias = "<div class='label alert-danger'>Te Quedan ".$fecha." Dias</div>";}else if($fecha == 2){$dias= "<div class='label alert-danger'>Te Quedan ".$fecha." Dias</div>";}else{$dias = $fecha." Dias";}
                if ($row['recabar_doc_mls']==3) { $recabar_doc_mls = "<label class='label label-info'>MLS Express</label>"; }elseif ($row['recabar_doc_mls']==2) { $recabar_doc_mls = "<label class='label label-danger'>MLS (No Terminado)</label>"; }elseif ($row['recabar_doc_mls']==1) { $recabar_doc_mls = "<label class='label label-success'>MLS</label>"; }else { $recabar_doc_mls = ""; }
                if ($row['promesa'] == 4) {$promesa ="<label class='label label-success'>Negociacion</label>";}elseif ($row['promesa'] == 3) {$promesa ="<label class='label label-success'>Promesa</label>"; }elseif ($row['promesa'] == 2) {$promesa ="<label class='label label-success'>Rentada</label>"; }elseif ($row['promesa'] == 1) {$promesa ="<label label class='label-success'>Vendida</label>"; }else { $promesa = ""; }
                echo "<tr>";
                echo "<td width='25'>".$num."</td>";
                echo "<td width='230'>".$row['nom_cartera']."</td>";
                echo "<td width='100'>".$dias."</td>";
                echo "<td width='150'><label class='label label-warning'>".$row['nombre']."</label></td>";
                echo "<td width='106'>".$promesa."</td>";
                echo "<td width='130' align='center'>".$recabar_doc_mls."</td>";
                echo "<td><a href='cartera.php?id=".$row['id_cartera']."' class='btn btn-primary  btn-sm'>Cartera</a></td>";
                echo "<td><a href='javascript:void(0)' data-toggle='modal' data-target='.bs-example-modal-sm' class='btn btn-warning btn-sm'>Estatus</a></td>";
                echo "<td><a href='javascript:void(0)' data-toggle='modal' data-target='.actividad' class='btn btn-info btn-sm'>Actividad</a></td>";
                echo "<td><a href='pdf/index.php?id=".$row['id_cartera']."' target='_blank' class='btn btn-danger btn-sm'>Reporte</a></td>";
                echo "</tr>";
                $num++;
              }
            ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</body>
</html>
<!--  Inicio Dialogo -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Estatus</h4>
      </div>
      <div class="modal-body">
        <form action="addPromesaSave.php" method="POST" id="formPromesa">
    <label for="promesa">Tipo de Estatus:</label>
    <br>
    <select name="promesa" id="promesa" class="form-control" >
      <option value="">-- Seleccione --</option>
      <option value="1">Vendida</option>
      <option value="2">Rentada</option>
      <option value="3">Promesa</option>
      <option value="4">Negociacion</option>
    </select>
    <br>
    <label for="fechaEsperada">Fecha Esperada de Cierre:</label>
    <br>
    <input type="date" name="fechaEsperada" id="fechaEsperada" class="form-control"  >
    <br>
    <label for="fechaCierre">Fecha de Cierre:</label>
    <br>
    <input type="date" name="fechaCierre" id="fechaCierre" class="form-control"  />
    <br>
    <label for="coment_promesa">Comentario</label>
    <br>
    <textarea name="coment_promesa" id="coment_promesa" cols="40" rows="5"></textarea>
    <input type="hidden" name="id_carteraPromesa" id="id_carteraPromesa">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <input type="submit" class="btn btn-primary" id="btnPromesa" value="Agregar">
        </form>
      </div>
      <div id="result"></div>
    </div>
  </div>
</div>
<!-- Fin Dialogo -->
<!--  Inicio Dialogo Actividad -->
<div class="modal fade actividad" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Actividad</h4>
      </div>
      <div class="modal-body">
        <form action="addActividadSave.php" class="form-inline" role="form" method="POST" id="formActividad">
          <div class="form-group">
          <label for="opcionActividad">Opcion:</label>
          <br>
          <select name="opcionActividad" id="opcionActividad" class="form-control" required >
            <option value="">-- Seleccione --</option>
            <option value="1">Cita</option>
            <option value="2">Llamada</option>
          </select>
          </div>
          <div class="form-group">
          <label for="usuario">Quien lo hace:</label>
          <br>
          <select name="usuario" id="usuario" class="form-control" required >
            <option value="">-- Seleccione --</option>
            <?php while ($row = $consulta->fetch_assoc()) {
              echo "<option value=".$row['id_user'].">".$row['nombre']." ".$row['ap_paterno']." ".$row['ap_materno']."</option>";
            } ?>
          </select>
          </div>
          <div class="form-group">
          <label for="fechaActividad">Fecha:</label>
          <br>
          <input type="date" name="fechaActividad" id="fechaActividad" class="form-control" required >
          </div>
          <br>
          <div class="form-group">
          <label for="interesado">Nombre del Interesado:</label>
          <br>
          <input type="text" name="interesado" id="interesado" class="form-control" placeholder="Nombre de Interesado" />
          </div>
          <div class="form-group">
          <label for="tel">Telefono:</label>
          <br>
          <input type="text" name="tel" id="tel" class="form-control" placeholder="Telefono" />
          </div>
          <div class="form-group">
          <label for="email">Email:</label>
          <br>
          <input type="email" name="email" id="email" class="form-control" placeholder="Correo Electronico" />
          </div>
          <br>
          <label for="comentarioAcitividad">Comentario:</label>
          <br>
          <textarea name="comentarioAcitividad" class="form-control" id="comentarioAcitividad" cols="40" rows="5"></textarea>
          <input type="hidden" name="id_carteraActividad" id="id_carteraActividad">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <input type="submit" class="btn btn-primary" id="btnActividad" value="Agregar">
        </form>
      </div>
      <div id="resultActividad"></div>
    </div>
  </div>
</div>
<!-- Fin Dialogo Actividad -->
<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript">
$(function() {
restaFechas = function(f1,f2)
{
  var aFecha1 = f1.split('-');
  var aFecha2 = f2.split('-');
  var fFecha1 = Date.UTC(aFecha1[0],aFecha1[1]-1,aFecha1[2]);
  var fFecha2 = Date.UTC(aFecha2[0],aFecha2[1]-1,aFecha2[2]);
  var dif = fFecha2 - fFecha1;
  var dias = Math.floor(dif / (1000 * 60 * 60 * 24));
  return dias;
}
//var f1 = '10/09/2014';
//var f2='15/10/2014';
//restaFechas(f1,f2);
//=============== Ajax Carteras =========================//
var f = '<?php $hoy = date("Y-m-d"); echo $hoy; ?>';

$.ajax({
  url: 'ajaxListCartera.php',
  type: 'POST',
  dataType: 'json',
  async: false,
  cache: false,
  success: function(data){
          var html = "";
            var num = 0;
            html ="<table border='0' class='table table-striped' id='table_cartera'>";
            html +="<thead><tr><th>#</th><th>Cartera</th><th>Vencimiento</th><th>Encargado</th><th>Estatus</th><th>Estatus MLS</th><th>Accion</th><th></th><th></th><th></th></tr></thead><tbody>";
            for (i = 0; i < data.data.length; i++) {
              var fecha = restaFechas(f,data.data[i].fecha_entrega);
              if(fecha <= 0){dias = "<div class='label alert-danger'>"+fecha+" Dias ATRASADO</div>";}else if(fecha == 1){dias = "<div class='label alert-danger'>Te Quedan "+fecha+" Dias</div>";}else if(fecha == 2){dias= "<div class='label alert-danger'>Te Quedan "+fecha+" Dias</div>";}else{dias = fecha+" Dias";}
              if (data.data[i].recabar_doc_mls==3) { recabar_doc_mls = "<label class='label label-info'>MLS Express</label>"; }else if (data.data[i].recabar_doc_mls==2) { recabar_doc_mls = "<label class='label label-danger'>MLS (No Terminado)</label>"; }else if (data.data[i].recabar_doc_mls==1) { recabar_doc_mls = "<label class='label label-success'>MLS</label>"; }else { recabar_doc_mls = ""; }
              if (data.data[i].promesa == 4) {promesa ="<label class='label label-success'>Negociacion</label>";}else if (data.data[i].promesa == 3) {promesa ="<label class='label label-success'>Promesa</label>"; }else if (data.data[i].promesa == 2) {promesa ="<label class='label label-success'>Rentada</label>"; }else if (data.data[i].promesa == 1) {promesa ="<label label class='label-success'>Vendida</label>"; }else { promesa = ""; }
              num++;
              html += "<tr class='lista' id_cartera='"+data.data[i].id_cartera+"' nom_cartera='"+data.data[i].nom_cartera+"' fecha='"+data.data[i].fecha+"' dias='"+data.data[i].dias+"' id_proceso='"+data.data[i].id_proceso+"' recabar_doc_mls='"+data.data[i].recabar_doc_mls+"' firma_aviso_privacidad='"+data.data[i].firma_aviso_privacidad+"' nuevo_contrato='"+data.data[i].nuevo_contrato+"' estatus='"+data.data[i].estatus+"' fecha_entrega='"+data.data[i].fecha_entrega+"' promesa='"+data.data[i].promesa+"' fechaEsperada='"+data.data[i].fechaEsperada+"' fechaCierre='"+data.data[i].fechaCierre+"' coment_promesa='"+data.data[i].coment_promesa+"' >";
              html += "<td>" + num + "</td>";
              html += "<td>" + data.data[i].nom_cartera + "</td>";
              html += "<td>" + dias + "</td>";
              html += "<td><label class='label label-warning'>" + data.data[i].nombre + "</label></td>";
              html += "<td>" + promesa +"</td>";
              html += "<td>" + recabar_doc_mls + "</td>";
              html += "<td><a href='cartera.php?id=" + data.data[i].id_cartera + "' class='btn btn-primary btn-sm'>Cartera</a></td>";
              html += "<td><a href='javascript:void(0)' data-toggle='modal' data-target='.bs-example-modal-sm' class='btn btn-warning btn-sm'>Estatus</a></td>";
              html += "<td><a href='javascript:void(0)' data-toggle='modal' data-target='.actividad' class='btn btn-info btn-sm'>Actividad</a></td>";
              html += "<td><a href='pdf/index.php?id=" + data.data[i].id_cartera + "' target='_blank' class='btn btn-danger btn-sm'>Reporte</a></td>";
              html += "</tr>";
            }
            html += "</tbody></table>";
            $("#table_lista_carteras").html(html);
            $(".lista").on('click', function() {

            id_cartera   = $(this).attr("id_cartera");
            promesa   = $(this).attr("promesa");
            fechaEsperada   = $(this).attr("fechaEsperada");
            fechaCierre   = $(this).attr("fechaCierre");
            coment_promesa   = $(this).attr("coment_promesa");

            $("#id_carteraPromesa").val(id_cartera);
            $("#id_carteraActividad").val(id_cartera);
            $("#promesa").val(promesa);
            $("#fechaEsperada").val(fechaEsperada);
            $("#fechaCierre").val(fechaCierre);
            $("#coment_promesa").val(coment_promesa);

          });
        }
})
.done(function() {
  console.log("success");
})
.fail(function() {
  console.log("error");
})
.always(function() {
  console.log("complete");
});
$('#table_cartera').dataTable();
//=============== Fin Ajax Carteras =====================//
//===============   Ajax   ==============================//
$('#btnPromesa').on('click', function(e) {
  e.preventDefault();
  /* Act on the event */

  if ($('#promesa').val() == '') {}else{
   /* $('#result').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  }
  }else if ($('#fechaEsperada').val() == '') {
    $('#result').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  } else{*/
    var datos = $('#formPromesa').serialize();

    $.ajax({
      url: 'addPromesaSave.php',
      type: 'POST',
      dataType: 'json',
      data: datos,
    })
    .done(function(data) {
      console.log("success");
      if (data.msj == true) {
        $("#result").html("<div class='alert alert-success'>Se Agrego a la Orden</div>");
        $("#result").fadeOut('5000');
        $("#promesa").val('');
        $("#fechaEsperada").val('');
        $("#fechaCierre").val('');
        window.location ="list_cartera.php";
      }else{
        $("#result").html("<div class='alert alert-danger'>No Se Agrego a la Orden</div>");
        $("#result").fadeOut('5000');
        $("#promesa").val('');
        $("#fechaEsperada").val('');
        $("#fechaCierre").val('');
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
  }
});
//=============== Fin Ajax ==============================//
//=============== Ajax Actividad ==============================//
$('#btnActividad').on('click', function(e) {
  e.preventDefault();
  /* Act on the event */
  if ($('#opcionActividad').val() == '') {
    $('#resultActividad').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  }else if ($('#fechaActividad').val() == '') {
    $('#resultActividad').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  }else if ($('#interesado').val() == '') {
    $('#resultActividad').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  }else if ($('#tel').val() == '') {
    $('#resultActividad').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  }else if ($('#email').val() == '') {
    $('#resultActividad').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  }else if ($('#comentarioAcitividad').val() == '') {
    $('#resultActividad').html("<div class='alert alert-danger'>Hay Campos Vacios!!!</div>");
  } else{

    var datos = $('#formActividad').serialize();

    $.ajax({
      url: 'addActividadSave.php',
      type: 'POST',
      dataType: 'json',
      data: datos,
    })
    .done(function(data) {
      console.log("success");
      if (data.msj == true) {
        $("#resultActividad").html("<div class='alert alert-success'>Se Agrego a la Orden</div>");
        $("#resultActividad").fadeOut('5000');
        $("#tipoActividad").val('');
        $("#opcionActividad").val('');
        $("#fechaActividad").val('');
        $("#interesado").val('');
        $("#tel").val('');
        $("#email").val('');
        $("#comentarioAcitividad").val('');
      }else{
        $("#resultActividad").html("<div class='alert alert-danger'>No Se Agrego a la Orden</div>");
        $("#resultActividad").fadeOut('5000');
        $("#tipoActividad").val('');
        $("#opcionActividad").val('');
        $("#fechaActividad").val('');
        $("#interesado").val('');
        $("#tel").val('');
        $("#email").val('');
        $("#comentarioAcitividad").val('');
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });

  }
});
//=============== Fin Ajax Actividad ==============================//
  });
</script>