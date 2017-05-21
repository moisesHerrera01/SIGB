<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?= $title?></title>
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/main.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/menu.css")?> rel="stylesheet" media="screen">
    <link href=<?= base_url("assets/css/sweetalert.css")?> rel="stylesheet" media="screen">
    <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
    <script type="text/javascript">
      var baseurl = "<?= base_url(); ?>";
    </script>
  </head>
  <body onload="window.print()">
  <div align="right"><img src=<?= base_url("assets/image/icono.jpg")?> alt="" width="150px"/></div>
<?php
setlocale (LC_TIME, 'es_ES');
date_default_timezone_set('America/El_Salvador');
$hora=date_create($var['hora']);

echo "<br><br><br><br><center><b>Ministerio de Trabajo y Previción Social - SICBAF</b></center>";

echo "<br><br><center><H2>ACTA DE RECEPCION DE MERCADERIA</H2></center>";

#echo "<br>Correlativo: ".$var['id_factura'];

echo "<br>Correlativo de Fuente de Fondos: ".$var['nombre_fuente']."-".$var['correlativo'];

echo "<br><br>En la Bodega General, del Ministerio de Trabajo y Prevención Social a las&nbsp".date_format($hora,"h:i A").
" de la fecha&nbsp".$var['fecha_ingreso'].
", reunidos los señores ".$var['nombre_entrega']." de la empresa ".$var['nombre_proveedor'].
" y el señor(a) EDUARDO CALDERON.";

echo "<br><br>Representando al Ministerio de Trabajo y Prevención Social, para la recepción de mercaderia que a continuación se detalla,
segun factura No. ".$var['numero_factura']." de fecha ".$var['fecha_factura'].".";
echo "<br><br>";
echo "<center>$table</center>";
echo('<br>'.$var['comentario_productos']);
echo "<br><br>No habiendo más que hacer constar y leída que fue, para constancia firmamos.";

echo "<br><br><br><br><br><br>";

echo "<left>____________________________</left>"."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp____________________________";

echo "<br>Por Empresa Suministrante"."&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
&nbspPor Bodega General";
 ?>
</body>
</html>
