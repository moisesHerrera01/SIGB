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
    "autocomplete": {
      required: true,
      checkautocomplete: 'seccion'
    }
  },
  messages: {
    "fecha_inicio": {
      required: "La Fecha inicial es obligatoria.",
    },
    "fecha_fin": {
      required: "La fecha final es obligatoria.",
    },
    "autocomplete": {
      required: 'La seccion es obligatoria.'
    }
  },
};

$(document).ready(function() {
  //producto
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/Bodega/Solicitud/Autocomplete',
    name: 'seccion',
    siguiente: 'button',
    content: 'suggestions'
  });

});
