//Reglas de validacion de factura
var reglas = {
  rules: {
    "conteo": {
        required: true,
        //checkautocomplete: 'compromiso'
    },
  },
  messages: {
    "conteo": {
      required: "El Compromiso es obligatorio."
    },
  },
};

$(document).ready(function() {
  //categoria
  $.autocomplete({
    elemet: $('input[name=conteo]'),
    url: 'index.php/Bodega/ConteoFisico/Autocomplete',
    name: 'nada',
    siguiente: 'button',
    content: 'suggestions'
  });
});
