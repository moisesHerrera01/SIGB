// Reglas de validacion de bienes inmuebles
var reglas = {
  rules: {
    "extension": {
        min: 0,
    },
    "precio": {
        min: 0,
    },
  },
  messages: {
    "extension": {
      min: "No debe ser negativo.",
    },
    "precio": {
      min: "No debe ser negativo.",
    },
  },
};
