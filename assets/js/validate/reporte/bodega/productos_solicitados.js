//Reglas de validacion de factura
var reglas = {
  rules: {
    "fechaMin": {
      required: true,
    },
    "fechaMax": {
      required: true,
      comparedate: 'fechaMin'
    },
    "autocomplete1": {
      required: true,
      checkautocomplete: 'fuente'
    },
    "cantidad": {
      required: true,
      min: 1
    },
  },
  messages: {
    "fechaMin": {
      required: "La Fecha inicial es obligatoria.",
    },
    "fechaMax": {
      required: "La fecha final es obligatoria.",
    },
    "autocomplete1": {
      required: 'La fuente de fondo es obligatoria.',
    },
    "cantidad": {
      required: 'La Cantidad es obligatoria.',
      min: 'La cantidad debe ser mayor a 0.',
    },
  },
};

$(document).ready(function() {

  //fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete1]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'fechaMin',
    content: 'suggestions'
  });

});
