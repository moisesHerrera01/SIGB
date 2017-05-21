//Reglas de validacion de solicitud control
var reglas = {
  rules: {
    "estado": {
      required: true,
    },
    "numero_solicitud": {
      required: true,
    },
  },
  messages: {
    "estado": {
      required: "El estado es obligatorio."
    },
    "numero_solicitud": {
      required: "Seleccione una solicitud a modificar.",
    },
  },
};
