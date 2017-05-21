//Reglas de validacion de proveedores
var reglas = {
  rules: {
    "autocomplete": {
        required: true,
        checkautocomplete: 'categoria'
    },
    "nombreProveedor": {
      required: true,
    },
    "nit": {
      required: true,
    },
  },
  messages: {
    "autocomplete": {
      required: "La categoria es obligatoria.",
    },
    "nombreProveedor": {
      required: "El nombre es obligatorio.",
    },
    "nit": {
      required: "El NIT es obligatorio.",
    },
  },
};
