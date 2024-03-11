<style type="text/css">
  <!--
  table { vertical-align: top; }
  tr    { vertical-align: top; }
  td    { vertical-align: top; }
  .midnight-blue{
    background:#2c3e50;
    padding: 4px 4px 4px;
    color:white;
    font-weight:bold;
    font-size:12px;
  }
  .silver{
    background:white;
    padding: 3px 4px 3px;
  }
  .clouds{
    background:#ecf0f1;
    padding: 3px 4px 3px;
  }
  .border-top{
    border-top: solid 1px #bdc3c7;

  }
  .border-left{
    border-left: solid 1px #bdc3c7;
  }
  .border-right{
    border-right: solid 1px #bdc3c7;
  }
  .border-bottom{
    border-bottom: solid 1px #bdc3c7;
  }
  table.page_footer {width: 100%; border: none; background-color: white; padding: 2mm;border-collapse:collapse; border: none;}
}
-->
</style>
<page pageset='new' backtop='10mm' backbottom='10mm' backleft='4mm' backright='5mm' style="font-size: 10px; font-family: helvetica">
  <page_header>
  <table style="width: 100%; border: solid 0px black;" cellspacing=0>
    <tr>
      <td style="text-align: left;    width: 36%"></td>
      <td style="text-align: center;    width: 31%;font-size: 14px; font-weight: bold">Reporte de Ventas</td>
      <td style="text-align: right;    width: 33%"><?php echo date('d/m/Y'); ?></td>
    </tr>
  </table>
  </page_header>
  <?php include "encabezado_general.php";?>
  <br>
  <div>
    <?php
/*$sql1             = mysqli_query($conexion, "select * from lineas where id_linea='" . $id_categoria . "'");
$rw1              = mysqli_fetch_array($sql1);
$nombre_categoria = $rw1['nombre_linea'];
if (empty($nombre_categoria)) {
    echo "Todos";
} else {
    echo $nombre_categoria;
}*/
//echo $consulta2;
?>
  </div>
  <table class="table-bordered" style="width:100%;">
    <tr class="midnight-blue">
    <th style="text-align:center;width:13%;">Fecha</th>
    <th style="text-align:center;width:10%;">Documento</th>
    <th style="text-align:center;width:7%;">Estado</th>
    <th style="text-align:center;width:7%;">No. Interno</th>
    <th style="text-align:center;width:17%;">Cliente</th>
    <th style="text-align:center;width:10%;">NIT</th>
    <th style="text-align:center;width:7%;">Forma de Pago</th>
    <th style="text-align:center;width:10%;">Total</th>
    <th style="text-align:center;width:9%;">Usuario </th>  
    </tr>
    <?php
$sumador_total  = 0;
$simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
while ($row = mysqli_fetch_array($query)) {
    $codigo           = $row['numero_factura'];
    $nombre_producto  = $row['fecha_factura'];
    //$nombre_linea     = $row['nombre_linea'];
    $giro_empresa     = $row['factura_nombre_cliente'];
    $stock_producto   = $row['factura_nit_cliente'];
    $guid             = "Factura";
            //echo "-".$row['guid_factura']."-";  
            if( strcmp($row['guid_factura'],'null')==0 ||strcmp($row['guid_factura'],'')==0 )
            {
                $guid='Comprobante';
            }
    $costo_producto   = $guid;
    $precio_venta     = $row['nombre_users']." ".$row['apellido_users'];
    $estado="Anulado";
    $tipo="";
    if( strcmp($row['estado_factura'],'1')==0 )
    {
        $estado='Emitido';
        $precio_mayorista = $row['monto_factura'];  
   
        $sumador_total += $precio_mayorista;  
    }
    if( strcmp($row['condiciones'],'1')==0 )
    {
        $tipo='Efectivo';
    }
    elseif( strcmp($row['condiciones'],'2')==0 )
    {
        $tipo='Cheque';
    }
    elseif( strcmp($row['condiciones'],'3')==0 )
    {
        $tipo='Tarjeta';
    }
    elseif( strcmp($row['condiciones'],'4')==0 )
    {
        $tipo='Credito';
    }
    elseif( strcmp($row['condiciones'],'5')==0 )
    {
        $tipo='Envio';
    }
    elseif( strcmp($row['condiciones'],'6')==0 )
    {
        $tipo='Transferencia';
    }
    elseif( strcmp($row['condiciones'],'7')==0 )
    {
        $tipo='VisaCuotas';
    }
 
    ?>
    <tr>
    <td class='text-left'><?php echo $nombre_producto; ?></td>
    <td class='text-left'><?php echo $costo_producto; ?></td>
    <td class='text-left'><?php echo $estado; ?></td>
     <td class='text-center'><label class='label label-purple'><?php echo $codigo; ?></label></td>
     <td class='text-left'><?php echo $giro_empresa; ?></td>
     
  
     <td class='text-center'><?php echo $stock_producto ?></td>
     <td class='text-center'><?php echo $tipo?></td>
    
     
     
     <td class='text-left' style='text-align:right;'><?php echo $simbolo_moneda . '. ' . number_format($precio_mayorista, 2); ?></td>
     <td class='text-left'><?php echo $precio_venta; ?></td>
   </tr>
   <?php
}

?>
 <tr>
 
  <td style='text-align:right;border-top:3px solid #2c3e50;padding:4px;padding-top:4px;font-size:14px' colspan="9"></td>
</tr>
<tr>
 
  <td style='text-align:right;border-top:3px solid #FFFFFF;padding:4px;padding-top:4px;font-size:14px' colspan="8"><?php echo "Total de Ventas: ".$simbolo_moneda . '' . number_format($sumador_total, 2) ?></td>
</tr>
</table>
<page_footer>
<table style="width: 100%; border: solid 0px black;">
  <tr>
    <td style="text-align: left;    width: 50%"></td>
    <td style="text-align: right;    width: 50%">page [[page_cu]]/[[page_nb]]</td>
  </tr>
</table>
</page_footer>
</page>