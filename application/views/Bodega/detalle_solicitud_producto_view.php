<?php

  $USER = $this->session->userdata('logged_in');
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );
  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
  $bandera =0;
  if ($controller == 'detalle_solicitud_producto'){
    if ($nivel == 0 || $nivel == 1){
      $bandera = 1;
    }
  }
  if ($controller == 'Detalle_Solicitud_Control'){
    if ($USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE AF' || $USER['rol'] == 'JEFE UACI'
         || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR UACI'){
      if ($nivel == 1 || $nivel == 2){
        $bandera = 1;
      } else {
        $bandera = 0;
      }
    } elseif ( $USER['rol'] == 'DIRECTOR ADMINISTRATIVO' ){
      if ($nivel == 2 || $nivel == 3){
        $bandera = 1;
      } else {
        $bandera = 0;
      }
    }

    elseif ($USER['rol'] == 'ADMINISTRADOR SICBAF'){
      if ($nivel == 1 || $nivel == 2 || $nivel == 3){
        $bandera = 1;
      } else {
        $bandera = 0;
      }
    }
  }
    if($bandera == 1){
    echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Solicitud</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Bodega/$controller/RecibirDatos", $atributos);

      $det = array(
          'name' => 'autocomplete1',
          'placeholder' => 'Escribe Producto',
          'class' => "form-control",
          'autocomplete' => 'off',
      );

      $cant = array(
          'name' => 'cantidad',
          'type' => 'number',
          'placeholder' => 'Escribe la Cantidad',
          'class' => "form-control"
      );

      // $fun = array(
      //     'name' => 'autocomplete2',
      //     'placeholder' => 'Escribe Fuente de Fondos',
      //     'class' => "form-control autocomplete",
      //     'autocomplete' => 'off',
      //     'uri' => 'index.php/Bodega/Fuentefondos/Autocomplete',
      //     'name_op' => 'fuente',
      //     'siguiente' => 'button',
      //     'content' => 'suggestions2'
      // );

      $atriLabel = array('class' => 'col-lg-2 control-label');
      $button = array('class' => 'btn btn-success',);

      echo "<fieldset>";
        echo "<legend>Paso 2: Detalle de productos.</legend>";

        // echo "<div class='form-group'>";
        //   echo form_label('Fuente de Fondos:', 'fun', $atriLabel);
        //   echo "<div class='col-lg-10'>";
        //     echo form_input($fun);
             echo form_hidden('fuente', $id_fuente);
        //     echo '<div id="suggestions2" class="suggestions"></div>';
        //   echo "</div>";
        // echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Producto:', 'det', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($det);
            echo form_hidden('precio');
            echo '<div id="suggestions1" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Cantidad:', 'cant', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($cant);
          echo "</div>";
        echo "</div>";

      echo form_hidden('solicitud', $id_solicitud);
      echo form_hidden('detalleproducto');
      echo form_hidden('fuente');
      echo form_hidden('id_detalle_solicitud_producto');
      echo form_submit('guardar','Agregar', $button);

      echo "<button class='btn btn-warning' type='reset' value='Reset'>Limpiar</button>";
      echo "</fieldset>";
      echo form_close();
      echo "</div>";
      echo "<div class='barra_carga'>";
      echo "</div>";
    echo "</div>";
    }
?>
