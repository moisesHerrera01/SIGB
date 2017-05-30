<?php
  class Bienes_inmuebles_model extends CI_Model{

    public $id_dato_comun;
    public $codigo_anterior;
    public $tipo_inmueble;
    public $terreno_extension;
    public $matricula;
    public $terreno_direccion;
    public $terreno_zona;
    public $id_condicion_bien;
    public $terreno_fines;
    public $terreno_precio_adquisicion;
    public $observacion;
    public $correlativo;
    public $codigo;
    public $id_oficina;
    public $id_empleado;

    function __construct() {
        parent::__construct();
    }

    public function obtenerCorrelativo(){
      $this->db->select('max(correlativo) as cor')
               ->from('sic_bien');
      $query = $this->db->get();
      $cor;
          foreach ($query->result() as $var) {
              $cor=$var->cor+1;
          }
      return $cor;
    }

    public function calcularCodigo($id){
      $this->db->select('s.numero_subcategoria,c.numero_categoria')
           ->from('sic_datos_comunes d')
           ->join('sic_subcategoria s','s.id_subcategoria=d.id_subcategoria')
           ->join('sic_categoria c','c.id_categoria=s.id_categoria')
           ->where('d.id_dato_comun',$id);
      $query = $this->db->get();
      if($query->num_rows() > 0 )
        {
           return $query->row();
        }
    }

    public function insertarBienesInmuebles($data){
        $this->id_dato_comun = $data['id_dato_comun'];
        $this->codigo_anterior = $data['codigo_anterior'];
        $this->tipo_inmueble = $data['tipo_inmueble'];
        $this->terreno_extension = $data['terreno_extension'];
        $this->matricula = $data['matricula'];
        $this->terreno_direccion = $data['terreno_direccion'];
        $this->terreno_zona = $data['terreno_zona'];
        $this->id_condicion_bien = $data['id_condicion_bien'];
        $this->terreno_fines = $data['terreno_fines'];
        $this->terreno_precio_adquisicion = $data['terreno_precio_adquisicion'];
        $this->observacion = $data['observacion'];
        $this->correlativo=$this->obtenerCorrelativo();
        $this->codigo=$data['codigo'];
        $this->db->insert('sic_bien', $this);
        return $this->db->insert_id();
    }

    public function obtenerBienesInmuebles(){
       $this->db->select('b.id_bien,b.id_condicion_bien,b.tipo_inmueble,b.terreno_extension,b.terreno_zona,b.terreno_direccion,
       c.nombre_condicion_bien,b.id_dato_comun,d.descripcion,b.codigo_anterior,b.matricula,b.terreno_fines,
       b.terreno_precio_adquisicion,b.observacion,b.correlativo,b.codigo')
                ->from('sic_bien b')
                ->join('sic_datos_comunes d', 'd.id_dato_comun = b.id_dato_comun')
                ->join('sic_condicion_bien c', 'c.id_condicion_bien = b.id_condicion_bien')
                ->order_by('b.id_bien');
     $query = $this->db->get();
       if($query->num_rows() > 0 )
       {
           return $query->result();
       }
   }

   public function buscarBienesInmuebles($busca){
      $this->db->select('b.id_bien,b.id_condicion_bien,b.tipo_inmueble,b.terreno_extension,b.terreno_zona,b.terreno_direccion,
      c.nombre_condicion_bien,b.id_dato_comun,d.descripcion,b.codigo_anterior,b.matricula,b.terreno_fines,
      b.terreno_precio_adquisicion,b.observacion,b.correlativo,b.codigo')
               ->from('sic_bien b')
               ->join('sic_datos_comunes d', 'd.id_dato_comun = b.id_dato_comun')
               ->join('sic_condicion_bien c', 'c.id_condicion_bien = b.id_condicion_bien')
               ->order_by('b.id_bien','desc')
               ->like('b.codigo', $busca)
               ->or_like('d.descripcion',$busca)
               ->where('b.terreno_zona<>',NULL)
               ->where('b.tipo_inmueble<>',NULL);
    $query = $this->db->get();
      if($query->num_rows() > 0 )
      {
          return $query->result();
      }
  }

    public function actualizarBienesInmuebles($id, $data){
      $this->db->where('id_bien',$id);
      $this->db->update('sic_bien', $data);
    }

    public function eliminarBienesInmuebles($id){
      $this->db->delete('sic_bien', array('id_bien' => $id));
    }

   public function obtenerBienesInmueblesLimit($porpagina,$segmento){
      $this->db->select('b.id_bien,b.id_condicion_bien,b.tipo_inmueble,b.terreno_extension,b.terreno_zona,b.terreno_direccion,
      c.nombre_condicion_bien,b.id_dato_comun,d.descripcion,b.codigo_anterior,b.matricula,b.terreno_fines,
      b.terreno_precio_adquisicion,b.observacion,b.correlativo,b.codigo')
               ->from('sic_bien b')
               ->join('sic_datos_comunes d', 'd.id_dato_comun = b.id_dato_comun')
               ->join('sic_condicion_bien c', 'c.id_condicion_bien = b.id_condicion_bien')
               ->order_by('b.id_bien','desc')
               ->where('b.terreno_zona<>',NULL)
               ->where('b.tipo_inmueble<>',NULL)
               ->limit($porpagina, $segmento);
    $query = $this->db->get();
      if($query->num_rows() > 0 )
      {
          return $query->result();
      }
  }

  public function totalBienesInmuebles(){
     $this->db->select('count(id_bien) as total')
              ->from('sic_bien b')
              ->join('sic_datos_comunes d', 'd.id_dato_comun = b.id_dato_comun')
              ->join('sic_condicion_bien c', 'c.id_condicion_bien = b.id_condicion_bien')
              ->order_by('b.id_bien')
              ->where('b.terreno_zona<>',NULL)
              ->where('b.tipo_inmueble<>',NULL);
   $query = $this->db->get();
     if($query->num_rows() > 0 )
     {
         return $query->row();
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
