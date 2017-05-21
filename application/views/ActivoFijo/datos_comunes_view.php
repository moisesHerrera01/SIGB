<?php

  $atributos = array(
    'class' => 'form-horizontal stepMe',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());

  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
      echo "<span class='icono icon-paste icon-title'>Dato Común</span>";
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Datos_comunes/RecibirDatos", $atributos);

    $sub = array(
      'name' => 'autocomplete1',
      'placeholder' => 'Ingrese Subcategoría',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Datos_comunes/AutocompleteSubcategorias',
      'name_op' => 'subcategoria',
      'siguiente' => 'tipo_movimiento',
      'content' => 'suggestions1'
  );

  $mar = array(
      'name' => 'autocomplete3',
      'placeholder' => 'Ingrese Marca',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Datos_comunes/AutocompleteMarcas',
      'name_op' => 'marca',
      'siguiente' => 'descripcion',
      'content' => 'suggestions3'
  );

  $desc = array(
      'name' => 'descripcion',
      'placeholder' => 'Ingrese descripción',
      'class' => "form-control"
  );

  $mod = array(
      'name' => 'modelo',
      'placeholder' => 'Ingrese Modelo',
      'class' => "form-control"
  );

  $col = array(
      'name' => 'color',
      'placeholder' => 'Ingrese color',
      'class' => "form-control",
  );

  $doc = array(
      'name' => 'autocomplete4',
      'placeholder' => 'Ingrese tipo de documento',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Datos_comunes/AutocompleteDocumentos',
      'name_op' => 'doc_ampara',
      'siguiente' => 'nombre_doc',
      'content' => 'suggestions4'
  );

  $nom = array(
      'name' => 'nombre_doc',
      'placeholder' => 'Ingrese Nombre del documento',
      'class' => "form-control"
  );

  $fecha = array(
      'name' => 'fecha',
      'placeholder' => 'Ingrese fecha adquisición',
      'class' => "form-control",
      'type'=>"date"
  );

  $pre = array(
      'name' => 'precio',
      'placeholder' => 'Ingrese precio unitario',
      'class' => "form-control",
      'type'=>"number",
  );

  $prov = array(
      'name' => 'autocomplete5',
      'placeholder' => 'Ingrese proveedor',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/Bodega/Proveedores/Autocomplete',
      'name_op' => 'proveedor',
      'siguiente' => 'proyecto',
      'content' => 'suggestions5'
  );

  $proy = array(
      'name' => 'autocomplete6',
      'placeholder' => 'Ingrese Proyecto',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/Bodega/Fuentefondos/Autocomplete',
      'name_op' => 'proyecto',
      'siguiente' => 'garantia',
      'content' => 'suggestions6'
  );

  $gar = array(
      'name' => 'garantia',
      'placeholder' => 'Ingrese garantia en meses',
      'class' => "form-control",
      'type'=>"number"
  );

  $obs = array(
      'name' => 'observacion',
      'placeholder' => 'Ingrese observacion',
      'class' => "form-control"
  );

  $cuen = array(
      'name' => 'autocomplete7',
      'placeholder' => 'Ingrese cuenta contable',
      'class' => "form-control autocomplete",
      'autocomplete' => 'off',
      'uri' => 'index.php/ActivoFijo/Datos_comunes/AutocompleteCuentas',
      'name_op' => 'cuenta',
      'siguiente' => 'codificar',
      'content' => 'suggestions7'
  );

  $atriLabel = array('class' => 'col-lg-2 control-label');

 $button = array('class' => 'btn btn-success',);

       echo "<fieldset>";
        echo "<legend>Paso 1:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Subcategoría:', 'sub', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($sub);
      echo form_hidden('subcategoria');
      echo '<div id="suggestions1" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Marca:', 'mar', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($mar);
      echo form_hidden('marca');
      echo '<div id="suggestions3" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Descripción:', 'desc', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($desc);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Modelo:', 'mod', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($mod);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Color:', 'col', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($col);
    echo "</div>";
  echo "</div>";
   echo "</fieldset>";

      echo "<fieldset>";
        echo "<legend>Paso 2:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Tipo documento:', 'doc', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($doc);
      echo form_hidden('doc_ampara');
      echo '<div id="suggestions4" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Nombre documento:', 'nom', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($nom);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Fecha adquisición:', 'fecha', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($fecha);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Precio unitario:', 'pre', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($pre);
    echo "</div>";
  echo "</div>";
   echo "</fieldset>";

      echo "<fieldset>";
        echo "<legend>Paso 3:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Proveedor:', 'prov', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($prov);
      echo form_hidden('proveedor');
      echo '<div id="suggestions5" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Proyecto:', 'proy', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($proy);
      echo form_hidden('proyecto');
      echo '<div id="suggestions6" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Garantía:', 'gar', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($gar);
    echo "</div>";
  echo "</div>";
   echo "</fieldset>";

        echo "<fieldset>";
        echo "<legend>Paso 4:</legend>";
  echo "<div class='form-group'>";
    echo form_label('Observación:', 'obs', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($obs);
    echo "</div>";
  echo "</div>";

  echo "<div class='form-group'>";
    echo form_label('Cuenta Contable:', 'cuen', $atriLabel);
    echo "<div class='col-lg-10'>";
      echo form_input($cuen);
      echo form_hidden('cuenta');
      echo '<div id="suggestions7" class="suggestions"></div>';
    echo "</div>";
  echo "</div>";

  echo form_hidden('id');

  echo form_submit('','Guardar', $button);

  echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id','subcategoria','autocomplete1', 'marca','autocomplete3',
  'descripcion','modelo','color','doc_ampara','autocomplete4','nombre_doc','fecha','precio','proveedor','autocomplete5',
  'proyecto','autocomplete6','garantia','observacion','cuenta','autocomplete7'])\">Limpiar</a>";
echo "</fieldset>";

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
    'url' => 'index.php/ActivoFijo/Datos_comunes/mostrarTabla'
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
