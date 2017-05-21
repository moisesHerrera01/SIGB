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
