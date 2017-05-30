//Reglas de validacion de factura
var reglas = {
  rules: {
    "fecha_inicio": {
      required: true,
    },
    "fecha_fin": {
      required: true,
      comparedate: 'fecha_inicio'
    }
  },
  messages: {
    "fecha_inicio": {
      required: "La Fecha inicial es obligatoria.",
    },
    "fecha_fin": {
      required: "La fecha final es obligatoria.",
    }
  },
};
