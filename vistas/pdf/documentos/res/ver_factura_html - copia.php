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
    <?php 
    include('../documentos/descargar_factura.php');
    $id_vendedor    = intval($_SESSION['id_users']);
    /////////////////////////////////////////////////////////////////////////////////////
    $sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where facturas_ventas.id_factura = '" . $id_factura . "'");

     //un arreglo de arreglos de productos para el xml
     $listaProductos = array();
     $boolTieneAfectos = false;
     $boolTieneExentos = false;
   while ($row = mysqli_fetch_array($sql)) {
    $id_producto     = $row["id_producto"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $nombre_producto = $row['nombre_producto'];
    $precio_venta_sub   = $row['importe_venta'];
    $precio_venta_uni = $row['precio_venta'];
    $id_vendedor_db = $row['id_users'];
    $nombreVendedor =  $row['nombre_users']." ".$row['apellido_users'];
    $nombreCliente = $row['factura_nombre_cliente'];
    $nitCliente = $row['factura_nit_cliente'];
    $esGenerico = $row['esGenerico'];
    $tipoFact =  $row['tipoDocumento'];
    $estadoFactura = $row['estado_factura'];

    

    if($esGenerico === '0')
    {
        //echo($esGenerico."<p>-esGenerico === 0</p>");
        $boolTieneAfectos = true;
    }

    $batchExistente = $row['serie_factura'];
    $certificacionExistente = $row['numero_certificacion'];
    $guidExistente = $row['guid_factura'];

    
    $montoGravable = $precio_venta_sub/1.12;
    $montoGravable = round($montoGravable, 6, PHP_ROUND_HALF_EVEN); 
    $montoImpuesto = $precio_venta_sub-$montoGravable;
    $montoImpuesto = round($montoImpuesto, 6, PHP_ROUND_HALF_EVEN); 

    $impuestosDelProducto = array("IVA","1",$montoGravable,$montoImpuesto);

    if($esGenerico === '1')
    {
        //echo($esGenerico."<p>-esGenerico === 1 </p>");
        $boolTieneExentos = true;
        $montoGravable = round($precio_venta_sub, 6, PHP_ROUND_HALF_EVEN); 
        $montoImpuesto = 0;

        //nombre del impuesto 0, CodigoUnidadGravable 1  ó 2 si es exento ,MontoGravable 2, MontoImpuesto 3 
        $impuestosDelProducto = array("IVA","2",$montoGravable,$montoImpuesto);
        $nombre_producto = $nombre_producto;
        
    }

    

    //cantidad 0, Descripcion 1, bienOServicio 2,precio Unitario 3, subtotal 4, 
    // y en la ultima posicion un arreglo con los impuestos 5
    $itemsVenta = array();
    array_push($itemsVenta, $cantidad,$nombre_producto,"B",$precio_venta_uni,$precio_venta_sub,$impuestosDelProducto);
    array_push($listaProductos,$itemsVenta);
    //1guardarFactura
   }

   //1aqui certificar el documento
    //1 aqui llamar al crearXML asdf
    //funcion 
    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
  
    $sql    = mysqli_query($conexion, "select * from perfil where id_perfil = '".$id_sucursal."'");
        while ($row = mysqli_fetch_array($sql)) {
            $id     = $row["id_perfil"];
            $nitEmisor     = $row["fiscal_empresa"];
            $nombreEmisor     = $row["nombre_empresa"];
            $nombreComercial     = $row["giro_empresa"];
            $direccionEmisor     = $row["direccion"];
            $departamento     = $row["estado"];
            $municipio     = $row["ciudad"];
            $codigoEstablecimiento     = $row["codigoEstablecimiento"];
            $requestor = $row["requestor"];
            $frase     = $row["frase"];
            $escenario     = $row["escenario"];
            $regimen       = $row["regimen"];

         }
    
    //$nitEmisor = "106500503";
    //$nombreEmisor = "AGENCIA IMPRESORA, SOCIEDAD ANONIMA";
    //$codigoEstablecimiento = "1";
    //$direccionEmisor = "Ciudad";
    $codigoPostal = "16001";
    //$departamento = "GUATEMALA";
    //$municipio = "GUATEMALA";
    $esCF = false;
   
    if(trim($nombreCliente) == "" || trim($nitCliente) == "" || trim($nitCliente) == "0")
    {
        $esCF = true;
        $nitCliente = "CF";
        //echo("NOMBRE CLIEENTE:: ".$nombreCliente);
        if(trim($nombreCliente) == "")
        {
            echo("nombre CFF");
            $nombreCliente = "CF";
        }
    }
    else{$esCF = false;}
    
   
    $frasesyEscenarios =  array();//array("1", "2");

    if($boolTieneExentos  === true)
    {
        array_push($frasesyEscenarios,"4","9");
        //echo("tiene exentos");
    }

    //if($boolTieneAfectos === true)
    //{
        array_push($frasesyEscenarios,$frase,$escenario);
        //echo("tiene afectos");
    //}

    $usuario = "ADMINISTRADOR";

    $caja = 1;
    //echo("HOLAAA 12345674566");

    if($estadoFactura == 1 || $tipoFact == "FCAM")
    {
        if(trim($batchExistente) === "" && trim($certificacionExistente) === "")
        {
            
            $var = certificar($tipoFact,$nitEmisor,$nombreEmisor,$codigoEstablecimiento,$direccionEmisor,$codigoPostal,$departamento, $municipio,$nitCliente, $nombreCliente,$frasesyEscenarios,$usuario,$requestor,
            $caja,$listaProductos,$nombreComercial);

            //echo( $var['Data']."<------ este echo no se veeeee");

            $batch = $var['Batch'];
            $numero_factura = $var['Serial'];
            $guid = $var['Guid'];
            $FechaCertificacion = $var['TimeStamp'];
            $totalIva = $var['TotalIva'];
            $horaEmision = $var['HoraEmision'];
            //echo($$totalIva."------ TOTAL IVAAAAAAA  certificacion");
            //alert($FechaCertificacion."--fecha Certificacion");
            //$fechaFormateada = strtotime($FechaCertificacion);
            //echo ('<script>alert("'.$FechaCertificacion.'");</script>');
            //echo("update ".$batch."- batch  ".$numero_factura."-num fact    ".$guid."-guid");                                                                                                                       
            date("d/m/Y", strtotime($fecha_factura));
            $sql   = mysqli_query($conexion, "UPDATE facturas_ventas set serie_factura = '".$batch."', guid_factura = '".$guid."', factura_nombre_cliente = '".$nombreCliente."', factura_nit_cliente = '".$nitCliente."', numero_certificacion = '".$numero_factura."', fechaCertificacion = '".$FechaCertificacion."', totalIva = '".$totalIva."', fecha_emision = '".$horaEmision."' where id_factura = '" . $id_factura . "'");
            //echo("/update");
            
            descargar_factura($id_factura, $conexion);
        }else{
            //echo("ENTRO AL ELSE.");
            $batch =$batchExistente;
            $numero_factura = $certificacionExistente;
            $guid = $guidExistente;
            descargar_factura($id_factura, $conexion);
        }
    }
    else if($estadoFactura == 2)
    {

    }
    
    
    ////////////////////////////////////////////////////////////////////////////////////
    include "encabezado_ventas.php";
   ?>
    <br>
        
    <br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
        <tr>
           <td style="width:50%;" class='midnight-blue'>Facturar a:</td>
        </tr>
        <tr>
           <td style="width:50%;" >
            <?php
//$sql_cliente = mysqli_query($conexion, "select * from clientes where id_cliente='$id_cliente'");
//$rw_cliente  = mysqli_fetch_array($sql_cliente);
echo "Nombre:  ".$nombreCliente;
echo "<br>";
echo "NIT:  ".$nitCliente;
//echo "<br> Teléfono: ";
//echo $rw_cliente['telefono_cliente'];
//echo "<br> Email: ";
//echo $rw_cliente['email_cliente'];
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
//$sql_user = mysqli_query($conexion, "select * from users where id_users='$id_vendedor'");
//$rw_user  = mysqli_fetch_array($sql_user);
echo $nombreVendedor;//$rw_user['nombre_users'] . " " . $rw_user['apellido_users'];
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
$sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where facturas_ventas.id_factura = '" . $id_factura . "'");

     
while ($row = mysqli_fetch_array($sql)) {
    $id_producto     = $row["id_producto"];
    $codigo_producto = $row['codigo_producto'];
    $cantidad        = $row['cantidad'];
    $desc_tmp        = $row['desc_venta'];
    $nombre_producto = $row['nombre_producto'];
    $esGenerico = $row['esGenerico'];
    $FechaCertificacion = $row['fechaCertificacion'];
    $totalImpuesto = $row['totalIva'];

    

    if($esGenerico === '1')
    {
        $nombre_producto = "*".$nombre_producto;
    }
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
?>

        <tr>
            <td colspan="3" style="widtd: 85%; text-align: right;"><!--SUBTOTAL <?php /*echo $simbolo_moneda;*/ ?> --></td>
            <td style="widtd: 15%; text-align: right;"> <?php /*echo number_format($subtotal, 2); */?></td>
        </tr>
        <tr>
            <td colspan="3" style="widtd: 85%; text-align: right;"><!--IVA (<?php /* echo $impuesto;?>)% <?php echo $simbolo_moneda; */?> --></td>
            <td style="widtd: 15%; text-align: right;"> <?php /* echo number_format($totalImpuesto, 2);*/ ?></td>
        </tr>
        <tr>
            <td colspan="3" style="widtd: 85%; text-align: right;">TOTAL <?php echo $simbolo_moneda; ?> </td>
            <td style="widtd: 15%; text-align: right;"> <?php echo number_format($total_factura, 2); ?></td>
        </tr>
    </table>




    <br>
    <?php
    if($boolTieneExentos  === true )
    {
        echo("<div style='font-size:11pt;text-align:center;font-weight:bold'>*Exenta del IVA (art. 7 núm. 15 Ley del IVA)</div>");
    }
        echo("<div style='font-size:11pt;text-align:center;font-weight:bold'>$regimen</div>");
    ?>
    
    <br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;">
         <tr>
            <th style="width: 100%;text-align: left" class='midnight-blue'><?php if($estadoFactura == 1 || $tipoFact == "FCAM") echo("CERTIFICACIÓN"); else echo("ENVIO DE PRODUCTO"); ?></th>  
            <th style="width: 100%;"></th>   
        </tr>
        <tr>
        
           <td style="width:100%;" ><b><?php if($estadoFactura == 1 || $tipoFact == "FCAM") echo (" N° Autorización: ".$guid); ?></b> </td>
        </tr>
       
        <tr>

        <?php 
        
        ?>

        <td style="width:100%;" ><b><?php if($estadoFactura == 1 || $tipoFact == "FCAM") echo("Fecha de Certificación: ".$FechaCertificacion); ?></b></td>
        </tr>
        <tr>
        <td style="width:100%;" ></td>
        </tr>
        <tr>
                
        </tr>
        <tr>
        <td style="width:100%; text-align: right;" ><b><br>Corposistemas <br>NIT: 108151654</b></td>
        
        </tr>
        <tr>
        <td style="width:100%;" ></td>
        </tr>
        
    </table>
    <br>
    <br>
    <div style="font-size:11pt;text-align:center;font-weight:bold"></div>




</page>




