<?php

  /*
  * ########################################
  * Este solo recibe 2 tipos de variables
  * alert: que desplegara dependiendo de cual
  * se le indique.
  * controller: la cual se activara dependiendo
  * del controlador que se le mande esta es opcional.
  * La unica variable a retornara sera mensaje
  * la cual advertira de algun error.
  *#########################################
  */

$anuncio = "";

if (isset($alert)) {
  if ($alert != "" && !is_numeric($alert)) {

    switch ($alert) {
      case 'new':
        $mensaje = "Registro Ingresado Correctamente.";
        break;
      case 'delete':
        $mensaje = "Registro Eliminado Correctamente.";
        break;
      case 'update':
        $mensaje = "Registro Modificado Correctamente.";
        break;
      case 'forbidden':
        $mensaje = "Ud. no posee los permisos necesarios";
        $tipo = 'danger';
        break;
      default:
        break;
    }
    /*
    * Aqui se pueden ubicar todos los controller que fuesen
    * necesarios.
    */
    if (isset($controller)) {
      switch ($controller) {
        case 'Detallefactura':
          switch ($alert) {
            case 'cargar':
              $mensaje = "Producto cargado con éxito a la Factura.";
              break;
            case 'cargada':
              $mensaje = "El Producto ya ha sido cargado.";
              break;
            case 'validar':
              $mensaje = "El producto no esta asignado al especifico.";
              break;
            default:
              break;
          }
          case 'solicitud_compra':
            switch ($alert) {
              case 'nueva':
                $mensaje = "Solicitud $numero creada con éxito.";
                break;
                case 'noexisteSol':
                  $mensaje = "Ingresar detalle de solicitud.";
                  $tipo = 'warning';
                  break;
                case 'send':
                  $mensaje = "Solicitud eviada con éxito.";
                  break;
                  case 'existeSol':
                  $mensaje = "Solicitud contiene detalle.";
                  $tipo = 'warning';
                  break;
              default:
                break;
            }
            case 'Seleccion_Subcategoria':
              switch ($alert) {
                    case 'existeequipo':
                    $mensaje = "Equipo informático contiene detalle.";
                    $tipo = 'warning';
                    break;
                default:
                  break;
              }
            case 'Gestionar_Solicitud':
              switch ($alert) {
                case 'descargado':
                  $mensaje = "Descargado con éxito";
                  break;
                    break;
                default:
                  break;
              }
              case 'Aprobar_Solicitud':
                switch ($alert) {
                  case 'descargado':
                    $mensaje = "Descargado con éxito";
                    break;
                      break;
                  default:
                    break;
                }
            case 'Detalle_Solicitud_Compra':
              switch ($alert) {
                case 'nueva':
                  $numero=$numero;
                  $mensaje = "Solicitud $numero creada con éxito.";
                  break;
                  case 'noespecifico':
                    $mensaje = "Debe pertenecer al mismo especifico";
                    break;
                    case 'mismo':
                      $mensaje = "Este producto, bien o servicio ya fue agregado";
                      break;
                default:
                  break;
              }
              case 'Detalle_orden_resumen':
                switch ($alert) {
                  case 'nueva':
                    case 'noespecifico':
                      $mensaje = "Debe pertenecer al mismo especifico";
                      break;
                      case 'mismo':
                        $mensaje = "Este producto, bien o servicio ya fue agregado";
                        break;
                  default:
                    break;
                }
          break;

          case 'detalle_solicitud_disponibilidad':
            switch ($alert) {
              case 'nueva':
                $numero=$numero;
                $mensaje = "Solicitud $numero creada con éxito.";
                break;
                case 'noespecifico':
                  $mensaje = "Debe pertenecer al mismo especifico";
                  break;
                  case 'mismo':
                    $mensaje = "Este producto, bien o servicio ya fue agregado";
                    break;
              default:
                break;
            }
        break;
        case 'factura':
          switch ($alert) {
            case 'fact_liquidada':
              $mensaje = "La Factura ya ha sido liquidada.";
              break;
            case 'fact_liquidar':
              $mensaje = "Factura Liquidada con éxito.";
              break;
            case 'existe_fact':
              $mensaje = "Factura contiene detalle.";
              break;
              case 'valida_monto':
                $mensaje = "No liquidada, monto total de la factura no concuerda con el monto total de la orden de compra.";
                $tipo = 'danger';
                break;
            default:
              break;
          }
          break;
        case 'retiro':
          switch ($alert) {
            case 'liquidar':
              $mensaje = "Solicitud liquidada con éxito.";
              break;
            case 'liquidada':
              $mensaje = "La Solicitud ya ha sido liquidada.";
              break;
            case 'aprobado':
              $mensaje = "Solicitud aprobada con éxito.";
              break;
            case 'aprobada':
              $mensaje = "La Solicitud ya ha sido aprobada.";
              break;
            case 'existeRet':
              $mensaje = "Retiro contiene detalle.";
              break;
            default:
              break;
          }
            break;
        case 'detalle_retiro':
          switch ($alert) {
            case 'descargar':
              $mensaje = "Producto/s descargado/s con éxito.";
          break;
          case 'peps':
            $mensaje = "Precio asignado mediante PEPS.";
            break;
            case 'ya_asignado':
              $mensaje = "Precio ya ha sido asignado";
              break;
            case 'descargado':
              $mensaje = "El Producto ya ha sido descargado.";
              break;
            case 'exist':
              $mensaje = "Se ha ajustado la cantidad a las existencias.";
              break;
            case 'noexist':
              $mensaje = "No hay más existencias de este Producto.";
              break;
            case 'precio':
              $mensaje = "Se encuentra en proceso, no pude ser eliminado.";
              break;
            case 'sin_existencia':
                $mensaje = "Este producto, bien o servicio no tiene suficiente existencia";
                $tipo = 'danger';
              break;
            default:
              break;
            }
            break;
        case 'solicitud':
          switch ($alert) {
            case 'existeSol':
              $mensaje = "Solicitud contiene detalle.";
              $tipo = 'warning';
              break;
            case 'send':
              $mensaje = "Solicitud eviada con éxito.";
              break;
          case 'nueva':
            $mensaje = "Solicitud ingresada con éxito.";
            break;
            case 'noexisteSol':
              $mensaje = "Ingresar detalle de solicitud.";
              $tipo = 'warning';
            default:
              break;
            }
            break;

            case 'solicitud_retiro':
              switch ($alert) {
                case 'existeSol':
                  $mensaje = "Solicitud contiene detalle.";
                  $tipo = 'warning';
                  break;
              case 'nueva':
                $mensaje = "Solicitud ingresada con éxito.";
                break;
                case 'liquidar':
                  $mensaje = "Solicitud liquidada con éxito.";
                  break;
                case 'liquidada':
                  $mensaje = "La Solicitud ya ha sido liquidada.";
                  break;
                default:
                  break;
                }
                break;
        case 'detalle_solicitud_producto':
          switch ($alert) {
            case 'proceso':
              $mensaje = "Se encuentra en proceso, no pude ser eliminado.";
              break;
              case 'sol_nueva':
                $numero=$numero;
                $mensaje = "Solicitud $numero creada con éxito.";
                break;
              case 'mismos':
                $mensaje = "Este producto, bien o servicio ya fue agregado";
                break;
              case 'sin_existencia':
                  $mensaje = "Este producto, bien o servicio no tiene suficiente existencia";
                  $tipo = 'danger';
                break;
            default:
              break;
            }
            break;
        case 'especifico':
          switch ($alert) {
            case 'existe':
            $mensaje = "Especifico contiene detalle.";
            break;
          default:
            break;
          }
          break;
        case 'login':
          switch ($alert) {
            case 'cerrar':
              $mensaje = "Ha cerrado sesión con éxito.";
              break;
            case 'error_autenticar':
              $mensaje = "Lo sentimos. Las credenciales son erroneas.";
              $tipo = 'danger';
              break;
            case 'error_no_autenticado':
              $mensaje = "Lo sentimos. Aun no ha iniciado sesión.";
              $tipo = 'danger';
            case 'error_rol':
              $mensaje = "Lo sentimos. No posee permisos.";
              $tipo = 'danger';
              break;
              break;
            default:
              break;
          }
          case 'movimiento':
            switch ($alert) {
              case 'cerrar_estado':
                $mensaje = "Movimiento cerrado con éxito.";
                break;
              case 'no_delete':
                $mensaje = "No se puede eliminar, Contiene registros asociados.";
                break;
              default:
                break;
            }
            case 'tipo_movimiento':
              switch ($alert) {
                case 'no_delete':
                  $mensaje = "No se puede eliminar, Contiene registros asociados.";
                  break;
                default:
                  break;
              }
            case 'marcas':
              switch ($alert) {
                case 'no_delete':
                  $mensaje = "No se puede eliminar, Contiene registros asociados.";
                  break;
                default:
                  break;
              }
            case 'Datos_comunes':
              switch ($alert) {
                case 'no_delete':
                  $mensaje = "No se puede eliminar, Contiene registros asociados.";
                  break;
                default:
                  break;
              }
              case 'Oficinas':
                switch ($alert) {
                  case 'no_delete':
                    $mensaje = "No se puede eliminar, Contiene registros asociados.";
                    break;
                  default:
                    break;
                }
              case 'Almacenes':
                switch ($alert) {
                  case 'no_delete':
                    $mensaje = "No se puede eliminar, Contiene registros asociados.";
                    break;
                  default:
                    break;
                }
              case 'Cuenta_Contable':
                switch ($alert) {
                  case 'no_delete':
                    $mensaje = "No se puede eliminar, Contiene registros asociados.";
                    $tipo='warning';
                    break;
                  default:
                    break;
                }
            case 'doc_ampara':
              switch ($alert) {
                case 'no_delete':
                  $mensaje = "No se puede eliminar, Contiene registros asociados.";
                  $tipo='warning';
                  break;
                default:
                  break;
              }
            case 'bienes_inmuebles':
              switch ($alert) {
                case 'no_delete':
                  $mensaje = "No se puede eliminar, Contiene registros asociados.";
                  break;
                default:
                  break;
              }
            case 'bienes_muebles':
              switch ($alert) {
                case 'no_delete':
                  $mensaje = "No se puede eliminar, Contiene registros asociados.";
                  break;
                default:
                  break;
              }
              case 'Solicitud_movimiento':
                switch ($alert) {
                  case 'existeSol':
                    $mensaje = "Solicitud contiene detalle.";
                    $tipo = 'warning';
                    break;
                  case 'send':
                    $mensaje = "Solicitud eviada con éxito.";
                    break;
                case 'nueva':
                  $mensaje = "Solicitud ingresada con éxito.";
                  break;
                  case 'noexisteSol':
                    $mensaje = "Ingresar detalle de solicitud.";
                    $tipo = 'warning';
                  default:
                    break;
                  }
                  break;
          break;
        default:
          break;
      }
    }

    if (!isset($tipo)) {
      $tipo = 'info';
    }

    $anuncio = '<div class="content_alert"><div class="alert alert-'.$tipo.'" id="myAlert"><a class="close">&times;</a>
                <strong>'. $mensaje .'</strong></div></div>';

  } else {
    return;
  }
}

echo $anuncio;
?>
