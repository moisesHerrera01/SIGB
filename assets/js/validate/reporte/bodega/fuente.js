//Reglas de validacion de factura
var reglas = {
  rules: {
    "autocomplete2": {
        required: true,
        //checkautocomplete: 'compromiso'
    },
  },
  messages: {
    "autocomplete2": {
      required: "La fuente de fondo es obligatoria."
    },
  },
};

$(document).ready(function() {
  //categoria
  $.autocomplete({
    elemet: $('input[name=autocomplete2]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'button',
    content: 'suggestions'
  });
});
