// Reglas de validacion de aprobar solicitud compras
var reglas = {
  rules: {
    "numero": {
      required: true,
      number: true
    },
    "autocomplete2":{
      required: true,
      checkautocomplete: 'fuentes'
    },
    "autocomplete4":{
      required: true,
      checkautocomplete: 'orden_compra'
    }
  },
  messages: {
    "numero": {
      required: "El numero de compromiso es obligatorio.",
      number: "Solo numeros."
    },
    "autocomplete2": {
      required: "La fuente de fondo es obligatoria."
    },
    "autocomplete4":{
      required: "La orden de compra es obligatoria."
    }
  },
};
