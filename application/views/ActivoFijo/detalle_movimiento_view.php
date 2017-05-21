<?php
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

    echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Movimiento</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/ActivoFijo/Detalle_Movimiento/RecibirDatos", $atributos);

      $mov = array(
          'name' => 'movimiento',
          'class' => "form-control",
          'value' => $nombre,
          'readonly'=>"readonly"
      );

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

      $emp = array(
          'name' => 'autocomplete2',
          'placeholder' => 'NOMBRE DEL EMPLEADO',
          'class' => "form-control autocomplete_func",
          'autocomplete' => 'off',
          'uri' => 'index.php/ActivoFijo/Reportes/Bienes_por_usuario/AutocompleteEmpleado',
          'name_op' => 'empleado',
          'siguiente' => 'button',
          'content' => 'suggestions2'
      );

      $ofc = array(
        'name' => 'autocomplete3',
        'placeholder' => 'Ingrese oficina',
        'class' => "form-control autocomplete_func",
        'autocomplete' => 'off',
        'uri' => 'index.php/ActivoFijo/Bienes_muebles/AutocompleteOficina',
        'name_op' => 'oficina',
        'siguiente' => 'autocomplete3',
        'content' => 'suggestions3'
      );

      $options = array(
      'bien' => 'BIEN',
      'empleado' => 'EMPLEADO',
      'oficina' => 'OFICINA',
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');

      $button = array('class' => 'btn btn-success',);

      echo "<div class='form-group'>";
        echo form_label('Movimiento:', 'mov', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($mov);
          echo form_hidden('movimiento', $mov);
        echo "</div>";
      echo "</div>";

      echo "<div class='form-group'>";
        echo form_label('Ingresar Por:', 'ing', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_dropdown('ingresar', $options, 'bien', array('class' => "form-control"));
        echo "</div>";
      echo "</div>";

      echo "<div id='bien'>";
        echo "<div class='form-group'>";
          echo form_label('Bien:', 'bien', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($bien);
            echo form_hidden('bien');
            echo '<div id="suggestions" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";
      echo "</div>";

      echo "<div id='empleado' style='display: none;'>";
        echo "<div class='form-group'>";
          echo form_label('Empleado:', 'em', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($emp);
            echo form_hidden('empleado');
            echo '<div id="suggestions2" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";
      echo "</div>";

      echo "<div id='oficina' style='display: none;'>";
        echo "<div class='form-group'>";
          echo form_label('Oficina:', 'fun', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($ofc);
            echo form_hidden('oficina');
            echo '<div id="suggestions3" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";
      echo "</div>";

      echo form_hidden('id_detalle_movimiento');
      echo form_hidden('movimiento', $id_mov);
      echo form_submit('guardar','Guardar', $button);

      echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['autocomplete'])\">Limpiar</a>";

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
    'url' => 'index.php/ActivoFijo/Detalle_Movimiento/mostrarTabla/'.$nombre
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
