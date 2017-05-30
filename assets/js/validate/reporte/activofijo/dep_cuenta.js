
var reglas = {
  rules: {
    "autocomplete7": {
        required: true,
        //checkautocomplete: 'compromiso'
    },
    "autocomplete6": {
        required: true,
        //checkautocomplete: 'compromiso'
    },
    "fecha": {
      required: true,
    },
  },
  messages: {
    "autocomplete7": {
      required: "La cuenta contable es obligatoria."
    },
    "autocomplete6": {
      required: "La fuente de fondo es obligatoria."
    },
    "fecha": {
      required: "La fecha es obligatoria.",
    },
  },
};

$(document).ready(function() {
  //cuenta
  $.autocomplete({
    elemet: $('input[name=autocomplete7]'),
    url: 'index.php/ActivoFijo/Datos_comunes/AutocompleteCuentas',
    name: 'cuenta',
    siguiente: 'codificar',
    content: 'suggestions1',
  });

  //fuente
  $.autocomplete({
    elemet: $('input[name=autocomplete6]'),
    url: 'index.php/Bodega/Fuentefondos/Autocomplete',
    name: 'fuente',
    siguiente: 'fecha',
    content: 'suggestions2',
  });
});
