$(document).ready(function(){
  //abrir modal
  $('.modal_open').click(open_modal);
});

function open_modal() {
  var id_sol = $(this).data('id');
  $.ajax({
    type: 'post',
    dataType: 'json',
    data: {id : id_sol},
    url: baseurl + "index.php/Compras/Solicitud_Compra/ConsultarSolicitudJson",
    success: function(result) {
      $(".modal .modal-dialog .modal-content .modal-body #cmt1").val(result[0].jefe);
      $(".modal .modal-dialog .modal-content .modal-body #cmt2").val(result[0].autorizante);
      $(".modal .modal-dialog .modal-content .modal-body #cmt3").val(result[0].compras);
    },
  });
  $("#cmt-modal").modal('toggle');
}
