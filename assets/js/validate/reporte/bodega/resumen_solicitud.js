var reglas = {
  rules: {
    "minFecha": {
      required: true,
    },
    "maxFecha": {
      required: true,
      comparedate: 'minFecha'
    }
  },
  messages: {
    "minFecha": {
      required: "La Fecha inicial es obligatoria.",
    },
    "maxFecha": {
      required: "La fecha final es obligatoria.",
    }
  },
};
