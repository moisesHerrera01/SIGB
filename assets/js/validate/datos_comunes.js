//Reglas de validacion de datos comunes
var reglas = {
  rules: {
    "descripcion": {
        required: true,
    },
    "precio": {
        required: true,
        min:0,
    },
    "garantia": {
        required: true,
        min:0,
    },
  },
  messages: {
    "descripcion": {
      required: "Este campo es obligatorio.",
    },
    "precio": {
      min: "No debe ser negativo.",
      required: "Este campo es obligatorio.",
    },
    "garantia": {
      min: "No debe ser negativo.",
      required: "Este campo es obligatorio.",
    },
  },
};
