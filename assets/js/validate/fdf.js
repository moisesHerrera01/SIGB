//Reglas de validacion de fuente de fondo
var reglas = {
  rules: {
    "nombreFuente": {
        required: true
    },
    "codigo": {
        required: true,
    }
  },
  messages: {
    "nombreFuente": {
      required: "El nombre es obligatorio."
    },
    "codigo": {
      required: "El Código es obligatorio.",
    }
  },
};
