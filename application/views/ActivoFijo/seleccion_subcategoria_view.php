<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'>Equipo informático</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Seleccion_Subcategoria/RecibirDatos", $atributos);

  $bien = array(
      'name' => 'autocomplete',
      'placeholder' => 'Ingrese bien',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Detalle_Movimiento/AutocompleteBienes',
      'name_op' => 'bien',
      'siguiente' => 'button',
      'content' => 'suggestions'
  );

  $tipo = array(
      'default' => 'TIPO COMPUTADORA',
      'LAPTOP' => 'LAPTOP',
      'DESKTOP' => 'DESKTOP',
      'SERVIDOR' => 'SERVIDOR'
  );
  $atriLabel = array('class' => 'col-lg-2 control-label');
  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Bien:', 'bien', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($bien);
      echo form_hidden('bien');
      echo '<div id="suggestions" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Tipo:', 'tipo', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_dropdown('tipo_computadora', $tipo, 'default', array('class' => 'form-control'));
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');
  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','autocomplete','bien'], ['tipo_computadora'])\">Limpiar</a>";
echo "</fieldset>";
  echo form_close();
  echo "</div>";
echo "</div>";
  $buscar = array(
    'name' => 'buscar',
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/ActivoFijo/Seleccion_Subcategoria/mostrarTabla'
  );
  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
