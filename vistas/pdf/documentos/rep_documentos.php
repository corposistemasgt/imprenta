<?php
/* Connect To Database*/
require_once "../../db.php";
require_once "../../php_conexion.php";
#require_once "../libraries/inventory.php"; //Contiene funcion que controla stock en el inventario
//Inicia Control de Permisos
include "../../permisos.php";
//Archivo de funciones PHP
require_once "../../funciones.php";
//Ontengo variables pasadas por GET
$sucusal = intval($_REQUEST['sucursal']);
$daterange= $_REQUEST['range'];

$fecha="";
if (!empty($daterange)) {
    list($f_inicio, $f_final)                    = explode(" - ", $daterange); //Extrae la fecha inicial y la fecha final en formato espa?ol
    list($dia_inicio, $mes_inicio, $anio_inicio) = explode("/", $f_inicio); //Extrae fecha inicial
    $fecha_inicial                               = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00"; //Fecha inicial formato ingles
    list($dia_fin, $mes_fin, $anio_fin)          = explode("/", $f_final); //Extrae la fecha final
    $fecha_final                                 = "$anio_fin-$mes_fin-$dia_fin 23:59:59";

    $fecha= " and (facturas_ventas.fecha_factura between '$fecha_inicial' and '$fecha_final' )";
}

$consulta2="SELECT * FROM facturas_ventas,users where 
facturas_ventas.id_users_factura = users.id_users and facturas_ventas.id_sucursal =  ".$sucusal.
$fecha." order by facturas_ventas.id_factura";

$query = mysqli_query($conexion, $consulta2);
// get the HTML
ob_start();
include dirname(__FILE__) . '/res/rep_ventas.php';
$content = ob_get_clean();

// convert to PDF
require_once dirname(__FILE__) . '/../html2pdf.class.php';
try
{
    $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', 3);
   // $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    ob_end_clean();
    $html2pdf->Output('productos.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
