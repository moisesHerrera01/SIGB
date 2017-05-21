<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );
  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
  $USER = $this->session->userdata('logged_in');
if($compra->estado_solicitud_compra=='APROBADA DISPONIBILIDAD' || $compra->estado_solicitud_compra=='APROBADA COMPRAS' ||
 $compra->nivel_solicitud==4 || $compra->nivel_solicitud==5){
  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle de montos</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Compras/Detalle_disponibilidad_montos/RecibirDatos", $atributos);

      $linea = array(
          'name' => 'autocomplete1',
          'placeholder' => 'Ingrese linea presupuestaria',
          'class' => "form-control autocomplete",
          'autocomplete' => 'off',
          'uri' => 'index.php/Compras/Solicitud_Disponibilidad/AutocompleteLineaTrabajo',
          'name_op' => 'linea',
          'siguiente' => 'monto',
          'content' => 'suggestions1'
      );

      $monto = array(
          'name' => 'monto',
          'type' => 'number',
          'placeholder' => 'Ingrese el monto',
          'class' => "form-control"
      );

      $atriLabel = array('class' => 'col-lg-2 control-label');
      $button = array('class' => 'btn btn-success',);

          echo "<div class='form-group'>";
            echo form_label('Linea:', 'linea', $atriLabel);
            echo "<div class='col-lg-10'>";
              echo form_input($linea);
              echo form_hidden('linea');
              echo '<div id="suggestions1" class="suggestions"></div>';
            echo "</div>";
          echo "</div>";

          echo "<div class='form-group'>";
            echo form_label('Monto:', 'monto', $atriLabel);
            echo "<div class='col-lg-10'>";
              echo form_input($monto);
            echo "</div>";
          echo "</div>";

          echo form_hidden('id_detalle_solicitud_disponibilidad');
          echo form_hidden('disponibilidad',$disponibilidad);
          echo form_submit('guardar','Agregar', $button);

          echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id_detalle_solicitud_disponibilidad',
           'linea','autocomplete1','monto'])\">Limpiar</a>";


      echo form_close();
      echo "</div>";
      echo "</div>";
      }
?>
