<?php

  $atributos = array(
    'class' => 'form-horizontal',
    'role' => 'form'
  );

  echo $this->breadcrumb->build_breadcrump($this->uri->uri_string());
  echo "<div class='content-form'>";
    echo "<div class='limit-content-title'>";
    switch ($nombre_subcategoria) {
      case 'PROCESADOR':
        echo "<span class='icono icon-paste icon-title'> Procesador</span>";
        break;
      case 'DISCO DURO':
        echo "<span class='icono icon-paste icon-title'> Disco duro</span>";
        break;
      case 'MEMORIA':
        echo "<span class='icono icon-paste icon-title'> Memoria</span>";
      break;
      case 'OFFICE':
        echo "<span class='icono icon-paste icon-title'> Office</span>";
        break;
      case 'SISTEMA OPERATIVO':
        echo "<span class='icono icon-paste icon-title'> Sistema operativo</span>";
        break;
      case 'IP':
        echo "<span class='icono icon-paste icon-title'> Dirección ip</span>";
        break;
      case 'PUNTO DE RED':
        echo "<span class='icono icon-paste icon-title'> Punto de red</span>";
        break;
      default:
        # code...
        break;
    }
    echo "</div>";
    echo "<div class='limit-content'>";
  echo form_open("/ActivoFijo/Equipo_informatico/RecibirDatos", $atributos);

  switch ($nombre_subcategoria) {
    case 'DISCO DURO':
        $hdd = array(
            'name' => 'autocomplete3',
            'placeholder' => 'INGRESE LA CAPACIDAD DEL DISCO DURO',
            'class' => "form-control autocomplete",
            'autocomplete' => 'off',
            'uri' => 'index.php/ActivoFijo/Equipo_informatico/AutocompleteDiscoDuro',
            'name_op' => 'hdd',
            'siguiente' => 'memoria',
            'content' => 'suggestions3'
        );

        $v_hdd = array(
            'name' => 'v_hdd',
            'placeholder' => 'Ingrese la velocidad del disco duro',
            'class' => "form-control"
        );
      break;
    case 'PROCESADOR':
        $proc = array(
            'name' => 'autocomplete2',
            'placeholder' => 'INGRESE EL TIPO DE PROCESADOR',
            'class' => "form-control autocomplete",
            'autocomplete' => 'off',
            'uri' => 'index.php/ActivoFijo/Equipo_informatico/AutocompleteProcesador',
            'name_op' => 'procesador',
            'siguiente' => 'hdd',
            'content' => 'suggestions2'
        );

        $v_proc = array(
            'name' => 'v_procesador',
            'placeholder' => 'Ingrese la velocidad del procesador',
            'class' => "form-control"
        );
    break;
    case 'MEMORIA':
        $mem = array(
            'name' => 'autocomplete4',
            'placeholder' => 'INGRESE EL TIPO DE MEMORIA',
            'class' => "form-control autocomplete",
            'autocomplete' => 'off',
            'uri' => 'index.php/ActivoFijo/Equipo_informatico/AutocompleteMemoria',
            'name_op' => 'memoria',
            'siguiente' => 'so',
            'content' => 'suggestions4'
        );

        $v_mem = array(
            'name' => 'v_memoria',
            'placeholder' => 'Ingrese la velocidad de la memoria',
            'class' => "form-control"
        );
    break;
    case 'SISTEMA OPERATIVO':
        $so = array(
            'name' => 'autocomplete5',
            'placeholder' => 'INGRESE LA VERSIÓN DEL SISTEMA OPERATIVO',
            'class' => "form-control autocomplete",
            'autocomplete' => 'off',
            'uri' => 'index.php/ActivoFijo/Equipo_informatico/AutocompleteSistemaOperativo',
            'name_op' => 'so',
            'siguiente' => 'office',
            'content' => 'suggestions5'
        );

        $clave_so = array(
            'name' => 'clave_so',
            'placeholder' => 'Ingrese la clave del sistema operativo',
            'class' => "form-control"
        );
    break;
    case 'OFFICE':
        $off = array(
            'name' => 'autocomplete6',
            'placeholder' => 'INGRESE LA VERSIÓN DE OFFICE',
            'class' => "form-control autocomplete",
            'autocomplete' => 'off',
            'uri' => 'index.php/ActivoFijo/Equipo_informatico/AutocompleteOffice',
            'name_op' => 'office',
            'siguiente' => 'v_procesador',
            'content' => 'suggestions6'
        );

        $clave_off = array(
            'name' => 'clave_office',
            'placeholder' => 'Ingrese la clave de office',
            'class' => "form-control"
        );
    break;
    case 'IP':
        $ip = array(
            'name' => 'ip',
            'placeholder' => 'Ingrese la dirección ip',
            'class' => "form-control"
        );
    break;
    case 'PUNTO DE RED':
        $punto = array(
            'name' => 'punto',
            'placeholder' => 'Ingrese el número de punto',
            'class' => "form-control"
        );
    break;
    default:
      break;
  }
  $atriLabel = array('class' => 'col-lg-2 control-label');
  $button = array('class' => 'btn btn-success');

  switch ($nombre_subcategoria) {
    case 'DISCO DURO':
        echo "<div class='form-group'>";
          echo form_label('Capacidad:', 'hdd', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($hdd);
            echo form_hidden('hdd');
            echo '<div id="suggestions3" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Velocidad:', 'v_hdd', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($v_hdd);
          echo "</div>";
        echo "</div>";
      break;

    case 'PROCESADOR':
        echo "<div class='form-group'>";
          echo form_label('Procesador:', 'proc', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($proc);
            echo form_hidden('procesador');
            echo '<div id="suggestions2" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Velocidad:', 'v_proc', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($v_proc);
          echo "</div>";
        echo "</div>";
      break;

      case 'MEMORIA':
        echo "<div class='form-group'>";
          echo form_label('Memoria:', 'mem', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($mem);
            echo form_hidden('memoria');
            echo '<div id="suggestions4" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Velocidad:', 'v_mem', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($v_mem);
          echo "</div>";
        echo "</div>";
      break;
      case 'SISTEMA OPERATIVO':
        echo "<div class='form-group'>";
          echo form_label('Sistema operativo:', 'so', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($so);
            echo form_hidden('so');
            echo '<div id="suggestions5" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Clave:', 'clave_so', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($clave_so);
          echo "</div>";
        echo "</div>";
      break;

      case 'OFFICE':
        echo "<div class='form-group'>";
          echo form_label('Office:', 'off', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($off);
            echo form_hidden('office');
            echo '<div id="suggestions6" class="suggestions"></div>';
          echo "</div>";
        echo "</div>";

        echo "<div class='form-group'>";
          echo form_label('Clave:', 'clave_off', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($clave_off);
          echo "</div>";
        echo "</div>";
      break;
      case 'IP':
        echo "<div class='form-group'>";
          echo form_label('Ip:', 'ip', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($ip);
          echo "</div>";
        echo "</div>";
        break;
        case 'PUNTO DE RED':
        echo "<div class='form-group'>";
          echo form_label('Punto:', 'punto', $atriLabel);
          echo "<div class='col-lg-10'>";
            echo form_input($punto);
          echo "</div>";
        echo "</div>";
          break;
    default:
      break;
  }

  echo form_hidden('subcategoria',$subcategoria);
  echo form_hidden('id_equipo_informatico',$id_equipo_informatico);
  echo form_hidden('id');

  echo form_submit('','Guardar', $button);
  switch ($nombre_subcategoria) {
    case 'DISCO DURO':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'hdd','autocomplete3','v_hdd'])\">Limpiar</a>";
      break;
    case 'PROCESADOR':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'procesador','autocomplete2','v_procesador'])\">Limpiar</a>";
      break;
    case 'MEMORIA':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'memoria','autocomplete4','v_memoria'])\">Limpiar</a>";
      break;
    case 'SISTEMA OPERATIVO':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'so','autocomplete5','clave_so'])\">Limpiar</a>";
      break;
    case 'OFFICE':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'so','autocomplete6','clave_office'])\">Limpiar</a>";
      break;
    case 'IP':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'ip'])\">Limpiar</a>";
      break;
    case 'PUNTO DE RED':
        echo "<a class=\"btn btn-warning\" onclick=\"limpiar(['id', 'bien','autocomplete',
        'punto'])\">Limpiar</a>";
      break;
    default:
      # code...
      break;
  }
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
    'url' => 'index.php/ActivoFijo/Equipo_informatico/mostrarTabla/'.$subcategoria
  );

  echo "<div class='content_buscar'>";
  echo form_input($buscar);
  echo "</div>";
?>
