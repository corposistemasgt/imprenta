<?php
include "is_logged.php"; //Archivo comprueba si el usuario esta logueado
/* Connect To Database*/
require_once "../db.php";
require_once "../php_conexion.php";
#require_once "../libraries/inventory.php"; //Contiene funcion que controla stock en el inventario
//Inicia Control de Permisos
include "../permisos.php";
//Archivo de funciones PHP
require_once "../funciones.php";
$user_id = $_SESSION['id_users'];
$sucursal=get_Sucursal($user_id);



$action  = (isset($_REQUEST['action']) && $_REQUEST['action'] != null) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $daterange   = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
    $employee_id = intval($_REQUEST['employee_id']);
    $tables      = "facturas_ventas,  users";
    $campos      = "*";
    $sWhere      = "users.id_users=facturas_ventas.id_vendedor and idcierre=0 and estado_factura <> 3 ";
    if ($employee_id > 0) {
        $sWhere .= " and facturas_ventas.id_vendedor = '" . $employee_id . "' ";
    }
    else
    {
      $sWhere .= " and id_sucursal = '" . $sucursal . "' ";
    }
    if (!empty($daterange)) {
        list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
        list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
        $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
        list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
        $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

        $sWhere .= " and facturas_ventas.fecha_factura between '$fecha_inicial' and '$fecha_final' ";
    }
    $sWhere .= " order by facturas_ventas.id_factura";
