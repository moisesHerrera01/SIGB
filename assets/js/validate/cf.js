//Reglas de validacion de conteo fisico
var reglas = {
  rules: {
    "nombre": {
        required: true,
    },
    "fecha_inicial": {
        required: true,
    },
    "fecha_final": {
        required: true,
    },
  },
  messages: {
    "nombre": {
        required: "El nombre es obligatorio."
    },
    "fecha_inicial": {
        required: "La fecha inicial es obligatoria."
    },
    "fecha_final": {
        required: "La fecha final es obligatoria."
    },
  },
};
