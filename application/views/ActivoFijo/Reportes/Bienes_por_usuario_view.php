<?php

echo $this->breadcrumb->build_breadcrump(implode('/', array_slice($this->uri->segment_array(), 0, 4)));

echo "<div style='text-align:center'>";
    echo "<div class='form-group'>";
    echo "<h3><font color=black>1-Reporte de bienes por usuario</font></h3>";
  echo "</div>";
echo "</div>";

$emp = array(
    'name' => 'autocomplete',
    'placeholder' => 'NOMBRE DEL EMPLEADO',
    'class' => "form-control autocomplete",
    'autocomplete' => 'off',
    'uri' => 'index.php/ActivoFijo/Reportes/Bienes_por_usuario/AutocompleteEmpleado',
    'name_op' => 'empleado',
    'siguiente' => 'button',
    'content' => 'suggestions'
);

$button = array('class' => 'btn btn-success',);
$atriLabel = array('class' => 'col-lg-2 control-label');

$atributos = array(
  'class' => 'form-horizontal',
  'role' => 'form',
);

echo "<div class='content-form'>";
  echo "<div class='limit-content-title'>";
    echo "<span class='icono icon-filter icon-title'> Filtro</span>";
  echo "</div>";
  echo "<div class='limit-content'>";
    echo form_open("/ActivoFijo/Reportes/Bienes_por_usuario/RecibirBienesUsuario", $atributos);

      echo "<div class='form-group'>";
        echo form_label('Empleado:', 'fun', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($emp);
          echo form_hidden('empleado');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo form_submit('','Generar', $button);
    echo form_close();
  echo "</div>";
echo "</div>";
?>
