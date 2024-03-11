<?php
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/*Inicia validacion del lado del servidor*/
if (empty($_POST['codigo'])) {
    $errors[] = "Código vacío";
} else if (empty($_POST['nombre'])) {
    $errors[] = "Nombre del producto vacío";
} else if ($_POST['linea'] == "") {
    $errors[] = "Selecciona una Linea del producto";
} else if ($_POST['proveedor'] == "") {
    $errors[] = "Selecciona un Proveedor";
} else if (empty($_POST['costo'])) {
    $errors[] = "Costo de Producto vacío";
} else if (empty($_POST['precio'])) {
    $errors[] = "Precio de venta vacío";
} 
else if ($_POST['estado'] == "") {
    $errors[] = "Selecciona el estado del producto";
} else if ($_POST['impuesto'] == "") {
    $errors[] = "Selecciona el impuesto del producto";
} else if ($_POST['inv'] == "") {
    $errors[] = "Selecciona Maneja Inventario";
} else if (
    !empty($_POST['codigo']) &&
    !empty($_POST['nombre']) &&
    $_POST['linea'] != "" &&
    $_POST['proveedor'] != "" &&
    $_POST['estado'] != "" &&
    $_POST['impuesto'] != "" &&
    $_POST['inv'] != "" &&
    !empty($_POST['costo']) &&
    !empty($_POST['precio']) 
) 
{

    require_once "../db.php";
    require_once "../php_conexion.php";
    require_once "../funciones.php";
    $codigo      = mysqli_real_escape_string($conexion, (strip_tags($_POST["codigo"], ENT_QUOTES)));
    $nombre      = mysqli_real_escape_string($conexion, (strip_tags($_POST["nombre"], ENT_QUOTES)));
    $descripcion = mysqli_real_escape_string($conexion, (strip_tags($_POST["descripcion"], ENT_QUOTES)));
    $linea       = intval($_POST['linea']);
    $proveedor   = intval($_POST['proveedor']);
    $estado      = intval($_POST['estado']);
    $impuesto    = intval($_POST['impuesto']);
    $inv         = intval($_POST['inv']);
    $costo            = floatval($_POST['costo']);
    $utilidad         = floatval($_POST['utilidad']);
    $precio_venta     = floatval($_POST['precio']);
    $precio_mayoreo   = floatval($_POST['preciom']);
    $precio_especial  = floatval($_POST['precioe']);
    $precio_cuatro  = floatval($_POST['precioc']);
    $stock            = floatval($_POST['stock']);
    if (empty($_POST['minimo'])) {
        $stock_minimo = 0;
    }else{
        $stock_minimo     = floatval($_POST['minimo']);
    }
    $date_added       = date("Y-m-d H:i:s");
    $users            = intval($_SESSION['id_users']);
    $esGenerico='0';
    $medida='';
    if(isset($_POST['medida']))
    {
        $medida       = $_POST['medida'];
    }
    $fechaVence='';
    if(isset($_POST['esGenerico']))
    {
        $esGenerico       = $_POST['esGenerico'];
    }
    if(isset($_POST['fechaVence']))
    {
        $fechaVence       = $_POST['fechaVence'];
    }

    if($fechaVence == '')
    {
         $fechaVence = "NULL"; 
    }else{
        $fechaVence = "'".$fechaVence."'";
    }
    $user_id = $_SESSION['id_users'];
    if($esGenerico === 'Si')
    {
        $esGenerico = 1;
    }else{
        $esGenerico = 0;
    }
    $query_new_insert = '';
    $sql                   = "SELECT * FROM productos WHERE codigo_producto ='" . $codigo . "';";
    $query_check_user_name = mysqli_query($conexion, $sql);
    $query_check_user      = mysqli_num_rows($query_check_user_name);
    if ($query_check_user == true) 
    {
        $errors[] = "Este producto ya existe";     
    } else 
    {
       
        $sql = "INSERT INTO productos(codigo_producto, nombre_producto, descripcion_producto,
         id_linea_producto, id_proveedor, inv_producto, iva_producto, estado_producto, 
         costo_producto, utilidad_producto, valor1_producto,valor2_producto,valor3_producto, 
         stock_producto,stock_min_producto, date_added,id_imp_producto,valor4_producto, 
         esGenerico, date_vence) VALUES ('$codigo','$nombre','$descripcion','$linea','$proveedor',
         '$inv','$impuesto','$estado','$costo','$utilidad','$precio_venta','$precio_mayoreo',
         '$precio_especial','$stock','$stock_minimo','$date_added','0','$precio_cuatro', 
         '$esGenerico', ".$fechaVence.")";
        $query_new_insert = mysqli_query($conexion, $sql);
        $ultimo_id_producto = mysqli_insert_id($conexion);
       
        $sql   = "SELECT * FROM  users  where id_users = '".$user_id."'";
        $query1 = mysqli_query($conexion, $sql);
        while ($row = mysqli_fetch_array($query1)) {
            $id_sucursalUsuario        = $row['sucursal_users'];
        }
        echo $id_sucursalUsuario;
        $sql   = "SELECT id_perfil FROM  perfil";
        $query1 = mysqli_query($conexion, $sql);
        while ($row = mysqli_fetch_array($query1)) 
        {
            $a=0;
            if(strcmp($row['id_perfil'],$id_sucursalUsuario)==0)
            {
                $a=$stock;               
            }
            $sql2 = "INSERT INTO stock(id_producto_stock, id_sucursal_stock, cantidad_stock) values(".$ultimo_id_producto.",".$row['id_perfil'].",".$a.")";
            $query_new_insert2 = mysqli_query($conexion, $sql2);
        }
    
        
        //echo($query_new_insert."--- devuelto");
        $sql         = mysqli_query($conexion, "select LAST_INSERT_ID(id_producto) as last from productos order by id_producto desc limit 0,1 ");
        $rw          = mysqli_fetch_array($sql);
        $id_producto = $rw['last'];
        //GURDAMOS LAS ENTRADAS EN EL KARDEX
        $saldo_total    = $stock * $costo;
        $sql_kardex     = mysqli_query($conexion, "select * from kardex where producto_kardex='" . $id_producto . "' order by id_kardex DESC LIMIT 1");
        $rww            = mysqli_fetch_array($sql_kardex);
        $saldoAnterior = 0;
        $totalSaldoAnterior = 0;
        if(!empty($rww))
        {
            $saldoAnterior = $rww['cant_saldo'];
            $totalSaldoAnterior = $rww['total_saldo'];
        }
        $cant_saldo     = $saldoAnterior + $stock;
        $saldo_full     = ($totalSaldoAnterior + $saldo_total);
        if($cant_saldo == 0)
        {$costo_promedio = 0;}else{
            $costo_promedio = ($totalSaldoAnterior + $saldo_total) / $cant_saldo;
        }
        $tipo           = 5;
    
        guardar_entradas($date_added, $id_producto, $stock, $costo, $saldo_total, $cant_saldo, $costo_promedio, $saldo_full, $date_added, $users, $tipo);
    
        if ($query_new_insert) {
            $messages[] = "Producto ha sido ingresado satisfactoriamente.";
        } else {
            $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
        }
    }
  
    
} else {
    $errors[] = "Error desconocido.";
}

if (isset($errors)) {

    ?>
        <div class="alert alert-danger" role="alert">
            <strong>Error!</strong>
            <?php
foreach ($errors as $error) {
        echo $error;
    }
    ?>
        </div>
        <?php
}
if (isset($messages)) {

    ?>
        <div class="alert alert-success" role="alert">
            <strong>¡Bien hecho!</strong>
            <?php
foreach ($messages as $message) {
        echo $message;
    }
    ?>
        </div>
        <?php
}

?>