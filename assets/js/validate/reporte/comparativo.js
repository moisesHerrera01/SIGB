//Reglas de validacion de cuadro comparativo
var reglas = {
  rules: {
    "anio": {
        required: true,
        min: 1
    },
  },
  messages: {
    "anio": {
      required: "La cantidad de años es obligatoria.",
      min: "La cantidad de años debe ser mayor o igual a 1"
    },
  },
};
