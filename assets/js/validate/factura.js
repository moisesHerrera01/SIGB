//Reglas de validacion de factura
var reglas = {
  rules: {
    "numeroFactura": {
        required: true
    },
    "autocomplete1": {
        required: true,
        checkautocomplete: 'compromiso'
    },
    "nombreEntrega": {
      required: true,
      lettersonly: true,
    },
    "fechaFactura": {
      required: true,
      minordate: true
    },
  },
  messages: {
    "numeroFactura": {
      required: "El número de la factura es obligatorio."
    },
    "autocomplete1": {
      required: "El Compromiso es obligatorio."
    },
    "nombreEntrega": {
      required: "El nombre entrega es obligatorio.",
      lettersonly: "El nombre entrega solo ocupa letras",
    },
    "fechaFactura": {
      required: "La fecha de la factura es obligatoria.",
    },
  },
};

$(document).ready(function() {

    $('#suggestions1').click(function(){
      var id = $('input[name=compromiso]').val();
      var content = 'content_detalle';
      $.ajax({
        type: 'post',
        url: baseurl + 'index.php/Compras/Compromiso_Presupuestario/generarJsonCompromiso',
        dataType: 'json',
        data: { id: id },
        beforeSend: function(){
          $("#"+content).html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
          var angulo = 0;
          setInterval(function(){
                angulo += 3;
               $("#cargando").rotate(angulo);
          },10);
        },
        success: function(result) {
          var info = '';
          info += "<p>Compromiso: " + result[0].numero_compromiso + "</p>";
          $('input[name=compromiso]').val(result[0].id_compromiso);
          info += "<p>Orden de compra: " + result[0].numero_orden_compra + "</p>";
          info += "<p>Monto total: $" + result[0].monto_total_oc + "</p>";
          $('input[name=orden]').val(result[0].id_orden_compra);
          info += "<p>Fuente Fondos: " + result[0].nombre_fuente + "</p>";
          $('input[name=fuente]').val(result[0].id_fuentes);
          info += "<p>Proveedor: " + result[0].nombre_proveedor + "</p>";
          $('input[name=proveedor]').val(result[0].id_proveedores);
          info += "<p>Requisitor: " + result[0].nombre_seccion + "</p>";
          $('input[name=seccion]').val(result[0].id_seccion);
          $('input[name=orden]').val(result[0].id_orden_compra);
          var detalle = result[0].detalle_orden;
          info += "<p>Productos:</p>";
          info += "<ul>";
          for (var i = 0; i < detalle.length; i++) {
            info += "<li>";
              info += detalle[i].id_especifico +" - "+ detalle[i].nombre_producto +", U.M: "+ detalle[i].unidad + " - Cantidad: " + detalle[i].cantidad;
            info += "</li>";
          }
          info += "</ul>";
          $('#'+content).fadeIn(300).html(info);
        },
      });
    });
});
