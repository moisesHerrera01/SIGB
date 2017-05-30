<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'>Solicitud de Movimiento</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Gestionar_movimiento/ActualizarDatos", $atributos);

  $dat= array(
      'name' => 'fecha_solicitud',
      'placeholder' => 'Escribe la fecha de ingreso',
      'class' => "form-control",
      'readonly'=>"readonly",
      'type'=>'date'
  );


  $ti= array(
      'name' => 'tipo',
      'placeholder' => 'Tipo de movimiento',
      'class' => "form-control",
      'readonly'=>"readonly",
  );

  $obs = array(
    'name' => 'observacion',
    'placeholder' => 'INGRESE OBSERVACIONES A LA SOLICITUD',
    'class' => "form-control",
    "rows" => "3",
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

  $button = array('class' => 'btn btn-success',);

  echo "<div class='form-group'>";
    echo form_label('Fecha:', 'dat', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($dat);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Tipo Movimiento:', 'ti', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ti);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Observaciones:', 'obs', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_textarea($obs);
    echo "</div>";
  echo "</div>";


  echo form_hidden('id');
  echo form_submit('','Guardar', $button);

  /*echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','autocomplete3','autocomplete4','autocomplete5',
  'estado_movimiento','usuario_externo','entregado_por,'recibido_por','autorizado_por','visto_bueno_por'],
  false,['observacion'])\">Limpiar</a>";*/

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['fecha_actual', 'entregado_por','recibido_por','autorizado_por',
  'visto_bueno_por','tipo'], false, ['observacion'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Gestionar_movimiento/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
