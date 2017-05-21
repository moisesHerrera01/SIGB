//Reglas de validacion de solicitud bodega
var reglas = {
  rules: {
    "autocomplete3": {
        required: true,
        checkautocomplete: 'seccion'
    },
    "autocomplete2": {
        required: true,
        checkautocomplete: 'id_fuentes'
    },
  },
  messages: {
    "autocomplete3": {
      required: "El nombre es sección es obligatorio."
    },
    "autocomplete2": {
      required: "La fuente de fondo es obligatoria."
    },
  },
};
