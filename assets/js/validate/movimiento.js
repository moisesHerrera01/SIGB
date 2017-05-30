//Reglas de validacion de movimiento
var reglas = {
  rules: {
    "usuario_externo": {
        lettersonly: true,
    },
    "entregado_por": {
        lettersonly: true,
    },
    "recibido_por": {
        lettersonly: true,
    },
    "autorizado_por": {
        lettersonly: true,
    },
    "visto_bueno_por": {
        lettersonly: true,
    },
  },
  messages: {
    "usuario_externo": {
      lettersonly: "Ingrese un nombre valido.",
    },
    "entregado_por": {
      lettersonly: "Ingrese un nombre valido.",
    },
    "recibido_por": {
      lettersonly: "Ingrese un nombre valido.",
    },
    "autorizado_por": {
      lettersonly: "Ingrese un nombre valido.",
    },
    "visto_bueno_por": {
        lettersonly: "Ingrese un nombre valido.",
    },
  },
};

function ocultar_inputs() {
  switch ($('input[name=tipo_movimiento]').val()) {
    case '2':
      $('#recibe').show();
      $('#entrega').hide();
      $('input[name=oficina_entrega]').val(28);
      break;
    case '3'://descargo
      $('#entrega').show();
      $('#recibe').hide();
      $('input[name=oficina_recibe]').val(28);
      break;
    default:
      $('#entrega').show();
      $('#recibe').show();
  }
}

$(document).ready(function () {
  autocomplete_func($('input[name=autocomplete5]'), ocultar_inputs);
});
