<?php
  class Bienes_muebles_model extends CI_Model{

    public $id_bien;
    public $id_dato_comun;
    public $codigo_anterior;
    public $serie;
    public $numero_motor;
    public $numero_placa;
    public $matricula;
    public $id_condicion_bien;
    public $observacion;
    public $correlativo;


    function __construct() {
        parent::__construct();
        $this->load->model(array('ActivoFijo/Movimiento_Model','ActivoFijo/Detalle_movimiento_model'));
    }

    public function insertarBienes_muebles($data){
        $this->id_dato_comun = $data['id_dato_comun'];
        $this->codigo_anterior = $data['codigo_anterior'];
        $this->serie = $data['serie'];
        $this->numero_motor = $data['numero_motor'];
        $this->numero_placa = $data['numero_placa'];
        $this->matricula = $data['matricula'];
        $this->id_condicion_bien = $data['id_condicion_bien'];
        $this->observacion = $data['observacion'];
        $this->correlativo = $data['correlativo'];
        $this->codigo = $data['codigo'];
        $this->db->insert('sic_bien', $this);
        $this->id_bien = $this->db->insert_id();

        $data_movimiento = array(
            'id_oficina_entrega' => 3,
            'id_oficina_recibe' => $data['id_oficina'],
            'id_empleado' => $data['id_empleado'],
            'id_tipo_movimiento' => 1,
            'usuario_externo' => "",
            'entregado_por' => "",
            'recibido_por' => $data['id_empleado'],
            'autorizado_por' => $data['id_empleado'],
            'visto_bueno_por' => $data['id_empleado'],
            'fecha_guarda' => date('Y-m-d'),
            'observacion' => "INGRESADO POR PRIMERA VEZ, INGRESADO POR MODULO BIEN"
        );

        $movimiento = $this->Movimiento_Model->insertarMovimiento($data_movimiento);

        $data_detalle_movimiento = array(
            'id_movimiento' => $movimiento,
            'id_bien' => $this->id_bien
        );

        $detalle_movimiento = $this->Detalle_movimiento_model->insertarDetalleMovimiento($data_detalle_movimiento);

        return $this->id_bien;
    }

    public function obtenerBienes_muebles(){
      $this->db->select('a.id_bien, e.codigo_anterior, e.codigo, e.serie, f.descripcion, c.id_oficina, c.nombre_oficina,
  		d.primer_nombre, d.primer_apellido, d.id_empleado, f.precio_unitario, g.nombre_marca, f.modelo, f.id_dato_comun,
      e.numero_motor, e.numero_placa, e.matricula, e.observacion')
              ->from('(
                	select max(id_detalle_movimiento) as id_detalle_movimiento, max(id_movimiento) as id_movimiento, id_bien
                	from sic_detalle_movimiento
                	group by id_bien
                	order by id_detalle_movimiento
                	) as a')
              ->join('sic_movimiento b', 'a.id_movimiento = b.id_movimiento')
              ->join('org_oficina c', 'b.id_oficina_recibe = c.id_oficina')
              ->join('sir_empleado d', 'd.id_empleado = b.id_empleado')
              ->join('sic_bien e', 'e.id_bien = a.id_bien')
              ->join('sic_datos_comunes f', 'f.id_dato_comun = e.id_dato_comun')
              ->join('sic_marcas g', 'g.id_marca = f.id_marca')
              ->order_by('e.id_bien','desc')
              ->where('e.terreno_zona=',NULL)
              ->where('e.tipo_inmueble=',NULL);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarBienes_muebles($busca){
      $this->db->select('a.id_bien, e.codigo_anterior, e.codigo, e.serie, f.descripcion, c.id_oficina, c.nombre_oficina,
  		d.primer_nombre, d.primer_apellido, d.id_empleado, f.precio_unitario, g.nombre_marca, f.modelo, f.id_dato_comun,
      e.numero_motor, e.numero_placa, e.matricula, e.observacion')
              ->from('(
                	select max(id_detalle_movimiento) as id_detalle_movimiento, max(id_movimiento) as id_movimiento, id_bien
                	from sic_detalle_movimiento
                	group by id_bien
                	order by id_detalle_movimiento
                	) as a')
              ->join('sic_movimiento b', 'a.id_movimiento = b.id_movimiento')
              ->join('org_oficina c', 'b.id_oficina_recibe = c.id_oficina')
              ->join('sir_empleado d', 'd.id_empleado = b.id_empleado')
              ->join('sic_bien e', 'e.id_bien = a.id_bien')
              ->join('sic_datos_comunes f', 'f.id_dato_comun = e.id_dato_comun')
              ->join('sic_marcas g', 'g.id_marca = f.id_marca')
              ->order_by('e.id_bien','desc')
              ->where('e.terreno_zona=',NULL)
              ->where('e.tipo_inmueble=',NULL)
              ->like('e.codigo', $busca)
              ->or_like('e.codigo_anterior', $busca);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerBien_mueble($id){
        $this->db->where('id_bien',$id);
        $query = $this->db->get('sic_bien');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $bin) {
            $nombre = $bin->codigo;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

    public function actualizarBien_mueble($id, $data){
      $this->db->where('id_bien',$id);
      $this->db->update('sic_bien', $data);
    }

    public function eliminarBien_mueble($id){
      $this->db->delete('sic_bien', array('id_bien' => $id));
    }

    public function obtenerBienes_mueblesLimit($porpagina, $segmento){
      $this->db->select('a.id_bien, e.codigo_anterior, e.codigo, e.serie, f.descripcion, c.id_oficina, c.nombre_oficina,
  		d.primer_nombre, d.primer_apellido, d.id_empleado, f.precio_unitario, g.nombre_marca, f.modelo, f.id_dato_comun,
      e.numero_motor, e.numero_placa, e.matricula, e.observacion')
              ->from('(
                	select max(id_detalle_movimiento) as id_detalle_movimiento, max(id_movimiento) as id_movimiento, id_bien
                	from sic_detalle_movimiento
                	group by id_bien
                	order by id_detalle_movimiento
                	) as a')
              ->join('sic_movimiento b', 'a.id_movimiento = b.id_movimiento')
              ->join('org_oficina c', 'b.id_oficina_recibe = c.id_oficina')
              ->join('sir_empleado d', 'd.id_empleado = b.id_empleado')
              ->join('sic_bien e', 'e.id_bien = a.id_bien')
              ->join('sic_datos_comunes f', 'f.id_dato_comun = e.id_dato_comun')
              ->join('sic_marcas g', 'g.id_marca = f.id_marca')
              ->order_by('e.id_bien','desc')
              ->limit($porpagina,$segmento)
              ->where('e.terreno_zona=',NULL)
              ->where('e.tipo_inmueble=',NULL);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function totalBienes_muebles(){
      $this->db->select('count(e.id_bien) as total')
              ->from('(
                	select max(id_detalle_movimiento) as id_detalle_movimiento, max(id_movimiento) as id_movimiento, id_bien
                	from sic_detalle_movimiento
                	group by id_bien
                	order by id_detalle_movimiento
                	) as a')
              ->join('sic_bien e', 'e.id_bien = a.id_bien')
              ->where('e.terreno_zona=',NULL)
              ->where('e.tipo_inmueble=',NULL);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerCorrelativo($id){
      $this->db->max('sic_bien','correlativo');
    }

    public function obtenerOficina($id){
      $this->db->where('id_oficina',$id);
      $query = $this->db->get('org_oficina');
      if ($query->num_rows() > 0) {
        $nombre;
        foreach ($query->result() as $ofi) {
          $nombre = $ofi->nombre_oficina;
        }
        return  $nombre;
      }
      else {
          return FALSE;
      }
    }

    public function buscarOficinas($busca){
      $this->db->select('o.id_oficina,o.nombre_oficina,s.nombre_seccion,a.nombre_almacen')
               ->from('org_oficina o')
               ->join('org_seccion_has_almacen sha','o.id_seccion_has_almacen=sha.id_seccion_has_almacen')
               ->join('org_seccion s','s.id_seccion=sha.id_seccion')
               ->join('org_almacen a','a.id_almacen=sha.id_almacen')
               ->like('o.nombre_oficina',$busca)
               ->or_like('o.id_oficina',$busca);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->result();
      }else{
        return FALSE;
      }
    }

    public function obtenerOficinas(){
      $this->db->select('o.id_oficina,o.nombre_oficina,s.nombre_seccion,a.nombre_almacen')
               ->from('org_oficina o')
               ->join('org_seccion_has_almacen sha','o.id_seccion_has_almacen=sha.id_seccion_has_almacen')
               ->join('org_seccion s','s.id_seccion=sha.id_seccion')
               ->join('org_almacen a','a.id_almacen=sha.id_almacen');
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->result();
      }else{
        return FALSE;
      }
    }

    public function obtenerEmpleado($id){
      $this->db->where('id_empleado',$id);
      $query = $this->db->get('sir_empleado');
      if ($query->num_rows() > 0) {
        return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEmpleados(){
      $this->db->select('e.id_empleado,u.nombre_completo')
               ->from('sir_empleado e')
               ->join('org_usuario u','e.nr=u.nr')
               ->order_by('e.id_empleado','asc')
               ->where('e.id_estado',1);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarEmpleados($busca){
      $this->db->select('e.id_empleado,u.nombre_completo')
               ->from('sir_empleado e')
               ->join('org_usuario u','e.nr=u.nr')
               ->order_by('e.id_empleado','asc')
               ->like('u.nombre_completo',$busca)
               ->where('e.id_estado',1);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function contieneDetalleMovimiento($id){
      $this->db->select('count(id_bien) as asociados')
               ->from('sic_detalle_movimiento')
               ->where('id_bien',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }
}
?>
