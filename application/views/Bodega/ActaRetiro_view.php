<html>
<head>
  <meta charset="utf-8">
  <title><?= $title?></title>
  <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
  <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet" media="screen" />
</head>
<body onload="window.print()">
  <div class="content">
    <div>
      <p align="right">
        <img src=<?= base_url("assets/image/icono.jpg")?> width="200px" />
      </p>
    </div>
<?php
//onload="window.print()"
$this->load->helper(array('fecha'));
foreach ($acta as $dato) {
  $id_solicitud = $dato->id_solicitud;
  $numero_solicitud=$dato->numero_solicitud;
  $fecha_solicitud = fecha($dato->fecha_solicitud);
  $comentario=$dato->comentario;
  $fecha_salida = fecha($dato->fecha_salida);
  $cantidad=$dato->cantidad;
  $nombre=$dato->nombre;
  $telefonos=$dato->telefonos;
  $linea_trabajo=$dato->linea_trabajo;
  $nombre_seccion=$dato->nombre_seccion;
  $nombre_completo=$dato->nombre_completo;
}

/*$cmp_ln_1 = 0;
$cmp_ln_2 = 0;
$cmp_ln_3 = 0;
$cmp_ln_4 = 0;
if (strlen($comentario) > 125) {
  $cmp_ln_1 = 125;
  if (strlen($comentario) > 250) {
    $cmp_ln_2 = 250;
    if (strlen($comentario) > 375) {
      $cmp_ln_3 = 375;
      if (strlen($comentario) > 500) {
        $cmp_ln_4 = 500;
      } else {
        $cmp_ln_4 = strlen($comentario);
      }
    } else {
      $cmp_ln_3 = strlen($comentario);
    }
  } else {
    $cmp_ln_2 = strlen($comentario);
  }
} else {
  $cmp_ln_1 = strlen($comentario);
}

$ocn_ln_1 = 0;
$ocn_ln_2 = 0;
if (strlen($otras_condiciones) > 125) {
  $ocn_ln_1 = 125;
  if (strlen($otras_condiciones) > 250) {
    $ocn_ln_2 = 250;
  } else {
    $ocn_ln_2 = strlen($otras_condiciones);
  }
} else {
  $ocn_ln_1 = strlen($otras_condiciones);
}*/
?>
<center><H4>MINISTERIO DE TRABAJO Y PREVISION SOCIAL</H4></center>
<center><H5><b>BODEGA INSTITUCIONAL</b></H5></center>
<center><H2><b>RETIRO DE BODEGA</b></H2></center>

<p>Unidad Presupuestaría: <?=substr($linea_trabajo,0,-2)?></p>
<p>Linea de Trabajo:<?= substr($linea_trabajo,2,2)?></p>
<p>Sección o Unidad: <?= $nombre_seccion?></p>


<table width=100% align="center" border=1 cellspacing=0 cellpadding=0 style="font-size:10px">
<tr>
<td colspan="3" width="400" height="25">
<b>Nº DE SOLICITUD:</b> <?= $id_solicitud?>
</td>
<td colspan="2" width="400">
<b>Nº POR FUENTE DE FONDOS:</b> <?= $numero_solicitud?>
</td>
</tr>
<tr>
<td colspan="3" width="415" height="25">
<b>FECHA DE ELABORACION:</b> <?= $fecha_solicitud?>
</td>
<td colspan="2" width="151">
<b>TELEFONO O EXTENSION:</b> <?= $telefonos?>
</td>
</tr>

<?php
echo "<tr>";
  echo "<td width=75 height=25 align=center>";
    echo  '<b>CANTIDAD SOLICITADA</b>';
  echo "</td>";
  echo "<td colspan=3 width=406 align=center>";
    echo  '<b>ARTICULOS</b>';
  echo "</td>";
  echo "<td width=75 align=center>";
    echo '<b>CANTIDAD ENTREGADA</b>';
  echo "</td>";
echo "</tr>";
foreach($acta as $detalleSol){
echo "<tr>";
  echo "<td width=75 height=25 align=center>";
    echo  $detalleSol->cantidad;
  echo "</td>";
  echo "<td colspan=3 width=406>";
    echo  $detalleSol->nombre;
  echo "</td>";
  echo "<td width=75>";
  echo "</td>";
echo "</tr>";
}
?>
<tr>
<td colspan="5" width="566" height="25">
<b>JUSTIFICACION:</b> <?= $comentario?>
</td>
</tr>
<tr>
<td colspan="3" width="283">
<center>ENTREGADO (BODEGA)</center>
</td>
<td colspan="3" width="283">
<center>PRECESADO (ADMON.)</center>
</td>
</tr>
<tr>
<td colspan="3" width="283" height="125">
</td>
<td colspan="3" width="283" height="125">
</td>
</tr>

</table>
<br><br><br><br><br>
<left><b>_______________________</left>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
_______________________&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp_______________________
<br>
<left>Solicitado - Firma y Sello
  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspAutorizado - Firma y Sello
  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspRecibido - Nombre y Firma</b>
</body>
</html>
