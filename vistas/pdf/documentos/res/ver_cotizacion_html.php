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
<page pageset='new' backtop='10mm' backbottom='10mm' backleft='20mm' backright='20mm' footer='page'>
    <?php include "encabezado_cot.php";?>
    <br>



    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
           <td style="width:50%;" class='midnight-blue'>COTIZACIÓN A:</td>
        </tr>
        <tr>
           <td style="width:50%;" >
            <?php
$sql_cliente = mysqli_query($conexion, "select * from clientes where id_cliente='$id_cliente'");
$rw_cliente  = mysqli_fetch_array($sql_cliente);
echo $rw_cliente['nombre_cliente'];
echo "<br>";
echo $rw_cliente['direccion_cliente'];
echo "<br> Teléfono: ";
echo $rw_cliente['telefono_cliente'];
echo "<br> Email: ";
echo $rw_cliente['email_cliente'];
?>

           </td>
        </tr>


    </table>

       <br>
        <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
           <td style="width:35%;" class='midnight-blue'>Vendedor</td>
          <td style="width:25%;" class='midnight-blue'>Fecha</td>
           <td style="width:40%;" class='midnight-blue'>Forma de Pago</td>
        </tr>
        <tr>
           <td style="width:35%;">
            <?php
$sql_user = mysqli_query($conexion, "select * from users where id_users='$id_vendedor'");
$rw_user  = mysqli_fetch_array($sql_user);
echo $rw_user['nombre_users'] . " " . $rw_user['apellido_users'];
?>
           </td>
          <td style="width:25%;"><?php echo date("d/m/Y", strtotime($fecha_factura)); ?></td>
           <td style="width:40%;" >
                <?php
if ($condiciones == 1) {echo "Efectivo";} elseif ($condiciones == 2) {echo "Cheque";} elseif ($condiciones == 3) {echo "Tarjeta";} elseif ($condiciones == 4) {echo "Crédito";}
?>
           </td>
        </tr>



    </table>
    <br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;">
        <tr>
            <th style="width: 10%;text-align:center" class='midnight-blue'>Cant.</th>
            <th style="width: 50%" class='midnight-blue'>Descripción</th>
            <th style="width: 15%;text-align: right" class='midnight-blue'>Precio Unit.</th>
            <th style="width: 15%;text-align: right" class='midnight-blue'>Total</th>

        </tr>

<?php
$nums          = 1;
$impuesto      = get_row('perfil', 'impuesto', 'id_perfil', 1);
$sumador_total = 0;
$sum_total     = 0;
$t_iva         = 0;
$sql           = mysqli_query($conexion, "select * from productos, detalle_fact_cot, facturas_cot where productos.id_producto=detalle_fact_cot.id_producto and detalle_fact_cot.numero_factura=facturas_cot.numero_factura and facturas_cot.id_factura='" . $id_factura . "'");

while ($row = mysqli_fetch_array($sql)) {
    $id_producto     = $row["id_producto"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $desc_tmp        = $row['desc_venta'];
    $nombre_producto = $row['nombre_producto'];
// control del impuesto por productos.
    if ($row['iva_producto'] == 0) {
        $p_venta   = $row['precio_venta'];
        $p_venta_f = number_format($p_venta, 2); //Formateo variables
        $p_venta_r = str_replace(",", "", $p_venta_f); //Reemplazo las comas
        $p_total   = $p_venta_r * $cantidad;
        $f_items   = rebajas($p_total, $desc_tmp); //Aplicando el descuento
        /*--------------------------------------------------------------------------------*/
        $p_total_f = number_format($f_items, 2); //Precio total formateado
        $p_total_r = str_replace(",", "", $p_total_f); //Reemplazo las comas

        $sum_total += $p_total_r; //Sumador
        $t_iva = ($sum_total * $impuesto) / 100;
        $t_iva = number_format($t_iva, 2, '.', '');
    }
    //end impuesto
    $precio_venta   = $row['precio_venta'];
    $precio_venta_f = number_format($precio_venta, 2); //Formateo variables
    $precio_venta_r = str_replace(",", "", $precio_venta_f); //Reemplazo las comas
    $precio_total   = $precio_venta_r * $cantidad;
    $final_items    = rebajas($precio_total, $desc_tmp); //Aplicando el descuento
    /*--------------------------------------------------------------------------------*/
    $precio_total_f = number_format($final_items, 2); //Precio total formateado
    $precio_total_r = str_replace(",", "", $precio_total_f); //Reemplazo las comas
    $sumador_total += $precio_total_r; //Sumador
    if ($nums % 2 == 0) {
        $clase = "clouds";
    } else {
        $clase = "silver";
    }
    ?>

        <tr>
            <td class='<?php echo $clase; ?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
            <td class='<?php echo $clase; ?>' style="width: 60%; text-align: left"><?php echo $nombre_producto; ?></td>
            <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_venta_f; ?></td>
            <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_total_f; ?></td>

        </tr>

    <?php

    $nums++;
}

$impuesto      = get_row('perfil', 'impuesto', 'id_perfil', 1);
$subtotal      = number_format($sumador_total, 2, '.', '');
$total_iva     = ($subtotal * $impuesto) / 100;
$total_iva     = number_format($total_iva, 2, '.', '') - number_format($t_iva, 2, '.', '');
$total_factura = $subtotal + $total_iva;

$mostrar_iva = $subtotal - ($subtotal / 1.12);


$mostrar_subtotal = $subtotal / 1.12;

$mostrar_total = $mostrar_iva + $mostrar_subtotal;


?>

        <tr>
            <td colspan="3" style="widtd: 85%; text-align: right;">SUBTOTAL <?php echo $simbolo_moneda; ?> </td>
            <td style="widtd: 15%; text-align: right;"> <?php echo number_format($mostrar_subtotal, 2); ?></td>
        </tr>
        <tr>
            <td colspan="3" style="widtd: 85%; text-align: right;">IVA (<?php echo $impuesto; ?>)% <?php echo $simbolo_moneda; ?> </td>
            <td style="widtd: 15%; text-align: right;"> <?php echo number_format($mostrar_iva, 2); ?></td>
        </tr><tr>
            <td colspan="3" style="widtd: 85%; text-align: right;">TOTAL <?php echo $simbolo_moneda; ?> </td>
            <td style="widtd: 15%; text-align: right;"> <?php echo number_format($mostrar_total, 2); ?></td>
        </tr>
    </table>



    <br>





</page>

