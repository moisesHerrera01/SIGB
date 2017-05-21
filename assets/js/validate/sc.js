//Reglas de validacion de solicitud compra
var reglas = {
  rules: {
    "valor": {
      required: true,
    },
    "justificacion": {
      required: true,
    },
    "fecha": {
      required: true,
      minordate: true
    },
    "autocomplete1": {
      required: true,
      checkautocomplete: 'sol'
    },
    "autocomplete2": {
      required: true,
      checkautocomplete: 'auto'
    },
    "autocomplete3": {
      required: true,
      checkautocomplete: 'admin'
    },
  },
  messages: {
    "valor": {
      required: "El valor estimado es obligatorio."
    },
    "justificacion": {
      required: "Se debe justificar la compra.",
    },
    "autocomplete1":{
        required: "El solicitante es obligatorio.",
    },
    "autocomplete2":{
        required: "El autorizante es obligatorio.",
    },
    "autocomplete3":{
        required: "El admin OC es obligatorio.",
    },
    "fecha": {
      required: "La fecha es obligatoria.",
    }
  },
};
