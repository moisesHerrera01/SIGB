$(document).ready(function(){

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
        showMessage("<span class='info'>Archivo para subir: "+fileName+", peso total: "+fileSize+" bytes.</span>");
    });

    //al enviar el formulario
    $(':button').click(function() {
        //información del formulario
        var formData = new FormData($(".form-horizontal")[0]);
        var message = "";
        //hacemos la petición ajax
        $.ajax({
            url: baseurl + "index.php/Bodega/Cargamasiva/cargar_archivo",
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
              $(".content_table").html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
              var angulo = 0;
              setInterval(function(){
                    angulo += 3;
                   $("#cargando").rotate(angulo);
              },10);
            },
            //una vez finalizado correctamente
            success: function(data){
                message = $("<span class='success'>El archivo ha subido correctamente.</span>");
                showMessage(message);
                $(".content_table").html(data);

            },
            //si ha ocurrido un error
            error: function(){
                message = $("<span class='error'>Ha ocurrido un error.</span>");
                showMessage(message);
            }
        });
    });
});

function cargar() {
    //hacemos la petición ajax
    $.ajax({
        url: baseurl + "index.php/Bodega/Cargamasiva/CargaMasiva",
        type: 'POST',
        //necesario para subir archivos via ajax
        cache: false,
        contentType: false,
        processData: false,
        //mientras enviamos el archivo
        beforeSend: function(){
          $(".mensaje_ajax").html("<p id='cargando' align='center' class='icono icon-spinner'></p>");
          var angulo = 0;
          setInterval(function(){
                angulo += 3;
               $("#cargando").rotate(angulo);
          },10);
        },
        //una vez finalizado correctamente
        success: function(data){
            $(".mensaje_ajax").html("<span class='success'>Los datos han sido introducidos correctamente.</span>");
        },
        //si ha ocurrido un error
        error: function(){
            message = $("<span class='error'>Ha ocurrido un error.</span>");
            showMessage(message);
        }
    });
}

//como la utilizamos demasiadas veces, creamos una función para
//evitar repetición de código
function showMessage(message){
    $(".messages").html("").show();
    $(".messages").html(message);
}
