//Reglas de validacion de solicitud disponibilidad
var reglas = {
  rules: {
    "autocomplete": {
        required: true
    },
    "fechaIngreso":{
      required: true,
      minordate: true
    },
    "fecha":{
      minordate: true
    },
  },
  messages: {
    "autocomplete": {
      required: "la solicitud es obligatoria."
    },
    "fechaIngreso":{
      required: "La fecha es obligatoria."
    }
  },
};

$(document).ready(function(){

    $('.modal_open').click(function() {
      var id_sol = $(this).data('id');
      $(".modal .modal-dialog .modal-content .modal-body input[name=disp]").val(id_sol);
      $("#cmt-modal").modal('toggle');
    });

    $(".messages").hide();
    //queremos que esta variable sea global
    var fileExtension = "";
    //función que observa los cambios del campo file y obtiene información
    $(':file').change(function() {
        //obtenemos un array con los datos del archivo
        var file = $("#archivo")[0].files[0];
        //obtenemos el nombre del archivo
        var fileName = file.name;
        //obtenemos la extensión del archivo
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        //obtenemos el tamaño del archivo
        var fileSize = file.size;
        //obtenemos el tipo de archivo image/png ejemplo
        var fileType = file.type;
        //mensaje con la información del archivo
        showMessage("<span class='info'>Archivo a subir: "+fileName+", peso total: "+fileSize+" bytes.</span>");
    });

    //al enviar el formulario
    $('button[name=subir]').click(function() {
        //información del formulario
        var formData = new FormData($("#memo")[0]);
        var message = "";
        //hacemos la petición ajax
        $.ajax({
            url: baseurl + "index.php/Compras/Solicitud_Disponibilidad/cargar_archivo",
            type: 'POST',
            // Form data
            //datos del formulario
            data: formData,
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,
            //mientras enviamos el archivo
            beforeSend: function(){
              message = $("<span class='success'>Subiendo...</span>");
              showMessage(message);
            },
            //una vez finalizado correctamente
            success: function(data){
                message = $("<span class='success'>"+data+"</span>");
                showMessage(message);

                $(location).attr('href',baseurl + 'index.php/Compras/Solicitud_Disponibilidad/index/update');
            },
            //si ha ocurrido un error
            error: function(){
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    });
});

//como la utilizamos demasiadas veces, creamos una función para
//evitar repetición de código
function showMessage(message){
    $(".messages").html("").show();
    $(".messages").html(message);
}
