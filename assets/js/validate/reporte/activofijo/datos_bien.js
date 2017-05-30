
var reglas = {
  rules: {
    "autocomplete": {
        required: true,
        //checkautocomplete: 'compromiso'
    },
  },
  messages: {
    "autocomplete": {
      required: "El bien es obligatorio."
    },
  },
};

$(document).ready(function() {
  //categoria
  $.autocomplete({
    elemet: $('input[name=autocomplete]'),
    url: 'index.php/ActivoFijo/Reportes/Datos_del_bien/AutocompleteBien',
    name: 'bien',
    siguiente: 'button',
    content: 'suggestions',
  });
});
