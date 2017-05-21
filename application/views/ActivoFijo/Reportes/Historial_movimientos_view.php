<?php

$atriLabel = array('class' => 'col-lg-2 control-label');
$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo form_open("/ActivoFijo/Reportes/Historial_movimientos/", $atributos);
echo form_close();
?>
