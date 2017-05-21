<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'>Bien Mueble</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Bienes_muebles/RecibirDatos", $atributos);

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

  $ser = array(
    'name' => 'serie',
    'placeholder' => 'Ingrese número de serie',
    'class' => "form-control"
  );

  $numM = array(
      'name' => 'numero_motor',
      'placeholder' => 'Ingrese número de motor',
      'class' => "form-control"
  );

  $numP = array(
      'name' => 'numero_placa',
      'placeholder' => 'Ingrese número de placa',
      'class' => "form-control"
  );

  $mat = array(
      'name' => 'matricula',
      'placeholder' => 'Ingrese número de matricula',
      'class' => "form-control",
  );

  $obs = array(
      'name' => 'observacion',
      'placeholder' => 'Ingrese una observación',
      'class' => "form-control"
  );

  $ofi = array(
      'name' => 'autocomplete3',
      'placeholder' => 'Ingrese oficina',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Bienes_muebles/AutocompleteOficina',
      'name_op' => 'oficina',
      'siguiente' => 'autocomplete4',
      'content' => 'suggestions3'
  );

  $emp = array(
      'name' => 'autocomplete4',
      'placeholder' => 'Ingrese empleado',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Bienes_muebles/AutocompleteEmpleado',
      'name_op' => 'empleado',
      'siguiente' => 'autocomplete1',
      'content' => 'suggestions4'
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
    echo form_label('Serie/Chasis:', 'ser', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ser);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Motor:', 'numM', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($numM);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Placa:', 'numP', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($numP);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Matricula:', 'mat', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($mat);
    echo "</div>";
  echo "</div>";
echo "</fieldset>";

echo "<fieldset>";
echo "<legend>Paso 2:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Observación:', 'obs', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($obs);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Oficina:', 'ofi', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ofi);
      echo form_hidden('oficina');
      echo '<div id="suggestions3" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Empleado:', 'emp', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($emp);
      echo form_hidden('empleado');
      echo '<div id="suggestions4" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";


  echo form_hidden('id');
  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','autocomplete1','codigo_anterior','serie','numero_motor',
  'numero_placa','matricula','autocomplete2','observacion','autocomplete3','autocomplete4'])\">Limpiar</a>";
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
    'url' => 'index.php/ActivoFijo/Bienes_muebles/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
