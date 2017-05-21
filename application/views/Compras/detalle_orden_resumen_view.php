<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );
  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
  $USER = $this->session->userdata('logged_in');
if($estado=='APROBADA ORDEN' || $estado=='APROBADA DISPONIBILIDAD' || $nivel==5 || $nivel==6){
  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Orden de Compra Resumen</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Compras/$controller/RecibirDatos", $atributos);

      $det = array(
          'name' => 'autocomplete1',
          'placeholder' => 'Ingrese Obra,bien o servicio solicitado',
          'class' => "form-control autocomplete_asoc2_text_area",
          'autocomplete' => 'off',
          'uri' => 'index.php/Compras/Detalle_orden_resumen/AutocompleteDetalleOrdenResumen/'.$id_orden_compra,
          'name_op' => 'producto',
          'siguiente' => 'cantidad',
          'content' => 'suggestions1',
          'asociacion' => 'cantidad',
          'asociacion2' => 'especificaciones'
      );

      $cant = array(
          'name' => 'cantidad',
          'type' => 'number',
          'placeholder' => 'Ingrese la Cantidad',
          'class' => "form-control"
      );

      $espe = array(
        'name' => 'especificaciones',
        'placeholder' => 'INGRESE ESPECIFICACIONES',
        'class' => "form-control",
        "rows" => "3"
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');
      $button = array('class' => 'btn btn-success',);

          echo "<fieldset>";
            echo "<legend>Paso 4: Detalle de productos.</legend>";
          echo "<div class='form-group'>";
            echo form_label('Producto:', 'det', $atriLabel);
            echo "<div class='col-lg-10'>";
              echo form_input($det);
              echo form_hidden('producto');
              echo '<div id="suggestions1" class="suggestions"></div>';
            echo "</div>";
          echo "</div>";

          echo "<div class='form-group'>";
            echo form_label('Cantidad:', 'cant', $atriLabel);
            echo "<div class='col-lg-10'>";
              echo form_input($cant);
            echo "</div>";
          echo "</div>";

          echo "<div class='form-group'>";
            echo form_label('Especificación:', 'espe', $atriLabel);
            echo "<div class='col-lg-10'>";
              echo form_textarea($espe);
            echo "</div>";
          echo "</div>";

          echo form_hidden('orden', $id_orden_compra);
          echo form_hidden('modulo',$modulo);
          echo form_hidden('id_modulo',$id_modulo);
          echo form_hidden('id_detalle_orden_resumen');
          echo form_submit('guardar','Agregar', $button);

echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'producto','autocomplete1','cantidad'],
false,['especificaciones'])\">Limpiar</a>";

      echo "</fieldset>";
      echo form_close();
      echo "</div>";
      echo "<div class='barra_carga'>";
      echo "</div>";
    echo "</div>";
        }
?>
