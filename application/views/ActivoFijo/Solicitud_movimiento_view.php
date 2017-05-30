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
  echo form_open("/ActivoFijo/Solicitud_movimiento/RecibirDatos", $atributos);

  $dat= array(
      'name' => 'fecha_solicitud',
      'placeholder' => 'Escribe la fecha de ingreso',
      'class' => "form-control",
      'value'=> $fecha,
      'readonly'=>"readonly",
      'type'=>'date'
  );

  $ent = array(
      'name' => 'autocomplete3',
      'placeholder' => 'Ingrese oficina que entrega',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Bienes_muebles/AutocompleteOficina',
      'name_op' => 'oficina_entrega',
      'siguiente' => 'autocomplete3',
      'content' => 'suggestions1'
  );

  $rec = array(
      'name' => 'autocomplete',
      'placeholder' => 'Ingrese oficina que recibe',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Bienes_muebles/AutocompleteOficina',
      'name_op' => 'oficina_recibe',
      'siguiente' => 'autocomplete4',
      'content' => 'suggestions2'
  );

  $emp = array(
      'name' => 'autocomplete4',
      'placeholder' => 'Ingrese empleado',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Bienes_muebles/AutocompleteEmpleado',
      'name_op' => 'empleado',
      'siguiente' => 'autocomplete5',
      'content' => 'suggestions3'
  );

  $tip = array(
      'name' => 'autocomplete5',
      'placeholder' => 'Ingrese tipo movimiento',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Movimiento/AutocompleteTipo_movimiento',
      'name_op' => 'tipo_movimiento',
      'siguiente' => 'autocomplete5',
      'content' => 'suggestions4'
  );

  $usu = array(
    'name' => 'usuario_externo',
    'placeholder' => 'Ingrese nombre de usuario externo',
    'class' => "form-control",
    'type' => 'text',
    'pattern'=>'[A-Z]'
  );

  $entx = array(
      'name' => 'entregado_por',
      'placeholder' => 'Ingrese el nombre de la persona que entrega',
      'class' => "form-control"
  );

  $recx = array(
      'name' => 'recibido_por',
      'placeholder' => 'Ingrese el nombre de la persona que recibe',
      'class' => "form-control"
  );

  $autx = array(
      'name' => 'autorizado_por',
      'placeholder' => 'Ingrese el nombre de la persona que autoriza',
      'class' => "form-control",
  );

  $visx = array(
      'name' => 'visto_bueno_por',
      'placeholder' => 'Ingrese el nombre de la persona que da visto bueno',
      'class' => "form-control"
  );

  $obs = array(
    'name' => 'observacion',
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
    echo form_label('Entrega:', 'ent', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($ent);
      echo form_hidden('oficina_entrega');
      echo '<div id="suggestions1" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Recibe:', 'rec', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($rec);
      echo form_hidden('oficina_recibe');
      echo '<div id="suggestions2" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Empleado:', 'emp', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($emp);
      echo form_hidden('empleado');
      echo '<div id="suggestions3" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Tipo mov:', 'tip', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($tip);
      echo form_hidden('tipo_movimiento');
      echo '<div id="suggestions4" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Usuario externo:', 'usu', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($usu);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Entregado:', 'entx', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($entx);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Recibido:', 'recx', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($recx);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Autorizado:', 'autx', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($autx);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Visto:', 'visx', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($visx);
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

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['autocomplete3', 'oficina_entrega','autocomplete','oficina_recibe',
  'autocomplete4','empleado','autocomplete5','tipo_movimiento','usuario_externo','entregado_por','recibido_por',
  'autorizado_por','visto_bueno_por'], false, ['observacion'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Solicitud_movimiento/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
