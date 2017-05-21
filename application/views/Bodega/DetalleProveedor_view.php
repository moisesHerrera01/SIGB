<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<h3><font color=black>Datos de proveedor</font></h3>";
echo "</div>";

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo "<div class='content-form'>";
    echo form_open("/Bodega/DetalleProveedor/mostrarTabla", $atributos);
    echo form_close();
echo "</div>";
?>
