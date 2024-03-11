<?php
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado


/*if (empty($_POST['id_sucursalTraslado'])) {
    echo("es empty el id_sucursal");
    $errors[] = "ID VACIO";
} else*/ if (1 === 1 ){//!empty($_POST['id_sucursalTraslado']) ) {

    //echo("entra al ultimo else");
    require_once "../db.php";
    require_once "../php_conexion.php";
    //Archivo de funciones PHP
    require_once "../funciones.php";
    $session_id     = session_id();

    
    //$id_sucursalTraslado     = intval($_POST['selec_sucursal']);
    $id_sucursalTraslado    = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['selec_sucursal'], ENT_QUOTES)));
    //echo("sucursal traslado id = ".$id_sucursalTraslado);

    $simbolo_moneda = get_row('perfil', 'moneda', 'id_perfil', 1);
    //Comprobamos si hay archivos en la tabla temporal
    $sql_count = mysqli_query($conexion, "select * from tmp_ventas where session_id='" . $session_id . "'");
    $count     = mysqli_num_rows($sql_count);
    if ($count == 0) {
        echo "<script>
        swal({
          title: 'No hay Productos agregados en la factura',
          text: 'Intentar nuevamente',
          type: 'error',
          confirmButtonText: 'ok'
      })</script>";
        exit;
    }

    // escaping, additionally removing everything that could be (html/javascript-) code
    $id_vendedor    = intval($_SESSION['id_users']);
    date_default_timezone_set('America/Guatemala'); 
    $date_added     = date("Y-m-d H:i:s");




    $sqlUsuarioACT        = mysqli_query($conexion, "select * from users where id_users = '".$id_vendedor."'"); //obtener el usuario activo 1aqui1
    //echo("select * from users where id_users = '".$id_vendedor."'   <- primer sql");
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];


    $sql           = mysqli_query($conexion, "select * from productos, tmp_ventas where productos.id_producto=tmp_ventas.id_producto and tmp_ventas.session_id='" . $session_id . "'");

    //$idEnvio = mysqli_query($conexion, "select * from productos, tmp_ventas where productos.id_producto=tmp_ventas.id_producto and tmp_ventas.session_id='" . $session_id . "'");
    //echo("<br> select * from productos, tmp_ventas where productos.id_producto=tmp_ventas.id_producto and tmp_ventas.session_id='" . $session_id . "' <- segunda sql");
    //echo("Antes del while  ");
    
    //echo("<br> insert into tbl_traslado values(NULL,'$id_vendedor','$id_sucursal', '$id_sucursalTraslado','$date_added')  <br> ");
    $registrar_traslado     = mysqli_query($conexion, "insert into tbl_traslado values(NULL,'$id_vendedor','$id_sucursal', '$id_sucursalTraslado','$date_added')");
    $id_tbl_traslado = $conexion->insert_id;
    while ($row = mysqli_fetch_array($sql)) {
        //echo("Dentro del while 1 <br> ");
        $id_tmp          = $row["id_tmp"];
        $id_producto     = $row['id_producto'];
        $codigo_producto = $row['codigo_producto'];
        $cantidad        = $row['cantidad_tmp'];
        //echo("<br>esta es la cantidad '$cantidad'");
        $desc_tmp        = $row['desc_tmp'];
        $nombre_producto = $row['nombre_producto']; 

        $sqlStock           = mysqli_query($conexion, "select * from stock where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursal."'" ); //and tmp_ventas.session_id='" . $session_id . "'");

        if( !$sqlStock  )
        {
            //echo("retorna false<br>");
            $cantidadStock = 0;
        }else if(mysqli_num_rows($sqlStock)==0){
            //echo("retorna 0 lineas<br>");
            $cantidadStock = 0;
        }else{
            //echo("<br> select * from stock where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursal."' <- tercer sql");
            while($row2 = mysqli_fetch_array($sqlStock)){
                $cantidadStock = $row2['cantidad_stock'];
                //echo("<br>cantidad en stock = ".$cantidadStock."  -  cantidad venta = ".$cantidad);
                $nuevaCantidad = $cantidadStock - $cantidad;
                //actualizar su cantidad (esto solo ocurre una vez, este while solo deberia funcionar una vez)
                $insert_detail           = mysqli_query($conexion, "update stock set cantidad_stock = '".$nuevaCantidad."' where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursal."'");
                $insertarTraslado = "insert into detalle_traslado values(NULL, '$id_tbl_traslado', '$id_producto', '$cantidad', '0','')";
                //echo($insertarTraslado);
                $insert_detail           = mysqli_query($conexion, $insertarTraslado);
            }
        }
        

        $sqlTrasladoAumentar =  mysqli_query($conexion, "select * from stock where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'" );


        $row3 = mysqli_fetch_array($sqlTrasladoAumentar);
        //echo("select * from stock where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'" ."-la consulta AAAA <br>");
        //echo("<br>".$row3."<- row3");
        if(!$row3){

           
            //no devuelve nada, creo
            //echo("no devuelve nada no existe este producto en la tabla stock (esto solo pasara una vez por producto y sucursal), creo<br>");
            //$sqlTrasladoAumentar =  mysqli_query($conexion, "insert into stock()" );

            $insertarInventario = "INSERT INTO stock (id_stock, id_producto_stock, id_sucursal_stock, cantidad_stock) 
            VALUES (NULL, '$id_producto', '$id_sucursalTraslado' ,'$cantidad');";
              $insert_detail = mysqli_query($conexion, $insertarInventario);
        }
        else if(empty($row3) || is_null($row3)){
            echo("no esta llena la variable <br>");
        }
        else{
            //si devuelve, creo
            //echo("<br> si devuelve cReo");
            $cantidadStock = $row3['cantidad_stock'];
            $nuevaCantidad = $cantidadStock + $cantidad;
            //actualizar su cantidad (esto solo ocurre una vez, este while solo deberia funcionar una vez)
            $insert_detail           = mysqli_query($conexion, "update stock set cantidad_stock = '".$nuevaCantidad."' where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'");
        
            while($row3 = mysqli_fetch_array($sqlTrasladoAumentar)){
                $cantidadStock = $row3['cantidad_stock'];
                $nuevaCantidad = $cantidadStock + $cantidad;
                //actualizar su cantidad (esto solo ocurre una vez, este while solo deberia funcionar una vez)
                $insert_detail           = mysqli_query($conexion, "update stock set cantidad_stock = '".$nuevaCantidad."' where id_producto_stock = '".$id_producto."' and id_sucursal_stock = '".$id_sucursalTraslado."'");
            }
        }
        
    }

    //SI TODO ESTA CORRECTO

    if ($insert_detail) {

        // AQUI DEBERIA GUARDAR EN LA TABLA DE TRASLADOS
        $delete = mysqli_query($conexion, "DELETE FROM tmp_ventas WHERE session_id='" .$session_id. "'");
        //echo "HOLA 123 todo bien";
        echo "<script>
        $('#outer_comprobante').load('../ajax/carga_correlativos.php');
        $('#resultados5').load('../ajax/carga_num_trans.php')
        $('#modal_vuelto').modal('show');
        </script>";
        #$messages[] = "Venta  ha sido Guardada satisfactoriamente.";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
    }


}

?>


















