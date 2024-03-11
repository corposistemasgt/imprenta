<?php

/*-------------------------
Punto de Ventas
---------------------------*/
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";
//Inicia Control de Permisos
include "../permisos.php";
$user_id = $_SESSION['id_users'];
get_cadena($user_id);
$modulo = "Gastos";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos
//Archivo de funciones PHP
require_once "../funciones.php";
$id_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    // escaping, additionally removing everything that could be (html/javascript-) code
    
    $q        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $aColumns = array('referencia_egreso'); //Columnas de busqueda
    $sTable   = "egresos";
    $sWhere   = "";
    if ($_GET['q'] != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }
    $sWhere .= " order by referencia_egreso";
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/gastos.php';
    //main query to fetch the data
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    //loop through fetched data
    if ($numrows > 0) {

        ?>
        	

        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <tr  class="info">
                    <th>Id</th>
                    <th>Referencia</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Agregado</th>
                    <th class='text-right'>Acciones</th>

                </tr>
                <?php
while ($row = mysqli_fetch_array($query)) {
            $id_egreso          = $row['id_egreso'];
            $referencia_egreso  = $row['referencia_egreso'];
            $descripcion_egreso = $row['descripcion_egreso'];
            $monto              = $row['monto'];
            $date_added         = date('d/m/Y', strtotime($row['fecha_added']));

            ?>

    <input type="hidden" value="<?php echo $referencia_egreso; ?>" id="referencia_egreso<?php echo $id_egreso; ?>">
    <input type="hidden" value="<?php echo $descripcion_egreso; ?>" id="descripcion_egreso<?php echo $id_egreso; ?>">
    <input type="hidden" value="<?php echo $monto; ?>" id="monto<?php echo $id_egreso; ?>">

    <tr>
        <td><span class="badge badge-purple"><?php echo $id_egreso; ?></span></td>
        <td><?php echo $referencia_egreso; ?></td>
        <td><?php echo $descripcion_egreso; ?></td>
        <td><?php echo $id_moneda . '' . number_format($monto, 2); ?></td>
        <td><?php echo $date_added; ?></td>

        <td >
            <div class="btn-group dropdown">
                <button type="button" class="btn btn-warning btn-sm dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                <div class="dropdown-menu dropdown-menu-right">                  
                   <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editarGastoo" onclick="obtener_datos('<?php echo $id_egreso; ?>');"><i class='fa fa-edit'></i> Editar</a>                 
                   <a class="dropdown-item" href="#" onclick="delete_gasto('<?php echo $id_egreso; ?>')"><i class='fa fa-trash'></i> Borrar</a>                
               </div>
           </div>

       </td>

   </tr>
   <?php
}
        ?>
<tr>
    <td colspan="7">
        <span class="pull-right">
            <?php
echo paginate($reload, $page, $total_pages, $adjacents);
        ?></span>
        </td>
    </tr>
</table>
</div>
<div id="editarGastoo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><i class='fa fa-edit'></i> Editar Egreso</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="editar_gasto" name="editar_gasto">
						<div id="resultados_ajax2"></div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="mod_referencia" class="control-label">Referencia:</label>
									<input type="text" class="form-control" id="mod_referencia" name="mod_referencia"  autocomplete="off">
									<input id="mod_id" name="mod_id" type='hidden'>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="mod_monto" class="control-label">Monto</label>
									<input type="text" class="form-control" id="mod_monto" name="mod_monto" autocomplete="off">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="mod_descripcion" class="control-label">Descripción</label>
									<textarea class="form-control"  id="mod_descripcion" name="mod_descripcion" maxlength="255"  autocomplete="off" required></textarea>
								</div>
							</div>
						</div>


 

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-primary waves-effect waves-light" onclick="update_gastos()" id="actualizar_datos">Actualizar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function update_gastos()
        { 
            var id=inputValue = document.getElementById("mod_id").value;
            var monto=inputValue = document.getElementById("mod_monto").value;
            var descripcion=inputValue = document.getElementById("mod_descripcion").value;
            var referencia=inputValue = document.getElementById("mod_referencia").value;
            
            $.ajax({
                type:'POST',
                url: '../ajax/actualizar_gasto.php',
                data: {id:id,monto:monto,descripcion:descripcion,referencia:referencia },
                success:function(data){ 
		           update(1);	
                   $('#editarGastoo').modal('hide');
                   $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
                   $('.modal-backdrop').remove();	            
	                swal("Exito", "Egreso Actualizado", "success"); 
                },
                error:function(data,e){ swal("Error",data.responseText, "error");  }
            
            });
        }
        function update	(page) {
		    var q = $("#q").val();
		
		    $.ajax({
		        url: '../ajax/buscar_gasto.php?action=ajax&page=' + page + '&q=' + q,		       
		        success: function(data) {
		            $(".outer_div").html(data).fadeIn('slow');
		           // $('#editarGastoo').modal('hide');
		        }
		    })
		}
        function delete_gasto(id) {
		    //var id=inputValue = document.getElementById("mod_id").value;
            swal({
                    title: "Cuidado",
                    text: "Realmente deseas eliminar este Gasto?",
                    icon: "warning",
                    buttons: ["Cancelar", "Eliminar"],
                    dangerMode: true,
                     })
                    .then((willDelete) => {
                        if (willDelete)   
                        { 
                            $.ajax({
                                type:'POST',
                                url: '../ajax/eliminar_gasto.php',
                                data: {id:id},
                                success:function(data){ swal("Exito", "Eliminacion Exitosa", "success"); load(1); },
                                error:function(data,e){ swal("Error",data.responseText, "error");  }
                            });  
                         } 
                });
            
    
		}
    </script>
<?php
}
//Este else Fue agregado de Prueba de prodria Quitar
    else {
        ?>
    <div class="alert alert-warning alert-dismissible" role="alert" align="center">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Aviso!</strong> No hay Registro de Gastos
  </div>


  

  <?php
}
// fin else
}
?>