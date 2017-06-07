<?php
  class Proveedor extends CI_Model{

    public $nombre_proveedor;
    public $nit;
    public $correo;
    public $telefono;
    public $fax;
    public $direccion;
    public $id_categoria_proveedor;
    public $nombre_contacto;

    function __construct() {
        parent::__construct();
    }

    public function insertarProveedor($data){
        $this->id_categoria_proveedor =$data['id_categoria_proveedor'];
        $this->nombre_proveedor = $data['nombre_proveedor'];
        $this->nombre_contacto = $data['nombre_contacto'];
        $this->nit = $data['nit'];
        $this->correo = $data['correo'];
        $this->telefono = $data['telefono'];
        $this->fax = $data['fax'];
        $this->direccion = $data['direccion'];

        $this->db->insert('sic_proveedores', $this);
    }

    public function obtenerProveedores(){
      $this->db->select('*')
                ->from('sic_proveedores p')
                ->join('sic_categoria_proveedor c','c.id_categoria_proveedor=p.id_categoria_proveedor')
                ->order_by('p.id_proveedores');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerProveedor($id){
        $this->db->where('id_proveedores',$id);
        $query = $this->db->get('sic_proveedores');
        if ($query->num_rows() > 0) {
          $nombre;
          foreach ($query->result() as $prov) {
            $nombre = $prov->nombre_proveedor;
          }
          return  $nombre;
        }
        else {
            return FALSE;
        }
    }

  /*  public function obtenerProveedoresLimit($porpagina, $segmento){
      $this->db->order_by("id_proveedores", "asc");
      $query = $this->db->get('sic_proveedores', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }*/

    public function obtenerProveedoresLimit($porpagina, $segmento){
      $this->db->select('*')
                ->from('sic_proveedores p')
                ->join('sic_categoria_proveedor c','c.id_categoria_proveedor=p.id_categoria_proveedor')
                ->order_by('p.id_proveedores')
                ->limit($porpagina,$segmento);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    function totalProveedores(){
      $this->db->select("count(id_proveedores) as total")
                ->from('sic_proveedores p')
                ->join('sic_categoria_proveedor c','c.id_categoria_proveedor=p.id_categoria_proveedor');
      $query=$this->db->get();
      return $query->row();
      }

    public function buscarProveedores($busca){
      $this->db->select('p.id_proveedores,p.nombre_proveedor,p.nit,correo,p.telefono,p.direccion,p.id_categoria_proveedor,
      p.descripcion,p.fax,p.nombre_contacto,c.nombre_categoria,c.rubro,c.tipo_empresa')
                ->from('sic_proveedores p')
                ->join('sic_categoria_proveedor c','c.id_categoria_proveedor=p.id_categoria_proveedor')
                ->order_by("p.id_proveedores", "asc")
                ->like('p.nombre_proveedor', $busca)
                ->or_like('p.nit',$busca);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerDatoProveedor($id){
      $this->db->select('*')
                ->from('sic_proveedores p')
                ->join('sic_categoria_proveedor c','c.id_categoria_proveedor=p.id_categoria_proveedor')
                ->order_by("p.id_proveedores", "asc")
                ->where('p.id_proveedores',$id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarProveedor($id, $data){
      $this->db->where('id_proveedores',$id);
      $this->db->update('sic_proveedores', $data);
    }

    public function eliminarProveedor($id){
      $this->db->delete('sic_proveedores', array('id_proveedores' => $id));
    }

    public function ReporteProveedores( $minFecha, $maxFecha,$id_fuente, $porpagina, $segmento) {
      $tiempo_inicio = microtime(true);
      $this->db->select('a.id_factura, a.fecha_factura, a.numero_factura, a.numero_compromiso, b.nombre_proveedor, c.id_detalleproducto,
                        d.id_especifico, SUM(c.total) AS "total"')
               ->from('sic_factura a')
               ->join('sic_proveedores b', 'a.id_proveedores = b.id_proveedores')
               ->join('sic_detalle_factura c', 'c.id_factura = a.id_factura')
               ->join('sic_detalle_producto d', 'd.id_detalleproducto = c.id_detalleproducto')
               ->where('a.id_fuentes', $id_fuente)
                ->where("a.fecha_factura BETWEEN '$minFecha' AND '$maxFecha'")
               ->group_by(array('a.id_factura', 'd.id_especifico'))
               ->order_by('a.fecha_factura ASC')
               ->order_by('a.id_factura ASC')
               ->limit($porpagina, $segmento);
       $query = $this->db->get();
       if ($query->num_rows() > 0) {
           return  $query->result_array();
       }
       else {
           return FALSE;
       }

       $tiempo_fin = microtime(true);
       echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);
    }


    public function TotalReporteProveedores( $minFecha, $maxFecha,$id_fuente ){
      $this->db->select('id_proveedores')
               ->from('sic_factura');
      $query=$this->db->get();
      $facts=$query->result();
      $this->db->select('count(*) AS cuenta')
               ->from('sic_factura a');
      $this->db->join('sic_proveedores b', 'a.id_proveedores = b.id_proveedores');
      $this->db->join('sic_detalle_factura c', 'c.id_factura = a.id_factura')
               ->join('sic_detalle_producto d', 'd.id_detalleproducto = c.id_detalleproducto')
               ->where('a.id_fuentes', $id_fuente)
               ->where("a.fecha_factura BETWEEN '$minFecha' AND '$maxFecha'")
               ->group_by(array('a.id_factura', 'd.id_especifico'))
               ->order_by('a.fecha_factura ASC')
               ->order_by('a.id_factura ASC');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return $query->num_rows();
      }
      else {
          return 0;
      }
    }
  }
?>
