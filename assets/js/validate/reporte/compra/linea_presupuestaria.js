//Reglas de validacion de factura
var reglas = {
  rules: {
    "fecha_inicio": {
      required: true,
    },
    "fecha_fin": {
      required: true,
      comparedate: 'fecha_inicio'
    },
    "autocomplete1": {
      required: true,
      checkautocomplete: 'fuente'
    },
  },
  messages: {
    "fecha_inicio": {
      required: "La Fecha inicial es obligatoria.",
    },
    "fecha_fin": {
      required: "La fecha final es obligatoria.",
    },
    "autocomplete1": {
      required: 'La linea presupuestaria es obligatoria.'
    }
  },
};

$(document).ready(function() {

  //fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Compras/Solicitud_Disponibilidad/AutocompleteLineaTrabajo',
    name: 'id_linea',
    siguiente: 'Generar',
    content: 'suggestions1'
  });

});
