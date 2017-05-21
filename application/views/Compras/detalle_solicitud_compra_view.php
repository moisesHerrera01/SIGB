<?php
$USER = $this->session->userdata('logged_in');
  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );
  echo $this->breadcrumb->build_breadcrump($modulo);
  $bandera = 0;

if($modulo=='Compras/Solicitud_Compra' && $nivel<2 || $modulo=='Compras/Aprobar_Solicitud' && ($nivel==2 || $nivel==1 || $nivel==3) &&
 ($autorizante == 111 || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'JEFE UNIDAD' ||
  $USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'JEFE AF'
   || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI')
  || $modulo=='Compras/Gestionar_Solicitud' && ($nivel==3||$nivel==4) && $USER['rol'] == 'JEFE COMPRAS' || $USER['rol'] == 'COLABORADOR COMPRAS' ||
  $USER['rol'] == 'JEFE UACI'|| $USER['rol'] == 'COLABORADOR UACI' || $USER['rol'] == 'ADMINISTRADOR SICBAF'){
    if ($modulo=='Compras/Solicitud_Compra'){
      if ($nivel == 0 || $nivel == 1){
        $bandera = 1;
      }
    }
    if ($id_modulo == $this->User_model->obtenerModulo("Compras/Aprobar_Solicitud")){
      if($USER['rol'] == 'ADMINISTRADOR SICBAF' || $USER['rol'] == 'COLABORADOR BODEGA' || $USER['rol'] == 'JEFE BODEGA' || $USER['rol'] == 'JEFE UNIDAD' || $USER['rol'] == 'JEFE COMPRAS' ||
      $USER['rol'] == 'JEFE UACI' || $USER['rol'] == 'JEFE AF' || $USER['rol'] == 'COLABORADOR AF' || $USER['rol'] == 'COLABORADOR COMPRAS' || $USER['rol'] == 'COLABORADOR UACI'){
        if ($nivel == 1 || $nivel == 2){
          $bandera = 1;
        } else {
          $bandera = 0;
        }
      }
      elseif ($autorizante == 111){
        if ($nivel == 2 || $nivel == 3){
          $bandera = 1;
        } else {
          $bandera = 0;
        }
      }
    }
    if ($modulo == 'Compras/Gestionar_Solicitud'){
      if ($nivel == 3 || $nivel == 4){
        $bandera = 1;
      }
    }

    if ($bandera ==1){
  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'> Detalle Solicitud</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
      echo form_open("/Compras/$controller/RecibirDatos", $atributos);

      $det = array(
          'name' => 'autocomplete1',
          'placeholder' => 'Ingrese Obra,bien o servicio solicitado',
          'class' => "form-control autocomplete",
          'autocomplete' => 'off',
          'uri' => 'index.php/Compras/Detalle_Solicitud_Compra/AutocompleteEspecificoProductoCompras',
          'name_op' => 'producto',
          'siguiente' => 'cantidad',
          'content' => 'suggestions1'
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

          echo form_hidden('solicitud', $id_solicitud_compra);
          echo form_hidden('modulo',$modulo);
          echo form_hidden('id_modulo',$id_modulo);
          echo form_hidden('id_detalle_solicitud_compra');
          echo form_submit('guardar','Agregar', $button);

          echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'producto','autocomplete1','cantidad'],false,['especificaciones'])\">Limpiar</a>";

      echo "</fieldset>";
      echo form_close();
      echo "</div>";
      echo "<div class='barra_carga'>";
      echo "</div>";
    echo "</div>";
    }
  }
?>
