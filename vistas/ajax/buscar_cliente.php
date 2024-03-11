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
$modulo = "Clientes";
permisos($modulo, $cadena_permisos);
//Finaliza Control de Permisos

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    // escaping, additionally removing everything that could be (html/javascript-) code
    $q        = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $aColumns = array('nombre_cliente', 'fiscal_cliente'); //Columnas de busqueda


    $sqlUsuarioACT  = mysqli_query($conexion, "select * from users inner join perfil on id_perfil = sucursal_users where id_users = '".$user_id."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];

    $sTable   = " clientes ";
    $sWhere   = "";
    if ($_GET['q'] != "") {
        $sWhere = "Where  (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR  ";
        }
        $sWhere = substr_replace($sWhere, "", -4);
        $sWhere .= ')';
    }
    $sWhere .= " order by nombre_cliente";
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 10; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
    //echo "SELECT count(*) AS numrows FROM $sTable  $sWhere";
    $row         = mysqli_fetch_array($count_query);
    $numrows     = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../html/clientes.php';
    //main query to fetch the data
    $sql   = "SELECT * FROM  $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($conexion, $sql);
    //loop through fetched data
    if ($numrows > 0) {

        ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <tr  class="info">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>NIT</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Agregado</th>
                    <th class='text-right'>Acciones</th>

                </tr>
                <?php
while ($row = mysqli_fetch_array($query)) {
            $id_cliente        = $row['id_cliente'];
            $nombre_cliente    = $row['nombre_cliente'];
            $fiscal_cliente    = $row['fiscal_cliente'];
            $telefono_cliente  = $row['telefono_cliente'];
            $email_cliente     = $row['email_cliente'];
            $direccion_cliente = $row['direccion_cliente'];
            $status_cliente    = $row['status_cliente'];
            $date_added        = date('d/m/Y', strtotime($row['date_added']));
            if ($status_cliente == 1) {
                $estado = "<span class='badge badge-success'>Activo</span>";
            } else {
                $estado = "<span class='badge badge-danger'>Inactivo</span>";
            }

            ?>

                    <input type="hidden" value="<?php echo $nombre_cliente; ?>" id="nombre_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $fiscal_cliente; ?>" id="fiscal_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $telefono_cliente; ?>" id="telefono_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $email_cliente; ?>" id="email_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $direccion_cliente; ?>" id="direccion_cliente<?php echo $id_cliente; ?>">
                    <input type="hidden" value="<?php echo $status_cliente; ?>" id="status_cliente<?php echo $id_cliente; ?>">




                    <tr>
                        <td><span class="badge badge-purple"><?php echo $id_cliente; ?></span></td>
                        <td><?php echo $nombre_cliente; ?></td>
                        <td ><?php echo $fiscal_cliente; ?></td>
                        <td><?php echo $email_cliente; ?></td>
                        <td><?php echo $estado; ?></td>
                        <td><?php echo $date_added; ?></td>

                        <td>
                            <div class="btn-group dropdown pull-right">
                                <button type="button" class="btn btn-warning btn-rounded btn-sm waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <i class='fa fa-cog'></i> <i class="caret"></i> </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                 <?php if ($permisos_editar == 1) {?>
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editarCliente" onclick="obtener_datos('<?php echo $id_cliente; ?>');"><i class='fa fa-edit'></i> Editar</a>
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#mostrarQR" onclick="mostrar_QR('<?php echo $id_cliente; ?>')"><i class='fa fa-edit'></i> QR</a>
                                 <?php }
            if ($permisos_eliminar == 1) {?>
                                 <a class="dropdown-item" href="#" data-toggle="modal" data-target="#dataDelete" data-id="<?php echo $id_cliente; ?>"><i class='fa fa-trash'></i> Borrar</a>
                                 <?php }?>


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
        <?php
}
//Este else Fue agregado de Prueba de prodria Quitar
    else {
        ?>
        <div class="alert alert-warning alert-dismissible" role="alert" align="center">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Aviso!</strong> No hay Registro de Clientes
      </div>
      <?php
}
// fin else
}
?>