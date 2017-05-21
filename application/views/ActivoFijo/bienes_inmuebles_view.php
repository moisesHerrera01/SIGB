<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'>Bien Inmueble</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Bienes_Inmuebles/RecibirDatos", $atributos);

  $dat = array(
      'name' => 'autocomplete1',
      'placeholder' => 'Ingrese Datos Comúnes',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Bienes_Inmuebles/AutocompleteDatosComunes',
      'name_op' => 'dato_comun',
      'siguiente' => 'codigo_anterior',
      'content' => 'suggestions1'
  );

  $ant = array(
      'name' => 'codigo_anterior',
      'placeholder' => 'Ingrese código anterior',
      'class' => "form-control"
  );

  $tipo = array(
      'default' => 'TIPO INMUEBLE',
      'CONSTRUCCION' => 'CONSTRUCCION',
      'TERRENO' => 'TERRENO'
  );

  $ext = array(
      'name' => 'extension',
      'placeholder' => 'Ingrese extensión total en metros cuadrados',
      'class' => "form-control",
      'type'=>"number"
  );

  $mat = array(
      'name' => 'matricula',
      'placeholder' => 'Ingrese número de matricula (CNR)',
      'class' => "form-control"
  );

  $dir = array(
      'name' => 'direccion',
      'placeholder' => 'Ingrese la dirección',
      'class' => "form-control",
  );

  $zona = array(
      'default' => 'ZONA',
      'RURAL' => 'RURAL',
      'URBANA' => 'URBANA'
  );

  $fin = array(
      'name' => 'fines',
      'placeholder' => 'Ingrese fines institucionales',
      'class' => "form-control"
  );

  $pre = array(
      'name' => 'precio',
      'placeholder' => 'Ingrese precio de adquisición',
      'class' => "form-control",
      'type'=>"number"
  );

  $obs = array(
      'name' => 'observacion',
      'placeholder' => 'Ingrese observación',
      'class' => "form-control"
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

echo "<fieldset>";
        echo "<legend>Paso 1:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Datos:', 'dat', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($dat);
      echo form_hidden('dato_comun');
      echo '<div id="suggestions1" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Cod. ant:', 'ant', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ant);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Inmueble:', 'tipo', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_dropdown('tipo_inmueble', $tipo, 'default', array('class' => 'form-control'));
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Extensión:', 'ext', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ext);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Matricula:', 'mat', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($mat);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Dirección:', 'dir', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($dir);
    echo "</div>";
  echo "</div>";
echo "</fieldset>";

echo "<fieldset>";
        echo "<legend>Paso 2:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Zona:', 'zona', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_dropdown('zona_bien', $zona, 'default', array('class' => 'form-control'));
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Fines:', 'fin', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($fin);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Precio:', 'pre', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($pre);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Observación:', 'obs', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($obs);
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');
  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','autocomplete1','codigo_anterior','extension','matricula','direccion',
  'autocomplete2','fines','precio','observacion'], ['tipo_inmueble','zona_bien'])\">Limpiar</a>";
echo "</fieldset>";
  echo form_close();
  echo "</div>";
  echo "<div class='barra_carga'>";
    echo "</div>";
echo "</div>";
  $buscar = array(
    'name' => 'buscar',
    'type' => 'search',
    'placeholder' => 'Buscar',
    'class' => 'form-control',
    'autocomplete' => 'off',
    'id' => 'buscar',
    'url' => 'index.php/ActivoFijo/Bienes_Inmuebles/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
