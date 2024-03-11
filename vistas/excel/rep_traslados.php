<?php
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}
/* Connect To Database*/
require_once "../db.php"; //Contiene las variables de configuracion para conectar a la base de datos
require_once "../php_conexion.php"; //Contiene funcion que conecta a la base de datos
//Fin
//Archivo de funciones PHP
require_once "../funciones.php";
//FIN
//$estado_factura = intval($_REQUEST['estado_factura']);
//$employee_id    = intval($_REQUEST['employee_id']);

//////////////OBTENES LA SUCURSAL EN LA QUE ESTAMOS LOGUEADO
$user_id   = $_SESSION['id_users'];
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users left join perfil on users.sucursal_users = perfil.id_perfil where id_users = '".$user_id."'");
$row             = mysqli_fetch_array($sqlUsuarioACT);
$nombre_sucursal         = $row['giro_empresa'];
$user_sucursal = $row['id_perfil'];


$daterange      = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['range'], ENT_QUOTES)));
$tables         = "detalle_traslado left join tbl_traslado on detalle_traslado.id_traslado = tbl_traslado.id left join productos on detalle_traslado.id_producto = productos.id_producto
left join users on tbl_traslado.id_usuario = users.id_users left join perfil on tbl_traslado.id_sucursal_destino = perfil.id_perfil  ";
$campos         = "*";
/*$sWhere         = "users.id_users=facturas_ventas.id_users_factura";
if ($estado_factura > 0) {
    $sWhere .= " and facturas_ventas.estado_factura = '" . $estado_factura . "' ";
}
if ($employee_id > 0) {
    $sWhere .= " and facturas_ventas.id_vendedor = '" . $employee_id . "' ";
}*/
 //$q = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
 //$q = trim($q);
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

    $sWhere = " tbl_traslado.fecha between '$fecha_inicial' and '$fecha_final' ";
    
    if($user_sucursal != 0){
        $sWhere .= " AND tbl_traslado.id_sucursal_origen = '".$user_sucursal."' ";
        /*if ($_REQUEST['q'] != "") {
            $sWhere .= " AND (tbl_traslado.id = '$q' OR perfil.giro_empresa like '%$q%' )";
        }*/
    }else{
        /*if ($_REQUEST['q'] != "") {
            //echo("la q no esta vacia ");
            $sWhere .= " AND (tbl_traslado.id = '$q' OR perfil.giro_empresa like '%$q%' )";
        }*/
    }
}
$sWhere .= " order by detalle_traslado.id_detalle_traslado";

$consulta  = "SELECT $campos FROM $tables WHERE $sWhere";
// NO PUEDEN HABER ECHOS echo($consulta);
$resultado = $conexion->query($consulta);
if ($resultado->num_rows > 0) {
    date_default_timezone_set('America/Guatemala');
    if (PHP_SAPI == 'cli') {
        die('Este archivo solo se puede ver desde un navegador web');
    }
    /** Se agrega la libreria PHPExcel */
    require_once 'lib/PHPExcel/PHPExcel.php';
    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();
    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("Codedrinks") //Autor
        ->setLastModifiedBy("Codedrinks") //Ultimo usuario que lo modificó
        ->setTitle("Reporte Excel con PHP y MySQL")
        ->setSubject("Reporte Excel con PHP y MySQL")
        ->setDescription("Reporte de Traslados")
        ->setKeywords("reporte Traslados")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Traslados Sucursal: ".$nombre_sucursal;
    $titulosColumnas = array('# Traslado', 'FECHA', 'PRODUCTO', 'CANTIDAD', 'DESTINO', 'USUARIO');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:F1');
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4])
        ->setCellValue('F3', $titulosColumnas[5]);
    //Se agregan los datos de los alumnos
    $i = 4;
    while ($fila = $resultado->fetch_array()) {

        /*$sql            = mysqli_query($conexion, "select nombre_cliente from clientes where id_cliente='" . $fila['id_cliente'] . "'");
        $rw             = mysqli_fetch_array($sql);
        $nombre_cliente = $rw['nombre_cliente'];
        $total          = $fila['monto_factura'];
        if ($fila['estado_factura'] != 2) {
            $estado = 'Pagado';
        } else { $estado = 'pendiente';}*/

        //$numero_factura   = $fila['id_traslado'];//$id_factura;//$row['numero_factura'];
        $fecha            = date("d/m/Y", strtotime($fila['fecha']));

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['id'])
            ->setCellValue('B' . $i, date("d/m/Y", strtotime($fila['fecha'])) )
            ->setCellValue('C' . $i, $fila['nombre_producto'])
            ->setCellValue('D' . $i, $fila['cantidad'])
            ->setCellValue('E' . $i, $fila['giro_empresa'])
            ->setCellValue('F' . $i, $fila['nombre_users'] . " " . $fila['apellido_users']);
        $i++;
    }
    $estiloTituloReporte = array(
        'font'      => array(
            'name'   => 'Verdana',
            'bold'   => true,
            'italic' => false,
            'strike' => false,
            'size'   => 16,
            'color'  => array(
                'rgb' => '1C2833', //Color de la Letra
            ),
        ),
        'fill'      => array(
            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('argb' => 'D6DBDF'),
        ),
        'borders'   => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_NONE,
            ),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation'   => 0,
            'wrap'       => true,
        ),
    );
    $estiloTituloColumnas = array(
        'font'      => array(
            'name'   => 'Arial',
            'bold'   => true,
            'italic' => false,
            'strike' => false,
            'size'   => 8,
            'color'  => array(
                'rgb' => '1C2833', //color de las letras
            ),
        ),
        'fill'      => array(
            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
            'rotation'   => 90,
            'startcolor' => array(
                'rgb' => 'D6DBDF', //color de fonto1
            ),
            'endcolor'   => array(
                'argb' => 'D6DBDF', //color de fonto2
            ),
        ),
        'borders'   => array(
            'top'    => array(
                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                'color' => array(
                    'rgb' => '143860',
                ),
            ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                'color' => array(
                    'rgb' => '143860',
                ),
            ),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap'       => true,
        ));
    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
        array(
            'font'    => array(
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'fill'    => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFd9b7f4'),
            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '3a2a47',
                    ),
                ),
            ),
        ));
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($estiloTituloColumnas);
    //$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:G" . ($i - 1));
    //1$objPHPExcel->getActiveSheet()->getStyle('F4:F' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO

    for ($i = 'A'; $i <= 'F'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Traslados');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ReporteTraslados.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    echo "<script>alert('No hay resultados para mostrar')</script>";
    echo "<script>window.close();</script>";
    echo "<script>window.location.replace('../html/bitacora_traslados.php');</script>";
    //header("Location:../html/bitacora_traslados.php");
    exit;
    //print_r('No hay resultados para mostrar, Seleccionar un Paciente');
}
