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
      <td style="text-align: center;    width: 34%;font-size: 14px; font-weight: bold">Reporte de Cierres por Usuario</td>
      <td style="text-align: right;    width: 33%"><?php echo date('d/m/Y'); ?></td>
    </tr>
  </table>
  </page_header>
  <?php include "encabezado_general.php";?>
    <br>
  <div>
    Usuario:
    <?php

$sql1     = mysqli_query($conexion, "select nombre_users, apellido_users from users where id_users='" . $employee_id . "'");
$rw1      = mysqli_fetch_array($sql1);
$fullname = $rw1['nombre_users'] . ' ' . $rw1['apellido_users'];

if (empty($fullname)) {
    echo "Todos";
} else {

  if (strcmp($fullname, ' ') == 0)
  {
    echo "Todos";
  }
  else  
  {
    echo $fullname;
  }
   
}
?>
  </div>

  <table class="table-bordered" style="width:100%;">
    <tr class="midnight-blue">
      <th style="width:45%;">Fecha y Hora</th>
      <th style="width:20%;"><Monto</th>
      <th style="width:20%;">Efectivo</th>
      <th style="width:20%;">Diferencia</th>
    </tr>
    <?php
$sumador_total  = 0;
$simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
while ($row = mysqli_fetch_array($query)) {
    $fecha= $row['fecha'];
    $monto   = $row['monto'];
    $efectivo     = $row['efectivo'];
    $diferencia= $row['diferencia'];
    ?>
      <tr>
        <td><?php echo $fecha; ?></td>
        <td><?php echo $simbolo_moneda . ' ' . number_format($monto, 2) ?></td>
        <td><?php echo $simbolo_moneda . ' ' . number_format($efectivo, 2) ?></td>
        <td><?php echo $simbolo_moneda . ' ' . number_format($diferencia, 2) ?></td>
      </tr>
      <?php
}

?>
    <tr>
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