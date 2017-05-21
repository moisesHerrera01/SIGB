// Reglas de validacion de bienes inmuebles
var reglas = {
  rules: {
    fecha_inicio: {
        minordate: true,
    },
    fecha_fin: {
        depend: 'fecha_inicio',
        comparedate: 'fecha_inicio',
    },
    autocomplete1: {
      checkautocomplete: 'fuente'
    }
  },
};
