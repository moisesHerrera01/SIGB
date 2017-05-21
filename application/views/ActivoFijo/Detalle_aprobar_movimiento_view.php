<?php
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

    echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Solicitud Movimiento</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/ActivoFijo/Detalle_aprobar_movimiento/RecibirDatos", $atributos);

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
        echo form_label('Bien:', 'bien', $atriLabel);
        echo "<div class='col-lg-10'>";
          echo form_input($bien);
          echo form_hidden('bien');
          echo '<div id="suggestions" class="suggestions"></div>';
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
    'url' => 'index.php/ActivoFijo/Detalle_aprobar_movimiento/mostrarTabla/'.$nombre
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
