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
$q = mysqli_real_escape_string($conexion, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
if ($_GET['q'] != "") {
    //$sWhere = "(remesas.numero_remesa like '%$q%' or bancos.nombre_banco like '%$q%' or cuentas.numero_cuenta like '%$q%')";

}
//////consultar la sucursal
$user_id = $_SESSION['id_users'];
$sucur=$_GET['sucursalid'];
$sqlUsuarioACT        = mysqli_query($conexion, "select * from users inner join perfil on sucursal_users = perfil.id_perfil 
inner join user_group on users.cargo_users = user_group.user_group_id 
where id_users = '".$user_id."'"); //obtener el usuario activo 1aqui1
    $rw         = mysqli_fetch_array($sqlUsuarioACT);
    $id_sucursal = $rw['sucursal_users'];
    $nombreSucursal = $rw['giro_empresa'];
    $nombreCargo      = $rw['name'];
///////////////////////fin consultar la sucursal

if(strcmp($sucur,'0')!=0)
{
    $sucur = $id_sucursal;
}
$sWhere    = " stock.id_producto_stock=productos.id_producto and stock.id_sucursal_stock=".$sucur." 
 and (productos.codigo_producto like '%$q%' or productos.nombre_producto like '%$q%')  ";


$consulta  = "SELECT * FROM productos left join lineas on productos.id_linea_producto = lineas.id_linea 
left join proveedores on productos.id_proveedor = proveedores.id_proveedor  ,stock WHERE $sWhere  order by productos.id_producto DESC";
$resultado = $conexion->query($consulta);
if ($resultado->num_rows > 0) {
    date_default_timezone_set('America/Mexico_City');
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
        ->setDescription("Reporte de Productos")
        ->setKeywords("reporte productos")
        ->setCategory("Reporte excel");
    $tituloReporte   = "Reporte de Productos";
    $titulosColumnas = array('ID', 'CODIGO', 'NOMBRE', 'EXISTENCIA', 'COSTO', 'P.VENTA', 'P.MAYOREO', 'P.ESPECIAL', 'CATEGORIA',  'VENCIMIENTO','PROVEEDOR','STOCK MINIMO');
    $objPHPExcel->setActiveSheetIndex(0)
        ->mergeCells('A1:J1');
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', $tituloReporte)
        ->setCellValue('A3', $titulosColumnas[0])
        ->setCellValue('B3', $titulosColumnas[1])
        ->setCellValue('C3', $titulosColumnas[2])
        ->setCellValue('D3', $titulosColumnas[3])
        ->setCellValue('E3', $titulosColumnas[4])
        ->setCellValue('F3', $titulosColumnas[5])
        ->setCellValue('G3', $titulosColumnas[6])
        ->setCellValue('H3', $titulosColumnas[7])
        ->setCellValue('I3', $titulosColumnas[8])
        ->setCellValue('J3', $titulosColumnas[9])
        ->setCellValue('K3', $titulosColumnas[10])
        ->setCellValue('L3', $titulosColumnas[11]);
    //Se agregan los datos de los alumnos
    $i = 4;
    while ($fila = $resultado->fetch_array()) {

        $id_producto = $fila['id_producto'];
        $stock_producto= $fila['id_producto'];
        if($fila['date_vence'] == 0)
            {
                $date_vence = "no";
            }else{
                $date_vence           = date('d/m/Y', strtotime($fila['date_vence']));
            }

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $fila['id_producto'])
            ->setCellValue('B' . $i, $fila['codigo_producto'])
            ->setCellValue('C' . $i, $fila['nombre_producto'])
            ->setCellValue('D' . $i, $fila['cantidad_stock'])
            ->setCellValue('E' . $i, $fila['costo_producto'])
            ->setCellValue('F' . $i, $fila['valor1_producto'])
            ->setCellValue('G' . $i, $fila['valor2_producto'])
            ->setCellValue('H' . $i, $fila['valor3_producto'])
            ->setCellValue('I' . $i, $fila['nombre_linea'])
            ->setCellValue('J' . $i, $date_vence)
            ->setCellValue('K' . $i, $fila['nombre_proveedor'])
            ->setCellValue('L' . $i, $fila['stock_min_producto']);
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
            'size'   => 10,
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
    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($estiloTituloColumnas);
    //$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:G" . ($i - 1));
    $objPHPExcel->getActiveSheet()->getStyle('E4:E' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    $objPHPExcel->getActiveSheet()->getStyle('F4:F' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    $objPHPExcel->getActiveSheet()->getStyle('G4:G' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO
    $objPHPExcel->getActiveSheet()->getStyle('H4:H' . ($i - 1))->getNumberFormat()->setFormatCode('#,##0.00'); //FORMATO NUMERICO

    for ($i = 'A'; $i <= 'L'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension($i)->setAutoSize(true);
    }
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Productos');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporteproductos.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    //1echo "<script>alert('No hay resultados para mostrar')</script>";
    //1echo "<script>window.close();</script>";
    //echo "<script>window.location.replace('../html/rep_pagos.php');</script>";
    //1header("Location:../html/productos.php");
    //1exit;
    //print_r('No hay resultados para mostrar, Seleccionar un Paciente');

    echo("consulta = ".$consulta);
}