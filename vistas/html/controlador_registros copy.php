<?php
    require_once "../db.php";
    require_once "../php_conexion.php"; 
    include "../permisos.php";
    require_once "../funciones.php";

    $idproducto = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars($_POST['cat'], ENT_QUOTES, 'UTF-8');
    $nombre = htmlspecialchars($_POST['nomb'], ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars($_POST['desc'], ENT_QUOTES, 'UTF-8');
    $costo = htmlspecialchars($_POST['cost'], ENT_QUOTES, 'UTF-8');
    $p1 = htmlspecialchars($_POST['p1'], ENT_QUOTES, 'UTF-8');
    $p2 = htmlspecialchars($_POST['p2'], ENT_QUOTES, 'UTF-8');
    $p3 = htmlspecialchars($_POST['p3'], ENT_QUOTES, 'UTF-8');
    $p4 = htmlspecialchars($_POST['p4'], ENT_QUOTES, 'UTF-8');
    $iva = htmlspecialchars($_POST['iva'], ENT_QUOTES, 'UTF-8');
    $inventario = htmlspecialchars($_POST['inv'], ENT_QUOTES, 'UTF-8');//1este valor debo registrarlo en la tabla stock
    $minimo = htmlspecialchars($_POST['min'], ENT_QUOTES, 'UTF-8');
    $esgenerico = htmlspecialchars($_POST['esgen'], ENT_QUOTES, 'UTF-8');
    $array_id = explode(",", $idproducto);
    $array_cat = explode(",", $categoria);
    $array_nom = explode(",", $nombre);
    $array_des = explode(",", $descripcion);
    $array_cost = explode(",", $costo);
    $array_p1 = explode(",", $p1);
    $array_p2 = explode(",", $p2);
    $array_p3 = explode(",", $p3);
    $array_p4 = explode(",", $p4);
    $array_iva = explode(",", $iva);
    $array_inv = explode(",", $inventario);
    $array_min = explode(",", $minimo);
    $array_esgen = explode(",",$esgenerico); 
    for($i = 0; $i < count($array_id); $i++)
    {
            $categoriaProd =  mysqli_real_escape_string($conexion, (strip_tags($array_cat[$i], ENT_QUOTES)));
            $codigo      = mysqli_real_escape_string($conexion, (strip_tags($array_id[$i], ENT_QUOTES)));
            $nombreProducto      = mysqli_real_escape_string($conexion, (strip_tags($array_nom[$i], ENT_QUOTES)));
            $descripcion = mysqli_real_escape_string($conexion, (strip_tags( $array_des[$i], ENT_QUOTES)));
            $ivaProd = mysqli_real_escape_string($conexion, (strip_tags( $array_iva[$i], ENT_QUOTES)));
            $costoProd = mysqli_real_escape_string($conexion, (strip_tags( $array_cost[$i], ENT_QUOTES)));
            $precio1Prod = mysqli_real_escape_string($conexion, (strip_tags( $array_p1[$i], ENT_QUOTES)));
            $precio2Prod = mysqli_real_escape_string($conexion, (strip_tags( $array_p2[$i], ENT_QUOTES)));
            $precio3Prod = mysqli_real_escape_string($conexion, (strip_tags( $array_p3[$i], ENT_QUOTES)));
            $precio4Prod = mysqli_real_escape_string($conexion, (strip_tags( $array_p4[$i], ENT_QUOTES)));
            $cantidadInventario = mysqli_real_escape_string($conexion, (strip_tags( $array_inv[$i], ENT_QUOTES)));
            $cantMin = mysqli_real_escape_string($conexion, (strip_tags( $array_min[$i], ENT_QUOTES)));
            $esGene0 = mysqli_real_escape_string($conexion, (strip_tags( $array_esgen[$i], ENT_QUOTES)));
          /* $esGene1 = "0";
            if($esGene0 === "s")
            {
                $esGene1 = "1";
            }else{
                $esGene1 = "0";
            }
            $date_added  = date("Y-m-d H:i:s");*/
            /*$sql = "SELECT * FROM lineas WHERE nombre_linea = '" . $categoriaProd . "';";
            $query_check_user_name = mysqli_query($conexion, $sql);
            $query_check_user      = mysqli_num_rows($query_check_user_name);
            if ($query_check_user == true) {
                $fila = $query_check_user_name ->fetch_array();
                $idCategoria = $fila['id_linea'];
                echo ($i."IFFFF \n");
            }else{
                echo ($i." else \n");
                $descripcion = $categoriaProd;
                $estado      = 1;
                $sql = "INSERT INTO lineas (nombre_linea, descripcion_linea, estado_linea, date_added)
                VALUES ('$categoriaProd','$descripcion','$estado','$date_added')";
                $query_new_insert = mysqli_query($conexion, $sql);
                $sql2 = "SELECT * FROM lineas WHERE nombre_linea = '" . $categoriaProd . "';";
                $query_check_user_name = mysqli_query($conexion, $sql2);
                $fila =  $query_check_user_name ->fetch_array();
                $idCategoria = $fila['id_linea'];
            }
            $linea = $idCategoria;*/

            $sql = "SELECT * FROM proveedores WHERE nombre_proveedor = '" . $esGene0. "';";
            $query_check_user_name = mysqli_query($conexion, $sql);
            $query_check_user      = mysqli_num_rows($query_check_user_name);
            if ($query_check_user == true) {
                $fila = $query_check_user_name ->fetch_array();
                $idCategoria = $fila['id_proveedor'];
              
            }else{
                $descripcion = $categoriaProd;
                $estado      = 1;
                $sql = "INSERT INTO proveedores (nombre_proveedor, fiscal_proveedor, web_proveedor,
                 direccion_proveedor,contacto_proveedor,email_proveedor,telefono_proveedor,
                 date_added,estado_proveedor)
                VALUES ('$esGene0','CF','','','$esGene0','','','$date_added','$estado')";
                $query_new_insert = mysqli_query($conexion, $sql);
                $sql2 = "SELECT * FROM proveedores WHERE nombre_proveedor = '" . $esGene0 . "';";
                $query_check_user_name = mysqli_query($conexion, $sql2);
                $fila =  $query_check_user_name ->fetch_array();
                $idCategoria = $fila['id_proveedor'];
            }
            $idprod = $idCategoria;
            $sql = "SELECT * FROM productos WHERE codigo_producto ='" . $codigo . "';";
            $query_check_user_name = mysqli_query($conexion, $sql);
            $query_check_user      = mysqli_num_rows($query_check_user_name);
            $proveedor = 0;
                $inv = 0;
                $ivaProd = $ivaProd/100;
                $estado = 1;
                $utilidad = 1;
            if ($query_check_user == true) {
                $fila =  $query_check_user_name ->fetch_array();
                $idProducto =  $fila['id_producto'];
                /*$sql = "UPDATE productos SET nombre_producto='" . $nombreProducto . "',
                                        descripcion_producto='" . $descripcion . "',
                                        id_linea_producto='" . $linea . "',
                                        id_proveedor='" . $proveedor . "',
                                        inv_producto='" . $inv . "',
                                        iva_producto='" . $impuesto . "',
                                        estado_producto='" . $estado . "',
                                        costo_producto='" . $costo . "',
                                        utilidad_producto='" . $utilidad . "',
                                        valor1_producto='" . $precio1Prod . "',
                                        valor2_producto='" . $precio2Prod . "',
                                        valor3_producto='" . $precio3Prod . "',
                                        valor4_producto='".$precio4Prod."',
                                        stock_producto='" . $cantidadInventario . "',
                                        stock_min_producto='" . $cantMin . "',
                                        esGenerico = '".$esGene1."'
                                        WHERE codigo_producto='" . $codigo . "'";*/
                $sql = "UPDATE productos SET id_proveedor = '".$idprod."' WHERE codigo_producto='" . $codigo . "'";
                 $query_update = mysqli_query($conexion, $sql);
                 //$query_paraStock = "INSERT INTO stock values(NULL,'$idProducto','2','$cantidadInventario' )";
                 //$insetar_inventario = mysqli_query($conexion, $query_paraStock);

            }
            else{
                
              /*  echo("INSERT INTO productos ".
                "(codigo_producto, nombre_producto, descripcion_producto, id_linea_producto, id_proveedor, inv_producto, iva_producto, estado_producto, costo_producto, utilidad_producto, valor1_producto, valor2_producto, valor3_producto, stock_producto, stock_min_producto, date_added, valor4_producto, esGenerico) VALUES ".
                "('$codigo','$nombreProducto','$descripcion','$linea','$proveedor','$inv','$ivaProd','$estado','$costoProd','$utilidad','$precio1Prod','$precio2Prod','$precio3Prod','$cantidadInventario','$cantMin','$date_added','$precio4Prod','$esGene1')");
                $sql = "INSERT INTO productos ".
                "(codigo_producto, nombre_producto, descripcion_producto, id_linea_producto, id_proveedor, inv_producto, iva_producto, estado_producto, costo_producto, utilidad_producto, valor1_producto, valor2_producto, valor3_producto, stock_producto, stock_min_producto, date_added, valor4_producto, esGenerico) VALUES ".
                "('$codigo','$nombreProducto','$descripcion','$linea','$proveedor','$inv','$ivaProd','$estado','$costoProd','$utilidad','$precio1Prod','$precio2Prod','$precio3Prod','$cantidadInventario','$cantMin','$date_added','$precio4Prod','$esGene1')";
                $query_new_insert = mysqli_query($conexion, $sql);
                $sql = "SELECT * FROM productos WHERE codigo_producto ='" . $codigo . "';";
                $query_check_user_name = mysqli_query($conexion, $sql);
                $fila =  $query_check_user_name ->fetch_array();
                $idProducto =  $fila['id_producto'];   
                $query_paraStock = "INSERT INTO stock values(NULL,'$idProducto','9','$cantidadInventario' )";
                $insetar_inventario = mysqli_query($conexion, $query_paraStock);*/
            }
          /*  $saldo_total    = $cantidadInventario * $costo;
            $sql_kardex     = mysqli_query($conexion, "select * from kardex where producto_kardex='" . $idProducto . "' order by id_kardex DESC LIMIT 1");
            $rww            = mysqli_fetch_array($sql_kardex);
            $saldoAnterior = 0;
            $totalSaldoAnterior = 0;
            if(!empty($rww))
            {
                $saldoAnterior = $rww['cant_saldo'];
                $totalSaldoAnterior = $rww['total_saldo'];
            }
            $cant_saldo     = $saldoAnterior + $cantidadInventario;
            $saldo_full     = ($totalSaldoAnterior + $saldo_total);
            if($cant_saldo == 0){
                $costo_promedio = 0;
            }
            else{
            $costo_promedio = ($totalSaldoAnterior + $saldo_total) / $cant_saldo;
            }
            $tipo = 5;
            guardar_entradas($date_added, $idProducto, $cantidadInventario, $costo, $saldo_total, $cant_saldo, $costo_promedio, $saldo_full, $date_added, 1, $tipo);
    */
        }   
?>