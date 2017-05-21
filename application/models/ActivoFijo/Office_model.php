<?php
  class Office_model extends CI_Model{

    public $version_office;

    function __construct() {
        parent::__construct();

    }

    public function insertarOffice($data){
        $this->version_office = $data['version_office'];
        $this->db->insert('sic_office', $this);
        return $this->db->insert_id();
    }

    public function obtenerOffices(){
      $this->db->order_by("id_office", "asc");
      $query = $this->db->get('sic_office');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarOffices($busca){
      $this->db->like('version_office', $busca);
      $query = $this->db->get('sic_office', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarOffice($id, $data){
      $this->db->where('id_office',$id);
      $this->db->update('sic_office', $data);
    }

    public function eliminarOffice($id){
      $this->db->delete('sic_office', array('id_office' => $id));
    }

    function totalOffices(){
      return $this->db->count_all('sic_office');
    }

    public function obtenerOfficesLimit($porpagina, $segmento){
      $this->db->order_by("id_office", "asc");
      $query = $this->db->get('sic_office', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function obtenerEquipoOfficeLimit($office, $minFecha, $maxFecha, $porpagina, $segmento) {

      $this->db->select('c.id_equipo_informatico , b.descripcion, c.tipo_computadora, d.nombre_marca,
       o.version_office,a.codigo')
               ->from('sic_bien a')
               ->join('sic_datos_comunes b', 'a.id_dato_comun = b.id_dato_comun')
               ->join('sic_equipo_informatico c', 'a.id_bien = c.id_bien')
               ->join('sic_detalle_equipo_informatico de','de.id_equipo_informatico=c.id_equipo_informatico')
               ->join('sic_marcas d', 'b.id_marca = d.id_marca')
               ->join('sic_office o','o.id_office=de.id_office')
               ->where('o.id_office', $office)
               ->where("b.fecha_adquisicion BETWEEN '$minFecha' AND '$maxFecha'")
               ->limit($porpagina, $segmento);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          $bienes = array();

          foreach ($query->result() as $bien) {

            $bien_aux = array(
              'id_equipo_informatico' => $bien->id_equipo_informatico,
              'descripcion' => $bien->descripcion,
              'tipo_computadora' => $bien->tipo_computadora,
              'nombre_marca' => $bien->nombre_marca,
              'version_office' => $bien->version_office,
              'codigo' => $bien->codigo
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

            $this->db->select('c.version_sistema_operativo')
                     ->from('sic_equipo_informatico a')
                     ->join('sic_detalle_equipo_informatico b', 'a.id_equipo_informatico = b.id_equipo_informatico')
                     ->join('sic_sistema_operativo c', 'b.id_sistema_operativo = c.id_sistema_operativo')
                     ->where('a.id_equipo_informatico', $bien->id_equipo_informatico);

            $query2 = $this->db->get();

            if ($query2->num_rows() > 0) {
              $pro = $query2->row();
              $bien_aux['version_sistema_operativo'] = $pro->version_sistema_operativo;
            } else {
              $bien_aux['version_sistema_operativo'] = '';
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

    public function totalEquipoOffice($office, $minFecha, $maxFecha) {

      $this->db->select('COUNT(*) as total')
               ->from('sic_bien a')
               ->join('sic_datos_comunes b', 'a.id_dato_comun = b.id_dato_comun')
               ->join('sic_equipo_informatico c', 'a.id_bien = c.id_bien')
               ->join('sic_detalle_equipo_informatico de','de.id_equipo_informatico=c.id_equipo_informatico')
               ->join('sic_office o','o.id_office=de.id_office')
               ->join('sic_marcas d', 'b.id_marca = d.id_marca')
               ->where('de.id_office', $office)
               ->where("b.fecha_adquisicion BETWEEN '$minFecha' AND '$maxFecha'");

      $query = $this->db->get();
      if ($query->num_rows() > 0){
        return $query->row();
      } else {
        return 0;
      }
    }

  }
?>
