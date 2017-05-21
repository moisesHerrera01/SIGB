<?= $this->breadcrumb->build_breadcrump($this->uri->uri_string()); ?>

<form class='form-horizontal' enctype='multipart/form-data'>
  <label class="btn btn-success btn-file">
    Seleccion Archivo&hellip; <input type="file" id="archivo" name="archivo" style="display: none;">
  </label>
  <div class="messages"></div>
  <p></p>
<input class='btn btn-success' type='button' name='subir' value='Subir Archivo' onclick="subir()">
</form>
<br/><br/>
