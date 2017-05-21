<?php
  class Sistema_operativo_model extends CI_Model{

    public $version_sistema_operativo;

    function __construct() {
        parent::__construct();

    }

    public function insertarSistema_operativo($data){
        $this->version_sistema_operativo = $data['version_sistema_operativo'];
        $this->db->insert('sic_sistema_operativo', $this);
        return $this->db->insert_id();
    }

    public function obtenerSistemas_operativos(){
      $this->db->order_by("id_sistema_operativo", "asc");
      $query = $this->db->get('sic_sistema_operativo');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarSistemas_operativos($busca){
      $this->db->like('version_sistema_operativo', $busca);
      $query = $this->db->get('sic_sistema_operativo', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarSistema_operativo($id, $data){
      $this->db->where('id_sistema_operativo',$id);
      $this->db->update('sic_sistema_operativo', $data);
    }

    public function eliminarSistema_operativo($id){
      $this->db->delete('sic_sistema_operativo', array('id_sistema_operativo' => $id));
    }

    function totalSistemas_operativos(){
      return $this->db->count_all('sic_sistema_operativo');
    }

    public function obtenerSistemas_operativosLimit($porpagina, $segmento){
      $this->db->order_by("id_sistema_operativo", "asc");
      $query = $this->db->get('sic_sistema_operativo', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEquipoPorOSLimit($os, $minFecha, $maxFecha, $porpagina, $segmento) {
      $this->db->select('d.id_bien, e.descripcion, b.tipo_computadora, f.nombre_marca, c.version_sistema_operativo, a.clave_sistema_operativo
              , b.id_equipo_informatico')
               ->from('sic_detalle_equipo_informatico a')
               ->join('sic_equipo_informatico b', 'b.id_equipo_informatico = a.id_equipo_informatico')
               ->join('sic_sistema_operativo c', 'c.id_sistema_operativo = a.id_sistema_operativo')
               ->join('sic_bien d', 'd.id_bien = b.id_bien')
               ->join('sic_datos_comunes e', 'd.id_dato_comun = e.id_dato_comun')
               ->join('sic_marcas f', 'e.id_marca = f.id_marca')
               ->where('a.id_sistema_operativo', $os)
               ->where("e.fecha_adquisicion BETWEEN '$minFecha' AND '$maxFecha'")
               ->limit($porpagina, $segmento);

      $query = $this->db->get();
      if ($query->num_rows() > 0) {
       $bienes = array();

       foreach ($query->result() as $bien) {

         $bien_aux = array(
           'id_equipo_informatico' => $bien->id_equipo_informatico,
           'id_bien' => $bien->id_bien,
           'descripcion' => $bien->descripcion,
           'tipo_computadora' => $bien->tipo_computadora,
           'nombre_marca' => $bien->nombre_marca,
           'version_sistema_operativo' => $bien->version_sistema_operativo,
           'clave_sistema_operativo' => $bien->clave_sistema_operativo
         );

         $this->db->select('c.nombre_procesador, b.velocidad_procesador')
                  ->from('sic_equipo_informatico a')
                  ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                  ->join('sic_procesador c', 'b.id_procesador = c.id_procesador')
                  ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

         $query2 = $this->db->get();

         if ($query2->num_rows() > 0) {
           $pro = $query2->row();
           $bien_aux['nombre_procesador'] = $pro->nombre_procesador;
           $bien_aux['velocidad_procesador'] = $pro->velocidad_procesador;
         } else {
           $bien_aux['nombre_procesador'] = '';
           $bien_aux['velocidad_procesador'] = '';
         }

         $this->db->select('c.capacidad, b.velocidad_disco_duro')
                  ->from('sic_equipo_informatico a')
                  ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                  ->join('sic_disco_duro c', 'b.id_disco_duro = c.id_disco_duro')
                  ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

         $query2 = $this->db->get();

         if ($query2->num_rows() > 0) {
           $pro = $query2->row();
           $bien_aux['capacidad'] = $pro->capacidad;
           $bien_aux['velocidad_disco_duro'] = $pro->velocidad_disco_duro;
         } else {
           $bien_aux['capacidad'] = '';
           $bien_aux['velocidad_disco_duro'] = '';
         }

         $this->db->select('c.tipo_memoria, b.velocidad_memoria')
                  ->from('sic_equipo_informatico a')
                  ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                  ->join('sic_memoria c', 'b.id_memoria = c.id_memoria')
                  ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

         $query2 = $this->db->get();

         if ($query2->num_rows() > 0) {
           $pro = $query2->row();
           $bien_aux['tipo_memoria'] = $pro->tipo_memoria;
           $bien_aux['velocidad_memoria'] = $pro->velocidad_memoria;
         } else {
           $bien_aux['tipo_memoria'] = '';
           $bien_aux['velocidad_memoria'] = '';
         }

         $this->db->select('c.version_office')
                  ->from('sic_equipo_informatico a')
                  ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                  ->join('sic_office c', 'b.id_office = c.id_office')
                  ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

         $query2 = $this->db->get();

         if ($query2->num_rows() > 0) {
           $pro = $query2->row();
           $bien_aux['version_office'] = $pro->version_office;
         } else {
           $bien_aux['version_office'] = '';
         }

         $this->db->select('b.direccion_ip')
                  ->from('sic_equipo_informatico a')
                  ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                  ->where('b.direccion_ip IS NOT NULL')
                  ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

         $query2 = $this->db->get();

         if ($query2->num_rows() > 0) {
           $pro = $query2->row();
           $bien_aux['direccion_ip'] = $pro->direccion_ip;
         } else {
           $bien_aux['direccion_ip'] = '';
         }

         $this->db->select('b.numero_de_punto')
                  ->from('sic_equipo_informatico a')
                  ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                  ->where('b.numero_de_punto IS NOT NULL')
                  ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

         $query2 = $this->db->get();

         if ($query2->num_rows() > 0) {
           $pro = $query2->row();
           $bien_aux['numero_de_punto'] = $pro->numero_de_punto;
         } else {
           $bien_aux['numero_de_punto'] = '';
         }

         $bienes[] = $bien_aux;

       }

       return $bienes;

      } else {
       return false;
      }
    }

    public function totalObtenerEquipoPorOSLimit($os, $minFecha, $maxFecha) {
      $this->db->select('COUNT(*) as total')
               ->from('sic_detalle_equipo_informatico a')
               ->join('sic_equipo_informatico b', 'b.id_equipo_informatico = a.id_equipo_informatico')
               ->join('sic_sistema_operativo c', 'c.id_sistema_operativo = a.id_sistema_operativo')
               ->join('sic_bien d', 'd.id_bien = b.id_bien')
               ->join('sic_datos_comunes e', 'd.id_dato_comun = e.id_dato_comun')
               ->join('sic_marcas f', 'e.id_marca = f.id_marca')
               ->where('a.id_sistema_operativo', $os)
               ->where("e.fecha_adquisicion BETWEEN '$minFecha' AND '$maxFecha'");

       $query = $this->db->get();
       if ($query->num_rows() > 0){
         return $query->row();
       } else {
         return 0;
       }
    }

  }
?>
