<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Retiro</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/retiro/RecibirDatos", $atributos);
      $dat= array(
          'name' => 'fecha_solicitud',
          'placeholder' => 'Escribe la fecha de ingreso',
          'class' => "form-control",
          'value'=> $fecha,
          'readonly'=>"readonly",
          'type'=>'date'
      );

      $sec= array(
          'name' => 'autocomplete',
          'placeholder' => 'Escribe la seccion o Unidad',
          'class' => "form-control autocomplete",
          'autocomplete' => 'off',
          'uri' => 'index.php/Bodega/retiro/Autocomplete',
          'name_op' => 'seccion',
          'siguiente' => 'numero_solicitud',
          'content' => 'suggestions',
          'readonly'=>"readonly"
      );

      $num = array(
          'name' => 'numero_solicitud',
          'readonly'=>"readonly",
          'placeholder' => 'Escribe el número de la Solicitud',
          'class' => "form-control",
      );

      $pri= array(
          'default'=>'SELECCIONE PRIORIDAD',
          'BAJA' => 'BAJA',
          'NORMAL' => 'NORMAL',
          'ALTA'=>'ALTA',
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
        echo form_label('Sección:', 'sec', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($sec);
          echo form_hidden('seccion');
          echo '<div id="suggestions" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Número:', 'num', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($num);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Prioridad:', 'pri', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('prioridad', $pri, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');
      echo form_hidden('estado');
      echo form_submit('','Guardar', $button);

            echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['autocomplete', 'numero_solicitud'],['prioridad'])\">Limpiar</a>";

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
    'url' => 'index.php/Bodega/solicitud/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
