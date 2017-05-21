<html>
<head>
  <meta charset="utf-8">
  <title><?= $title?></title>
  <link rel="shortcut icon" href="<?= base_url("assets/image/logo.jpg")?>" />
  <link href=<?= base_url("vendor/twbs/bootstrap/dist/css/bootstrap.min.css")?> rel="stylesheet" media="screen" />
</head>
<body onload="window.print()">
<?php
$this->load->helper(array('fecha'));
foreach ($prueba as $dato) {
  $numero_solicitud_compra = $dato->numero_solicitud_compra;
  $fecha_solicitud_compra = fecha($dato->fecha_solicitud_compra);
  $nombre_empleado=$dato->nombre_empleado;
  $funcional=$dato->cargo_funcional;
  $nombre_seccion=$dato->seccion;
  $justificacion=$dato->justificacion;
  $id_especifico=$dato->id_especifico;
  $cantidad=$dato->cantidad;
  $nombre_unidad_medida=$dato->nombre;
  $nombre_producto=$dato->nombre_producto;
  $precio_estimado=$dato->precio_estimado;
  $especificaciones=$dato->especificaciones;
  $forma_entrega=$dato->forma_entrega;
  $lugar_entrega=$dato->lugar_entrega;
  $otras_condiciones=$dato->otras_condiciones;
  $comentario=$dato->comentario;
}

$cmp_ln_1 = 0;
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
}
?>
<table width=95% align="center" height=100% border=1 cellspacing=0 cellpadding=0 style="font-size:10px">
<tr>
<td colspan=7 valign=center width=70% ><img src=<?= base_url("assets/image/icono.jpg")?> alt="" width="150px" /></td>
<td valign=center> No. de Requerimiento: <?= $numero_solicitud_compra?></td>
</tr>

<tr>
<td colspan=2 valign=center width=15% ><b>FECHA:</b></td>
<td colspan=6 valign=center> <?= $fecha_solicitud_compra?> </td>
</tr>

<tr>
<td colspan=2 valign=center width=15% ><b>NOMBRE DEL SOLICITANTE:</b> </td>
<td colspan=6 valign=center> <?= $nombre_empleado?></td>
</tr>

<tr>
<td colspan=2 valign=center width=15% ><b>CARGO:</b> </td>
<td colspan=6 valign=center> <?= $funcional?></td>
</tr>

<tr height="6%">
<td colspan=2 valign=center width=15% > <b>DEPENDENCIA:</b></td>
<td colspan=3 valign=center width=40%><?= $dato->seccion_padre ?></td>
<td valign=center width=13%><b>FIRMA DEL SOLICITANTE:</b></td>
<td colspan=2 valign=center> </td>
</tr>

<tr height="6%">
<td colspan=2 valign=center width=15% ><b>NOMBRE DEL AUTORIZANTE:</b></td>
<td colspan=3 valign=center width=40%> <?php echo $autorizante->nombre_empleado; ?>  </td>
<td valign=center width=13%><b>FIRMA DEL AUTORIZANTE:</b> </td>
<td colspan=2 valign=center> </td>
</tr>

<tr>
<td colspan=2 valign=center width=15% ><b>CARGO:</b> </td>
<td colspan=6 valign=center><?= $autorizante->cargo_funcional?></td>
</tr>

<tr>
<td colspan=2 valign=center width=15% ><b>DEPENDENCIA:</b> </td>
<td colspan=6 valign=center><?= $autorizante->seccion_padre ?></td>
</tr>

<tr>
<th colspan=8 valign=center> <p ALIGN=CENTER>JUSTIFICACIÓN DE LA SOLICITUD</p></th>
</tr>

<tr>
<td colspan=8 valign=center ><?= $justificacion?> </td>
</tr>

<tr>
<td rowspan=2 valign=center align="center" width=7.5%> <b>ITEM</b></td>
<td rowspan=2 valign=center align="center" width=7.5%> <b>CODIGO</b></td>
<td colspan=6 valign=center ALIGN=CENTER > <b>DETALLE</b></td>
</tr>

