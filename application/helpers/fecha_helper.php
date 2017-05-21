<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('fecha')) {

  function fecha($fecha) {

    $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    $arrayDias = array( 'Domingo', 'Lunes', 'Martes',
        'Miercoles', 'Jueves', 'Viernes', 'Sabado');
    $date=date_create($fecha);
    $fechatxt = date_format($date,"d")." de ".$arrayMeses[date_format($date,"n")-1]." del ".date_format($date,"Y");

    return $fechatxt;
  }
}
