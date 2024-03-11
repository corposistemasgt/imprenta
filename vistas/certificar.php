
<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

 
 /* Connect To Database*/
 include "db.php";
 include "php_conexion.php";
 //Archivo de funciones PHP
 include "funciones.php";
 $id_factura = intval($_GET['id_factura']);
 $id_sucursal= intval($_GET['idsucursal']);
 /////////////////////////////////////////////////////////////////////////////////////
 $sql    = mysqli_query($conexion, "select * from facturas_ventas left join detalle_fact_ventas on 
 facturas_ventas.id_factura = detalle_fact_ventas.id_factura left join productos on productos.id_producto = 
 detalle_fact_ventas.id_producto left join users on users.id_users = facturas_ventas.id_vendedor where 
 facturas_ventas.id_factura = '" . $id_factura . "'");

  //un arreglo de arreglos de productos para el xml
 
  $listaProductos = array();
  $boolTieneAfectos = false;
  $boolTieneExentos = false;
while ($row = mysqli_fetch_array($sql)) {
 $id_producto     = $row["id_producto"];
 $codigo_producto = $row['codigo_producto'];
 $cantidad        = $row['cantidad'];
 $stock_min_producto = $row['stock_min_producto'];
 $nombre_producto = $row['nombre_producto'];
 $medida=$row['medida'];
 $precio_venta_sub   = $row['importe_venta'] ;
 $maneja_inventario = $row['inv_producto'];
 ///////
 $monto_sobre_cual_calcular_impuestos   = $row['importe_venta'];
 $descuento_enporcentaje = $row['desc_venta'];
 $descuento_endecimal = $descuento_enporcentaje/100;
 $precio_venta_uni = $row['precio_venta'];
 $monto_descuento = "0";
 if($descuento_endecimal > 0){
     $monto_descuento = $precio_venta_uni*$descuento_endecimal*$cantidad;
 }
 $precio_venta_sub = $precio_venta_uni * $cantidad;
 $id_vendedor_db = $row['id_users'];
 $nombreVendedor =  $row['nombre_users']." ".$row['apellido_users'];
 $nombreCliente = $row['factura_nombre_cliente'];
 $nitCliente = $row['factura_nit_cliente'];
 $direccion = $row['factura_direccion_cliente'];
 $esGenerico = $row['esGenerico'];
 $tipoFact =  "FACT";
 $estadoFactura = $row['estado_factura'];

 $bien_servicio = "B";
 if($stock_min_producto == 0 && $maneja_inventario == 1){ // LA CONDICION DEBE SER SI ESTOCK MIN ES 0 y SI NO MANEJA INVENTARIO //1 //!1 //1IMPORTANTE1 //1 IMPORTANTE 1
     $bien_servicio = "S";
 }

 $batchExistente = $row['serie_factura'];
 $certificacionExistente = $row['numero_certificacion'];
 $guidExistente = $row['guid_factura'];
 $gtotal = $row['monto_factura'];
 
 $montoGravable = $monto_sobre_cual_calcular_impuestos/1.12;
 $montoGravable = round($montoGravable, 6, PHP_ROUND_HALF_EVEN); 
 $montoImpuesto = $monto_sobre_cual_calcular_impuestos-$montoGravable;
 $montoImpuesto = round($montoImpuesto, 6, PHP_ROUND_HALF_EVEN); 

 $impuestosDelProducto = array("IVA","1",$montoGravable,$montoImpuesto);

 if($esGenerico === '1')
 {
     $boolTieneExentos = true;
     $montoGravable = round($precio_venta_sub, 6, PHP_ROUND_HALF_EVEN); 
     $montoImpuesto = 0;

     //nombre del impuesto 0, CodigoUnidadGravable 1  ó 2 si es exento ,MontoGravable 2, MontoImpuesto 3 
     $impuestosDelProducto = array("IVA","2",$montoGravable,$montoImpuesto);
     //$nombre_producto = $nombre_producto;
     
 }
 $itemsVenta = array();
 array_push($itemsVenta, $cantidad,$nombre_producto,$bien_servicio,$precio_venta_uni,$precio_venta_sub,$impuestosDelProducto,$monto_descuento,$medida);
 array_push($listaProductos,$itemsVenta);
}
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
 $codigoPostal = "16001";
 $esCF = false;
 if(trim($frase) == "3" and trim($escenario) == "1"){
     $tipoFact = "FPEQ";
 }

 if(trim($nombreCliente) == "" || trim($nitCliente) == "" || trim($nitCliente) == "0")
 {
     $esCF = true;
     $nitCliente = "CF";
     if(trim($nombreCliente) == "")
     {
         $nombreCliente = "CF";
     }
 }
 else{$esCF = false;}
 

 $frasesyEscenarios =  array();//array("1", "2");

 if($boolTieneExentos  === true)
 {
     array_push($frasesyEscenarios,"4","9");
 }
     array_push($frasesyEscenarios,$frase,$escenario);

     //array_push($frasesyEscenarios,"2","1");
 $usuario = "ADMINISTRADOR";

 $caja = 1;


 //if($estadoFactura != 2 || $tipoFact == "FCAM" )
// {
     if($estadoFactura == 3 && trim($guidExistente) === ""){
         return;
     }
     else if(trim($batchExistente) === "" && trim($certificacionExistente) === "")
     {
         
         $var = certificar($tipoFact,$nitEmisor,$nombreEmisor,$codigoEstablecimiento,$direccionEmisor,
         $codigoPostal, $departamento, $municipio,$nitCliente, $nombreCliente,$frasesyEscenarios,$usuario,
         $requestor,   $caja,$listaProductos,$nombreComercial,$direccion);
         
        if(strcmp($var['Result'],'true')==0)
        {
            $serie = $var['serie'];
            $numero_factura = $var['numero'];
            $guid = $var['guid'];
            $FechaCertificacion = $var['TimeStamp'];
            $totalIva = $var['TotalIva'];
            $horaEmision = $var['HoraEmision']; 
            $link = $var['Link'];                                                                                                                 
            $sql   = mysqli_query($conexion, "UPDATE facturas_ventas set serie_factura = '".$serie."', 
            guid_factura = '".$guid."', factura_nombre_cliente = '".$nombreCliente."', 
            factura_nit_cliente = '".$nitCliente."', numero_certificacion = '".$numero_factura."', 
            fechaCertificacion = '".$FechaCertificacion."', totalIva = '".$totalIva."', 
            fecha_emision = '".$horaEmision."' where id_factura = '" . $id_factura . "'");
            echo json_encode(array("resultado"=>"true","link"=>$link,"receptor"=>$nombreCliente,
            "emisor"=>$nombreEmisor,"nitemisor"=>$nitEmisor,"requestor"=>$requestor,"guid"=>$guid,
            "fecha"=>$FechaCertificacion,"tipo"=>$tipoFact,"total"=>$gtotal));
        }
        else
        {
            $descripcion = $var['Description'];
            $descripcion=base64_encode($descripcion);
            echo json_encode(array("resultado"=>"false","descripcion"=>$descripcion));
        }
         

     }
     else
     {
         $batch =$batchExistente;
         $numero_factura = $certificacionExistente;
         $guid = $guidExistente;
     }


?>