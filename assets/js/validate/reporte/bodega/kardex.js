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
      checkautocomplete: 'producto'
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
    "autocomplete": {
      required: 'El producto es obligatorio.'
    },
    "autocomplete1": {
      required: 'La fuente de fondo es obligatoria.'
    }
  },
};

$(document).ready(function() {
  //producto
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/Bodega/Productos/Autocomplete',
    name: 'producto',
    siguiente: 'autocomplete1',
    content: 'suggestions'
  });

  //fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'btn',
    content: 'suggestions1'
  });

});
