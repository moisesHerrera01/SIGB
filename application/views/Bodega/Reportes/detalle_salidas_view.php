<?php

$atriLabel = array('class' => 'col-lg-2 control-label');
$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 3)));

echo form_open("/Bodega/Retiro/RecibirRetiro", $atributos);
echo form_close();
?>
