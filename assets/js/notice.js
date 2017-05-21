var not = 0;
var max = 2;
var activo = 0;

$(document).ready(function() {

  $('html').click(function () {
    $('.content-area-notice').slideUp();
    activo = 0;
  });

  $('#notice').click(function (e) {
    if (1 == activo) {

      $('.content-area-notice').slideUp();
      activo = 0;

    } else if (0 == activo) {

      $('.content-area-notice').slideDown();
      Notificaciones();
      activo = 1;

    }

    e.stopPropagation();
  });

  TotalNotificaciones();
  setInterval(function() {
      TotalNotificaciones();
  }, 60000);
});

var getLocation = function() {
  var arrayLocation = String(window.location).split('/');
  return arrayLocation[5];
}


var Notificaciones = function() {
  var elemet = $(this);
  var content = $('.content-area-notice');
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: baseurl + "index.php/Notificacion/ConsultarNotificaciones",
    //data: {},
    success: function(result) {
      var mensaje = "";
      var i;
      if (result) {
        for (i = 0; i < result.length; i++) {
          mensaje += "<div class='alert alert-"+ result[i].clase_notificacion +" alert-dismissable'><a href="+result[i].url_notificacion+">";
            mensaje += "<button type='button' class='close' data-id='"+result[i].id_notificacion+"' data-dismiss='alert'>&times;</button>";
            mensaje += result[i].mensaje_notificacion;
          mensaje += "</a></div>";
        }
      } else {
        mensaje = "<div class='name'>Notificaciones</div>";
      }

      content.html(mensaje);

      $('.close').click(function() {
        var id = $(this).data('id');
        $.ajax({
          type: 'post',
          data: { id: id },
          url: baseurl + "index.php/Notificacion/EliminarDato",
          success: function(result) {
            $('#notice .badge').text(result);
          }
        });
      });
    },
  });
}

var Notificacion = function() {
  var elemet = $(this);
  var content = $('.content-notice');
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: baseurl + "index.php/Notificacion/ConsultarNotificaciones",
    success: function(result) {
      var mensaje = "";
      for (var i = 0; i < result.length; i++) {
        mensaje += "<div class='alert alert-"+ result[i].clase_notificacion +" alert-dismissable' style='display: 'none''><a href="+result[i].url_notificacion+">";
          mensaje += "<button type='button' class='close' data-id='"+result[i].id_notificacion+"' data-dismiss='alert'>&times;</button>";
          mensaje += result[i].mensaje_notificacion;
        mensaje += "</a></div>";
        if (max == i) {
            break;
            max = 2;
        }
      }

      content.html(mensaje);

      $(".content-notice .alert").slideDown();
      setInterval(function() {
          $(".content-notice .alert").slideUp();
      }, 5000);
    },
  });
}

var TotalNotificaciones = function () {
  var elemet = $(this);
  var content = $('.content-area-notice');
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: baseurl + "index.php/Notificacion/TotalNotificaciones",
    success: function(result) {
      $('#notice .badge').text(result);
      if (result > not) {
          max = 0;
          Notificacion();
      }
      not = result;
    },
  });
}
