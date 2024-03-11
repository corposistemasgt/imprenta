<?php
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/*Inicia validacion del lado del servidor*/
if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} elseif (empty($_POST['mod_codigo'])) {
    $errors[] = "Codigo vacío";
} else if (empty($_POST['mod_nombre'])) {
    $errors[] = "Nombre del producto vacío";
} else if ($_POST['mod_linea'] == "") {
    $errors[] = "Selecciona una categoria del producto";
} else if ($_POST['mod_proveedor'] == "") {
    $errors[] = "Selecciona un Proveedor";
} else if (empty($_POST['mod_costo'])) {
    $errors[] = "Costo de Producto vacío";
} else if (empty($_POST['mod_precio'])) {
    $errors[] = "Precio de venta vacío";
} 
/*else if (empty($_POST['mod_minimo'])) {
    $errors[] = "Stock minimo vacío";
}*/ 
else if ($_POST['mod_estado'] == "") {
    $errors[] = "Selecciona el estado del producto";
} else if ($_POST['mod_impuesto'] == "") {
    $errors[] = "Selecciona el impuesto del producto";
} else if ($_POST['mod_inv'] == "") {
    $errors[] = "Selecciona el impuesto del producto";
} else if (
    !empty($_POST['mod_codigo']) &&
    !empty($_POST['mod_nombre']) &&
    $_POST['mod_linea'] != "" &&
    $_POST['mod_proveedor'] != "" &&
    //$_POST['mod_medida'] != "" &&
    $_POST['mod_inv'] != "" &&
    $_POST['mod_impuesto'] != "" &&
    $_POST['mod_estado'] != "" &&
    !empty($_POST['mod_costo']) &&
    !empty($_POST['mod_precio']) 
    //&& !empty($_POST['mod_minimo'])
) {
    /* Connect To Database*/
    require_once "../db.php";
    require_once "../php_conexion.php";
    // escaping, additionally removing everything that could be (html/javascript-) code
    $codigo      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_codigo"], ENT_QUOTES)));
    $nombre      = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_nombre"], ENT_QUOTES)));
    $descripcion = mysqli_real_escape_string($conexion, (strip_tags($_POST["mod_descripcion"], ENT_QUOTES)));
    $linea       = intval($_POST['mod_linea']);
    $proveedor   = intval($_POST['mod_proveedor']);
    //$medida          = intval($_POST['mod_medida']);
    $inv      = intval($_POST['mod_inv']);
    $impuesto = intval($_POST['mod_impuesto']);
    $estado   = intval($_POST['mod_estado']);
    //$imp             = intval($_POST['id_imp2']);
    $costo           = floatval($_POST['mod_costo']);
    $utilidad        = floatval($_POST['mod_utilidad']);
    $precio_venta    = floatval($_POST['mod_precio']);
    $precio_mayoreo  = floatval($_POST['mod_preciom']);
    $precio_especial = floatval($_POST['mod_precioe']);
    $precio_cuatro   = floatval($_POST['mod_precioc']);
    $stock           = floatval($_POST['mod_stock']);
   // $bien           = floatval($_POST['mod_bien']);
   // $medida='UNI';
    $esGenerico='0';
    $medida='';
    $fechaVence='';

    if(isset($_POST['mod_bien']))
    {
        $bien  = $_POST['mod_bien'];
    }
    
    if(isset($_POST['mod_fecha']))
    {
        $fechaVence       = $_POST['mod_fecha'];
    }
    if(isset($_POST['mod_fecha']))
    {
        $fechaVence       = $_POST['mod_fecha'];
    }
    $medida       =''; 
    try
    {
        if(isset($_POST['mod_medida']))
    {
        $medida       = $_POST['mod_medida'];
    }
        
    }
    catch(Exception $e){}
    if(strcmp($medida,'')==0)
    {
        $medida="UNI";
    }
   /* if(isset($_POST['medida']))
    {
        $medida       = $_POST['medida'];
    }
    echo "-".$medida;*/



    if (empty($_POST['minimo'])) {
        $stock_minimo = 0;
    }else{
        $stock_minimo     = floatval($_POST['minimo']);
    }
    if(isset($_POST['mod_generico']))
    {
        $esGenerico     =  $_POST['mod_generico'];
    }
    $bien="B";
    if(strcmp($bien,'B')==0)
    {
        $bien=1;
    }
    else
    {
        $bien=0;
    }
    if($fechaVence == ''){
        //echo("Si es comillas vacias");
        $fechaVence = "NULL";
       
    }else{
        $fechaVence = "'".$fechaVence."'";
    }
  
    $id_producto     = $_POST['mod_id'];
    $sql             = "UPDATE productos SET codigo_producto='" . $codigo . "',
                                        nombre_producto='" . $nombre . "',
                                        descripcion_producto='" . $descripcion . "',
                                        id_linea_producto='" . $linea . "',
                                        id_proveedor='" . $proveedor . "',
                                        inv_producto='" . $inv . "',
                                        iva_producto='" . $impuesto . "',
                                        estado_producto='" . $estado . "',
                                        costo_producto='" . $costo . "',
                                        utilidad_producto='" . $utilidad . "',
                                        valor1_producto='" . $precio_venta . "',
                                        valor2_producto='" . $precio_mayoreo . "',
                                        valor3_producto='" . $precio_especial . "',
                                        valor4_producto='".$precio_cuatro."',
                                        stock_producto='" . $stock . "',
                                        medida='" . $medida . "',
                                        bien='" . $bien . "',
                                        stock_min_producto='" . $stock_minimo . "',
                                        esGenerico = '".$esGenerico."',
                                        date_vence = ".$fechaVence."
                                        WHERE id_producto='" . $id_producto . "'";
                                    
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "Producto ha sido actualizado satisfactoriamente.";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
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