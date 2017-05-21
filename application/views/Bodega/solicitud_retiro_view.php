<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Solicitud</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/Solicitud_retiro/RecibirDatos", $atributos);
      $dat= array(
          'name' => 'fecha_solicitud',
          'placeholder' => 'Fecha de ingreso',
          'class' => "form-control",
          'type'=>'date'
      );

      $sec = array(
          'name' => 'autocomplete3',
          'placeholder' => 'Ingrese el nombre de la sección',
          'class' => "form-control autocomplete",
          'autocomplete' => 'off',
          'uri' => 'index.php/ActivoFijo/Almacenes/AutocompleteSeccion',
          'name_op' => 'seccion',
          'siguiente' => 'id_usuario',
          'content' => 'suggestions3'
      );

      $us = array(
          'name' => 'autocomplete',
          'id' => 'autocomplete',
          'placeholder' => 'Ingrese el nombre del usuario',
          'class' => "form-control",
          'autocomplete' => 'off',
          'uri' => 'index.php/Bodega/Solicitud_retiro/AutocompleteUsuarioSeccion',
          'name_op' => 'id_usuario',
          'siguiente' => 'default',
          'content' => 'suggestions1'
      );

      $pri= array(
          'default'=>'SELECCIONE PRIORIDAD',
          'BAJA' => 'BAJA',
          'NORMAL' => 'NORMAL',
          'ALTA'=>'ALTA',
      );

      $fun = array(
          'name' => 'autocomplete2',
          'placeholder' => 'Escribe Fuente de Fondos',
          'class' => "form-control autocomplete",
          'autocomplete' => 'off',
          'uri' => 'index.php/Bodega/Fuentefondos/Autocomplete',
          'name_op' => 'id_fuentes',
          'siguiente' => 'button',
          'content' => 'suggestions2'
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');
      $button = array('class' => 'btn btn-success',);

      echo "<fieldset>";
        echo "<legend>Paso 1: Datos Generales.</legend>";
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
          echo '<div id="suggestions3" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Solicitante:', 'us', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($us);
          echo form_hidden('id_usuario');
          echo '<div id="suggestions1" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Prioridad:', 'pri', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('prioridad', $pri, 'default', array('class' => 'form-control'));
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Fuente de Fondos:', 'fun', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($fun);
          echo form_hidden('id_fuentes');
          echo '<div id="suggestions2" class="suggestions"></div>';
        echo "</div>";
      echo "</div>";

      echo form_hidden('id');
      echo form_hidden('seccion');
      echo form_submit('','Siguiente', $button);

      echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['fecha_solicitud','id_usuario','autocomplete','numero_solicitud',
      'id_seccion','autocomplete3','fuente','autocomplete2'],['prioridad'])\">Limpiar</a>";
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
    'url' => 'index.php/Bodega/Solicitud_retiro/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
