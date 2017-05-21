function set_Date(string) {
  var fechaux = string.split("-");
  var fechaintro = new Date(parseInt(fechaux[0])+"-"+parseInt(fechaux[1])+"-"+parseInt(fechaux[2]));
  return fechaintro;
}

//valida que en input se escriban solo letras
jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-z-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ]+$/i.test(value);
}, "Solo letras");

//valida que en un input de tipo fecha no sea mayor a la fecha actual
jQuery.validator.addMethod("minordate", function(value, element) {

  if ("" != value) {
    var fechaintro = set_Date(value);
    var fechactual = new Date();

    if (fechaintro > fechactual) {

      return false;

    } else {

      return true;

    }

  } else {
    return true;
  }

}, "Fecha seleccionada no valida.");

//valida una fecha inicial y una final
//param es nombre del input
jQuery.validator.addMethod("comparedate", function(value, element, param) {

  if ("" != value) {

    var fechaux = value.split("-");
    var fechainicial = set_Date($('input[name='+param+']').val());
    var fechafinal = set_Date(value);

    if (fechafinal >= fechainicial) {

      return true;

    } else {

      return false;

    }

  } else {
    return true;
  }

}, "Fecha final es menor a la inicial.");

//da error si la dependencia es vacia
jQuery.validator.addMethod("depend", function(value, element, param) {

  if ("" != $('input[name='+param+']').val()) {

    return true;

  } else {
    return false;
  }

}, "Digitar fecha incial antes.");


//valida que se ha seleccionado un elemento del autocomplete
jQuery.validator.addMethod("checkautocomplete", function(value, element, param) {

  if ("" != $('input[name='+param+']').val() && "" != value) {

    return true;

  } else {

    return false;

  }

}, "Debe seleccionar un elemento.");