<tr>
<td valign=center align="center" width=10%><b>CANTIDAD</b> </td>
<td valign=center align="center" width=10%> <b>UNIDAD DE MEDIDA</b></td>
<td valign=center align="center" width=18%> <b>OBRA, BIEN O SERIVCIO SOLICITADO</b></td>
<td colspan=3 valign=center align="center" > <b>ESPECIFICACIONES TÉCNICAS (material,color,medida,otras caracteristicas requeridas. En caso de ser necesario adjuntar documento con esta información):</b></td>
</tr>



<?php
$cont=1;
foreach($prueba as $detalleSol){
echo "<tr>";
  echo "<td valign=center align=center height=6%>";
    echo  $cont;
  echo "</td>";
  echo "<td valign=center align=center>";
    echo  $detalleSol->id_especifico;
  echo "</td>";
  echo "<td valign=center align=center>";
    echo $detalleSol->cantidad;
  echo "</td>";
  echo "<td valign=center align=center>";
    echo $detalleSol->nombre;
  echo "</td>";
  echo "<td valign=center align=center>";
    echo $detalleSol->nombre_producto;
  echo "</td>";
  echo "<td colspan=3 valign=center align=center>";
    echo $detalleSol->especificaciones;
  echo "</td>";
echo "</tr>";
$cont=$cont+1;
}
?>

<tr>
<td colspan=8 valign=center ALIGN=CENTER > <b>CONDICIONES:</b></td>
</tr>

<tr>
<td colspan=4 valign=center > <b>VALOR ESTIMADO DE LA COMPRA:</b></td>
<td colspan=4 valign=center> $<?= number_format($precio_estimado, 2)?></td>
</tr>

<tr>
<td colspan=4 valign=center ><b>FORMA DE ENTREGA: </b></td>
<td colspan=4 valign=center> <?= $forma_entrega?></td>
</tr>

<tr>
<td colspan=4 valign=center ><b>LUGAR DE ENTREGA:</b></td>
<td colspan=4 valign=center> <?= $lugar_entrega?></td>
</tr>

<tr>
<td rowspan="2" colspan=4 valign=center ><b>OTRAS CONDICIONES NECESARIAS: </b></td>
<td colspan=4 valign=center><?= ($ocn_ln_1 != 0) ? substr($otras_condiciones, 0, $ocn_ln_1) : ""?></td>
</tr>

<tr>
  <td colspan=4 valign=center><?= ($ocn_ln_2 != 0) ? substr($otras_condiciones, $ocn_ln_1, $ocn_ln_2) : ""?></td>
</tr>

<tr>
<td rowspan="4" colspan=4 valign=center ><b>OBSERVACIONES:</b></td>
<td colspan=4 valign=center><?= ($cmp_ln_1 != 0) ? substr($comentario, 0, $cmp_ln_1) : ""?></td>
</tr>

<tr>
<td colspan=4 valign=center> <?= ($cmp_ln_2 != 0) ? substr($comentario, $cmp_ln_1, $cmp_ln_2) : ""?></td>
</tr>

<tr>
<td colspan=4 valign=center> <?= ($cmp_ln_3 != 0) ? substr($comentario, $cmp_ln_2, $cmp_ln_3) : ""?></td>
</tr>

<tr>
<td colspan=4 valign=center> <?= ($cmp_ln_4 != 0) ? substr($comentario, $cmp_ln_3, $cmp_ln_4) : ""?></td>
</tr>

<tr>
<td colspan=8 valign=center  ALIGN=CENTER><b>PROPUESTA DE ADMINISTRADOR DE CONTRATO U ORDEN DE COMPRA</b> </td>
</tr>

<tr>
<td colspan=2 valign=center > <b>NOMBRE:</b></td>
<td colspan=6 valign=center> <?= $adminoc->nombre_empleado?> </td>
</tr>

<tr>
<td colspan=2 valign=center ><b>CARGO:</b> </td>
<td colspan=6 valign=center> <?= $adminoc->cargo_funcional?></td>
</tr>

<tr>
<td colspan=2 valign=center ><b>DEPENDENCIA:</b> </td>
<td colspan=6 valign=center> <?= $adminoc->seccion_padre?></td>
</tr>

</tbody>
</table>


</body>
</html>