//echo "SELECT count(*) AS numrows FROM $tables where $sWhere ";
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = 100; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset    = ($page - 1) * $per_page;
    //Count the total number of row in your table*/
    $count_query = mysqli_query($conexion, "SELECT count(*) AS numrows FROM $tables where $sWhere ");
    if ($row = mysqli_fetch_array($count_query)) {$numrows = $row['numrows'];} else {echo mysqli_error($conexion);}
    $total_pages = ceil($numrows / $per_page);
    $reload      = '../rep_corte_caja.php';
    //main query to fetch the data
    //$query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
    $query = mysqli_query($conexion, "SELECT $campos FROM  $tables where $sWhere ");
    //echo("SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page"." --la malvada consulta");
    //echo("SELECT $campos FROM  $tables where $sWhere LIMIT "." --la consulta");
    //loop through fetched data
  
    if ($numrows > 0) {
        ?>
  
      <div class="table-responsive">
        <?php
        $finales        = 0;
        $totalVentas    = 0;
        $totalEfectivo  = 0;
        $totalCheque    = 0;
        $totalBanco     = 0;
        $totalCredito   = 0;
        $totalTransferencia   = 0;
        $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
        while ($row = mysqli_fetch_array($query)) {
            if ($row['condiciones'] == 1) {
                $totalEfectivo += $row['monto_factura'];
            } elseif ($row['condiciones'] == 2) {
                $totalCheque += $row['monto_factura'];
            } elseif ($row['condiciones'] == 3) {
                $totalBanco += $row['monto_factura'];
            } elseif ($row['condiciones'] == 4 and $row['estado_factura'] == 2) {
                $totalCredito += $row['monto_factura'];
            }elseif ($row['condiciones'] == 6){
              $totalTransferencia += $row['monto_factura'];
            }
            $totalVentas += $row['monto_factura'];
        }
        $totalVentas=$totalEfectivo+$totalCheque+$totalBanco+$totalTransferencia;
        //---------------------------------------------------------------------------------------
        $condo=" and id_sucursal=".$sucursal;
        $abonoSql    = "SELECT * FROM creditos_abonos where idcierre=0 ".$condo." and fecha_abono
        between '$fecha_inicial' and '$fecha_final'";
        if ($employee_id > 0) 
        {
          $condo=" and id_users_abono=".$employee_id;
        }
        $abonoQuery  = $conexion->query($abonoSql);
        $total_abono = 0;
        while ($abonoResult = $abonoQuery->fetch_assoc()) {
            $total_abono += $abonoResult['abono'];
        }
//---------------------------------------------------------------------------------------
        ?>

        
        <div class="col-sm-6">
          <table class="table table-bordered" cellspacing="0" style="width: 100%;font-size: 12pt;">
            <tr class="success">
              <td style="width:100%; text-align: center; font-weight:bold;" colspan="2">Ventas</td>
            </tr>
            <tr>
             <td style="width:50%;text-align: left;">Efectivo Ventas:</td>
             <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($totalEfectivo, 2); ?></td>
           </tr>
           <tr>
             <td style="width:50%;text-align: left;">Cheque:</td>
             <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($totalCheque, 2); ?></td>
           </tr>
           <tr>
             <td style="width:50%;text-align: left;">Transferencia:</td>
             <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($totalTransferencia, 2); ?></td>
           </tr>
           <tr>
             <td style="width:50%;text-align: left;">Tarjeta:</td>
             <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($totalBanco, 2); ?></td>
           </tr>
      
           <tr>
             <td style="width:50%;text-align: right;font-weight:bold;">Total Ventas:</td>
             <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($totalVentas, 2); ?></td>
           </tr>
         </table>
       </div>
       <?php
        $totalEntrada   = 0;
        $totalSalida    = 0;
        $total_efectivo = 0;
        $cajainicial    = 0;
        $subtotal = 0;
        $date=date("Y-m-d H:i:s"); 
        if ($employee_id > 0) {
            $caja = mysqli_query($conexion, "select * from egresos where idcierre=0 and users='" . $employee_id . "' and fecha_added between '$fecha_inicial' and '$fecha_final'");
        } else {
            $caja = mysqli_query($conexion, "select * from egresos where idcierre=0 and idsucursal='".$sucursal."' and fecha_added between '$fecha_inicial' and '$fecha_final'");
        }
        while ($rw = mysqli_fetch_array($caja)) {

          $totalSalida += $rw['monto'];
          $total_efectivo = $totalSalida - $totalEntrada;
           
        }
        $aper="select monto from apertura_caja where idcierre=0";
        if ($employee_id > 0) {
          $aper.= " and idusuario='" . $employee_id . "'";
      } else {
          $aper.= " and idsucursal='".$sucursal."'";
      }
      //echo $aper;
        $inicio = mysqli_query($conexion, $aper);
        while ($rw = mysqli_fetch_array($inicio)) {

          $cajainicial = $rw['monto'];
           
        }
        $subtotal = $totalVentas- $totalSalida+ $total_abono+$cajainicial;
        ?>
        
      <div class="col-sm-6">


       <table class="table table-bordered" cellspacing="10" style="width: 100%;font-size: 12pt;">
        <tr class="success">
         <td style="width:100%; text-align: center;font-weight:bold;" colspan="2">Control de Efectivo</td>
       </tr>
      	
       <tr>
         <td style="width:50%;text-align: left;">Caja inicial:</td>
         <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($cajainicial, 2); ?></td>
       </tr>

       <tr>
         <td style="width:50%;text-align: left;">Abonos a Creditos:</td>
         <td style="width:50%; text-align: left;"><?php echo $simbolo_moneda . '' . number_format($total_abono, 2); ?></td>
       </tr>

       <tr>
         <td style="width:50%;text-align: left;">Salidas de Caja (-):</td>
         <td style="width:50%; text-align: left;">
         <div >
            <div style="width: 80%; float:left" >
              <?php echo $simbolo_moneda . '' . number_format($totalSalida, 2); ?>
            </div>
                   
          </div>                
         </td>
       </tr> 
       <tr>
             <td style="width:50%;text-align: right;font-weight:bold;">Total Caja:</td>
             <td style="width:50%; text-align: left;"><?php    echo $simbolo_moneda . '' . number_format($subtotal, 2); ?></td>
        </tr>
     </table>
   </div>
   <br>
   <div class="col-sm-12">
    


     <table class="table table-striped"  cellspacing="0" style="width: 100%;font-size: 14pt;">
       <tr>
         <td >
            <label  style="padding-left: 50px;padding-right: 50px;">Cantidad de Efectivo:</label>
						<input type="text" name="cantidad_efectivo" style="padding-left: 50px;" id="cantidad_efectivo" value="0" required autocomplete="off">					
          </td>       
          <td>
              <button type="submit" style="width:300px;" name="" onclick="accion('<?php echo $subtotal;?>','<?php echo $employee_id;?>','<?php echo $sucursal;?>');" id="guardar_cierre" class="btn btn-info btn-block btn-lg waves-effect waves-light" aria-haspopup="true" aria-expanded="false"><span class="fa fa-save"></span> Guardar</button>	    
            </div>  
        </td>
       </tr>
     </table>
   </div>

 </div>
 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
 <script>
    function accion(monto,c,suc)
    {
      
      var efectivo=inputValue = document.getElementById("cantidad_efectivo").value;
      if(efectivo==0)
      {
        swal("Error","El monto de efectivo no pude ser 0", "error");
      }
      else
      {
        console.log("efec:"+efectivo+" monto"+monto);
        if(monto==efectivo)
        {
            $.ajax({
                type:'POST',
                url: '../ajax/nuevo_cierre.php',
                data: {monto:monto,efectivo:efectivo,idusuario:c,sucursal:suc },
                success:function(data){ swal("Exito", "Cierre de Caja Exitoso", "success");  load(1);},
                error:function(data,e){ swal("Error",data.responseText, "error");  }
            });
        }
        else
        {
            if(monto>efectivo)
            { 
                swal({
                    title: "Realmente deseas realizar el cierre de caja?",
                    text: "El monto de ventas es mayor al efectivo de caja",
                    icon: "warning",
                    buttons: ["Cancelar", "Cerrar Caja"],
                    dangerMode: true,
                     })
                    .then((willDelete) => {
                        if (willDelete)   
                        { 
                            $.ajax({
                                type:'POST',
                                url: '../ajax/nuevo_cierre.php',
                                data: {monto:monto,efectivo:efectivo,idusuario:c,sucursal:suc },
                                success:function(data){ swal("Exito", "Cierre de Caja Exitoso", "success"); load(1); },
                                error:function(data,e){ swal("Error",data.responseText, "error");  }
                            });  
                         } 
                });
            }
            else
            {   
                swal({
                    title: "Realmente deseas realizar el cierre de caja?",
                    text: "El monto de efectivo es mayor al monto de ventas",
                    icon: "warning",
                    buttons: ["Cancelar", "Cerrar Caja"],
                    dangerMode: true,
                     })
                    .then((willDelete) => 
                    {
                    if (willDelete)   
                        {   
                            $.ajax({
                                type:'POST',
                                url: '../ajax/nuevo_cierre.php',
                                data: {monto:monto,efectivo:efectivo,idusuario:c,sucursal:suc },
                                success:function(data){ swal("Exito", "Cierre de Caja Exitoso", "success"); load(1);},
                                error:function(data,e){ swal("Error",data.responseText, "error");  }
                            });
                        
                        } 
                });
            }
        }   
      }
    }
    </script>
 <?php
}
}
?>

