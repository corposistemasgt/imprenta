<?php
include 'is_logged.php'; //Archivo verifica que el usario que intenta acceder a la URL esta logueado
/*Inicia validacion del lado del servidor*/
if (empty($_POST['nombre_empresa'])) {
    $errors[] = "Nombre de empresa esta vacío";
} else if (empty($_POST['telefono'])) {
    $errors[] = "Teléfono esta vacío";
} else if (empty($_POST['impuesto'])) {
    $errors[] = "IVA esta vacío";
} else if (empty($_POST['moneda'])) {
    $errors[] = "Moneda esta vacío";
} else if (
    !empty($_POST['nombre_empresa']) &&
    !empty($_POST['telefono']) &&
    !empty($_POST['impuesto']) &&
    !empty($_POST['moneda'])
) {
    /* Connect To Database*/
    require_once "../db.php";
    require_once "../php_conexion.php";
    // escaping, additionally removing everything that could be (html/javascript-) code
    $id_perfil = mysqli_real_escape_string($conexion, (strip_tags($_POST["id_perfil"], ENT_QUOTES)));
    $nombre_empresa = mysqli_real_escape_string($conexion, (strip_tags($_POST["nombre_empresa"], ENT_QUOTES)));
    $giro           = mysqli_real_escape_string($conexion, (strip_tags($_POST["giro"], ENT_QUOTES)));
    $fiscal         = mysqli_real_escape_string($conexion, (strip_tags($_POST["fiscal"], ENT_QUOTES)));
    $telefono       = mysqli_real_escape_string($conexion, (strip_tags($_POST["telefono"], ENT_QUOTES)));
    $email          = mysqli_real_escape_string($conexion, (strip_tags($_POST["email"], ENT_QUOTES)));
    $impuesto       = mysqli_real_escape_string($conexion, (strip_tags($_POST["impuesto"], ENT_QUOTES)));
    $nom_impuesto   = mysqli_real_escape_string($conexion, (strip_tags($_POST["nom_impuesto"], ENT_QUOTES)));
    $moneda         = mysqli_real_escape_string($conexion, (strip_tags($_POST["moneda"], ENT_QUOTES)));
    $direccion      = mysqli_real_escape_string($conexion, (strip_tags($_POST["direccion"], ENT_QUOTES)));
    $ciudad         = mysqli_real_escape_string($conexion, (strip_tags($_POST["ciudad"], ENT_QUOTES)));
    $estado         = mysqli_real_escape_string($conexion, (strip_tags($_POST["estado"], ENT_QUOTES)));
    $requestor  = mysqli_real_escape_string($conexion, (strip_tags($_POST["requestor"], ENT_QUOTES)));
    $frase  = mysqli_real_escape_string($conexion, (strip_tags($_POST["frase"], ENT_QUOTES)));
    $escenario  = mysqli_real_escape_string($conexion, (strip_tags($_POST["escenario"], ENT_QUOTES)));
    $codigo_postal  = mysqli_real_escape_string($conexion, (strip_tags($_POST["codigo_postal"], ENT_QUOTES)));

    $sql = "UPDATE perfil SET id_perfil='" . $id_perfil . "',
                                            nombre_empresa='" . $nombre_empresa . "',
                                            giro_empresa='" . $giro . "',
                                            fiscal_empresa='" . $fiscal . "',
                                            telefono='" . $telefono . "',
                                            email='" . $email . "',
                                            impuesto='" . $impuesto . "',
                                            nom_impuesto='" . $nom_impuesto . "',
                                            moneda='" . $moneda . "',
                                            direccion='" . $direccion . "',
                                            ciudad='" . $ciudad . "',
                                            estado='" . $estado . "',
                                            codigo_postal='".$codigo_postal."', 
                                            requestor='".$requestor."',
                                            frase='".$frase."',
                                            escenario='".$escenario."' 
                                            WHERE id_perfil='".$id_perfil."'";
                                           
    $query_update = mysqli_query($conexion, $sql);
    if ($query_update) {
        $messages[] = "Datos han sido actualizados satisfactoriamente.";
    } else {
        $errors[] = "Lo siento algo ha salido mal intenta nuevamente." . mysqli_error($conexion);
    }
} else {
    $errors[] = "Error desconocido.";
}

if (isset($errors)) {

    ?>
            <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
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
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
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