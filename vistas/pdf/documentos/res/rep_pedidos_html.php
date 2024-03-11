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
<page pageset='new' backtop='10mm' backbottom='10mm' backleft='20mm' backright='20mm' style="font-size: 13px; font-family: helvetica">
  <page_header>
  <table style="width: 100%; border: solid 0px black;" cellspacing=0>
    <tr>
      <td style="text-align: left;    width: 33%"></td>
      <td style="text-align: center;    width: 34%;font-size: 14px; font-weight: bold">Reporte de Pedidos</td>
      <td style="text-align: right;    width: 33%"><?php echo (date('d/m/Y') ); ?></td>
    </tr>
  </table>
  </page_header>
  <?php include "encabezado_general.php";?>
  <br>
  <div>
    Categoria:
    <?php
$sql1             = mysqli_query($conexion, "select * from lineas where id_linea='" . $id_categoria . "'");
$rw1              = mysqli_fetch_array($sql1);
$nombre_categoria = $rw1['nombre_linea'];
if (empty($nombre_categoria)) {
    echo "Todos";
} else {
    echo $nombre_categoria;
}
?>
  </div>

  <table class="table-bordered" style="width:100%;">
    <tr class="midnight-blue">
      <th style="width:10%;">Codigo</th>
      <th style="width:10%;">Categoria</th>
      <th style="width:20%;">Nombre</th>
      <th style="width:8%;">Stock</th>
      <th style="width:8%;">Stock Minimo </th>
      <th style="width:8%;">Diferencia</th>
      <th style="width:10%;">Costo</th>
      <th style="width:10%;">Total</th>
    </tr>
    <?php
$sumador_total_costo  = 0;
$sumador_total_importe = 0;
$simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
//echo($query."123-");
while ($row = mysqli_fetch_array($query)) {
    $codigo           = $row['codigo_producto'];
    $nombre_linea     = $row['nombre_linea'];
    $nombre_producto  = $row['nombre_producto'];
    $stock   = $row['cantidad_stock'];
    $minimo   = $row['stock_min_producto'];
    $diferencia     = $minimo-$stock;
    $costo = $row['costo_producto'];
    $total  = $diferencia*$costo;

    ?>
    <tr>
     <td class='text-center'><label class='label label-purple'><?php echo $codigo; ?></label></td>
     <td class='text-left'><?php echo $nombre_linea; ?></td>
     <td class='text-left'><?php echo $nombre_producto; ?></td>
    
     <td class='text-center'><?php echo $stock ?></td>
     <td class='text-center'><?php echo $minimo ?></td>
     <td class='text-center'><?php echo $diferencia ?></td>
     <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($costo, 2); ?></td>
     <td class='text-left'><?php echo $simbolo_moneda . '' . number_format($total, 2); ?></td>
   </tr>
   <?php
}

?>
 <tr>
  <td style='text-align:right;border-top:3px solid #2c3e50;padding:4px;padding-top:4px;font-size:14px' colspan="9"><?php echo $simbolo_moneda . '' . number_format($sumador_total_importe, 2) ?></td>
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