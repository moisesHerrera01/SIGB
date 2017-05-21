<?php
  class Equipo_informatico_model extends CI_Model{

    public $id_equipo_informatico;
    public $id_subcategoria;
    public $id_procesador;
    public $id_disco_duro;
    public $id_memoria;
    public $id_sistema_operativo;
    public $id_office;
    public $velocidad_procesador;
    public $velocidad_disco_duro;
    public $velocidad_memoria;
    public $clave_sistema_operativo;
    public $clave_office;
    public $direccion_ip;
    public $numero_de_punto;

    function __construct() {
        parent::__construct();
    }

    public function insertarEquipoInformatico($data){
        $this->id_equipo_informatico = $data['id_equipo_informatico'];
        $this->id_subcategoria = $data['id_subcategoria'];
        $this->id_procesador = $data['id_procesador'];
        $this->id_disco_duro = $data['id_disco_duro'];
        $this->id_memoria = $data['id_memoria'];
        $this->id_sistema_operativo = $data['id_sistema_operativo'];
        $this->id_office = $data['id_office'];
        $this->velocidad_procesador = $data['velocidad_procesador'];
        $this->velocidad_disco_duro = $data['velocidad_disco_duro'];
        $this->velocidad_memoria = $data['velocidad_memoria'];
        $this->clave_sistema_operativo = $data['clave_sistema_operativo'];
        $this->clave_office = $data['clave_office'];
        $this->direccion_ip = $data['direccion_ip'];
        $this->numero_de_punto = $data['numero_de_punto'];

        $this->db->insert('sic_detalle_equipo_informatico', $this);
        return $this->db->insert_id();
    }

    public function obtenerEquipoInformaticoLimit($porpagina, $segmento,$id_subcategoria,$id_equipo_informatico){
            $nombre_subcategoria=$this->obtenerCategoria($id_subcategoria)->nombre_subcategoria;
          if ($nombre_subcategoria=='DISCO DURO') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,dd.id_disco_duro,dd.capacidad, de.velocidad_disco_duro,e.id_bien,
            de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_disco_duro dd','dd.id_disco_duro=de.id_disco_duro');
          }elseif ($nombre_subcategoria=='PROCESADOR') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,pro.id_procesador,pro.nombre_procesador,de.velocidad_procesador,
            e.id_bien,de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_procesador pro','pro.id_procesador=de.id_procesador');
          }elseif ($nombre_subcategoria=='MEMORIA') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,m.id_memoria,m.tipo_memoria,de.velocidad_memoria,e.id_bien,
            de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_memoria m','m.id_memoria=de.id_memoria');
          }elseif ($nombre_subcategoria=='SISTEMA OPERATIVO') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,sis.id_sistema_operativo,sis.version_sistema_operativo,
            de.clave_sistema_operativo,e.id_bien,de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_sistema_operativo sis','sis.id_sistema_operativo=de.id_sistema_operativo');
          }elseif ($nombre_subcategoria=='OFFICE') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,of.id_office,of.version_office,de.clave_office,e.id_bien,
            de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_office of','of.id_office=de.id_office');
          }elseif ($nombre_subcategoria=='IP') {
          $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
          d.id_dato_comun,d.descripcion,de.direccion_ip,e.id_bien,de.id_detalle_equipo_informatico')
                   ->from('sic_equipo_informatico e')
                   ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                   ->join('sic_bien b','b.id_bien=e.id_bien')
                   ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                   ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                   ->join('sic_categoria c','c.id_categoria=s.id_categoria');
        }elseif ($nombre_subcategoria=='PUNTO DE RED') {
        $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
        d.id_dato_comun,d.descripcion,de.numero_de_punto,e.id_bien,de.id_detalle_equipo_informatico')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria');
        }
               $this->db->where('de.id_subcategoria',$id_subcategoria)
                        ->where('e.id_equipo_informatico',$id_equipo_informatico)
                        ->limit($porpagina, $segmento);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarEquipoInformatico($busca,$id_subcategoria,$id_equipo_informatico){
      $nombre_subcategoria=$this->obtenerCategoria($id_subcategoria)->nombre_subcategoria;
          if ($nombre_subcategoria=='DISCO DURO') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,dd.id_disco_duro,dd.capacidad, de.velocidad_disco_duro,e.id_bien,
            de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_disco_duro dd','dd.id_disco_duro=de.id_disco_duro');
          }elseif ($nombre_subcategoria=='PROCESADOR') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,pro.id_procesador,pro.nombre_procesador,de.velocidad_procesador,
            e.id_bien,de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_procesador pro','pro.id_procesador=de.id_procesador');
          }elseif ($nombre_subcategoria=='MEMORIA') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,m.id_memoria,m.tipo_memoria,de.velocidad_memoria,e.id_bien,
            de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_memoria m','m.id_memoria=de.id_memoria');
          }elseif ($nombre_subcategoria=='SISTEMA OPERATIVO') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,sis.id_sistema_operativo,sis.version_sistema_operativo,
            de.clave_sistema_operativo,e.id_bien,de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_sistema_operativo sis','sis.id_sistema_operativo=de.id_sistema_operativo');
          }elseif ($nombre_subcategoria=='OFFICE') {
            $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
            d.id_dato_comun,d.descripcion,of.id_office,of.version_office,de.clave_office,e.id_bien,de.id_detalle_equipo_informatico')
                     ->from('sic_equipo_informatico e')
                     ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                     ->join('sic_bien b','b.id_bien=e.id_bien')
                     ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                     ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                     ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                     ->join('sic_office of','of.id_office=de.id_office');
          }elseif ($nombre_subcategoria=='IP') {
          $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
          d.id_dato_comun,d.descripcion,de.direccion_ip,e.id_bien,de.id_detalle_equipo_informatico')
                   ->from('sic_equipo_informatico e')
                   ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                   ->join('sic_bien b','b.id_bien=e.id_bien')
                   ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                   ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                   ->join('sic_categoria c','c.id_categoria=s.id_categoria');
        }elseif ($nombre_subcategoria=='PUNTO DE RED') {
        $this->db->select('e.id_equipo_informatico,c.nombre_categoria,s.nombre_subcategoria,
        d.id_dato_comun,d.descripcion,de.numero_de_punto,e.id_bien,de.id_detalle_equipo_informatico')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria');
      }
    $this->db->where('de.id_subcategoria',$id_subcategoria)
               ->where('e.id_equipo_informatico',$id_equipo_informatico)
               ->like('d.descripcion',$busca);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function totalEquipoInformatico($id_subcategoria,$id_equipo_informatico){
      $nombre_subcategoria=$this->obtenerCategoria($id_subcategoria)->nombre_subcategoria;
      if ($nombre_subcategoria=='DISCO DURO') {
        $this->db->select('count(*) as total')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                 ->join('sic_disco_duro dd','dd.id_disco_duro=de.id_disco_duro');
      }elseif ($nombre_subcategoria=='PROCESADOR') {
        $this->db->select('count(*) as total')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                 ->join('sic_procesador pro','pro.id_procesador=de.id_procesador');
      }elseif ($nombre_subcategoria=='MEMORIA') {
        $this->db->select('count(*) as total')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                 ->join('sic_memoria m','m.id_memoria=de.id_memoria');
      }elseif ($nombre_subcategoria=='SISTEMA OPERATIVO') {
        $this->db->select('count(*) as total')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                 ->join('sic_sistema_operativo sis','sis.id_sistema_operativo=de.id_sistema_operativo');
      }elseif ($nombre_subcategoria=='OFFICE') {
        $this->db->select('count(*) as total')
                 ->from('sic_equipo_informatico e')
                 ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
                 ->join('sic_bien b','b.id_bien=e.id_bien')
                 ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
                 ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
                 ->join('sic_categoria c','c.id_categoria=s.id_categoria')
                 ->join('sic_office of','of.id_office=de.id_office');
      }elseif ($nombre_subcategoria=='IP') {
      $this->db->select('count(*) as total')
               ->from('sic_equipo_informatico e')
               ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
               ->join('sic_bien b','b.id_bien=e.id_bien')
               ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
               ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
               ->join('sic_categoria c','c.id_categoria=s.id_categoria');
    }elseif ($nombre_subcategoria=='PUNTO DE RED') {
    $this->db->select('count(*) as total')
             ->from('sic_equipo_informatico e')
             ->join('sic_detalle_equipo_informatico de','e.id_equipo_informatico=de.id_equipo_informatico')
             ->join('sic_bien b','b.id_bien=e.id_bien')
             ->join('sic_datos_comunes d','d.id_dato_comun=b.id_dato_comun')
             ->join('sic_subcategoria s','s.id_subcategoria=de.id_subcategoria')
             ->join('sic_categoria c','c.id_categoria=s.id_categoria');
  }
               $this->db->where('de.id_subcategoria',$id_subcategoria)
                        ->where('e.id_equipo_informatico',$id_equipo_informatico);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->row();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarEquipoInformatico($id, $data){
      $this->db->where('id_detalle_equipo_informatico',$id);
      $this->db->update('sic_detalle_equipo_informatico', $data);
    }

    public function eliminarEquipoInformatico($id){
      $this->db->delete('sic_detalle_equipo_informatico', array('id_detalle_equipo_informatico' => $id));
    }

    public function obtenerCategoria($id_subcategoria){
      $this->db->select('*')
               ->from('sic_subcategoria s')
               ->join('sic_categoria c','s.id_categoria=c.id_categoria')
               ->where('s.id_subcategoria',$id_subcategoria);
      $query=$this->db->get();
      return $query->row();
    }

    public function obtenerDatos($id_dato_comun){
      $this->db->select('*')
               ->from('sic_subcategoria s')
               ->join('sic_categoria c','s.id_categoria=c.id_categoria')
               ->join('sic_datos_comunes dc','dc.id_subcategoria=s.id_subcategoria')
               ->where('dc.id_dato_comun',$id_dato_comun);
      $query=$this->db->get();
      return $query->row();
    }

    public function totalEquipoPorTipoComputadoraLimit($tipo, $minFecha, $maxFecha) {

      $this->db->select('COUNT(*) as total')
               ->from('sic_bien a')
               ->join('sic_datos_comunes b', 'a.id_dato_comun = b.id_dato_comun')
               ->join('sic_equipo_informatico c', 'a.id_bien = c.id_bien')
               ->join('sic_marcas d', 'b.id_marca = d.id_marca')
               ->where('c.tipo_computadora', $tipo)
               ->where("b.fecha_adquisicion BETWEEN '$minFecha' AND '$maxFecha'");

      $query = $this->db->get();
      if ($query->num_rows() > 0){
        return $query->row();
      } else {
        return 0;
      }
    }

    public function obtenerEquipoPorTipoComputadoraLimit($tipo, $minFecha, $maxFecha, $porpagina, $segmento) {

      $this->db->select('a.id_bien, c.id_equipo_informatico, b.descripcion, c.tipo_computadora, d.nombre_marca')
               ->from('sic_bien a')
               ->join('sic_datos_comunes b', 'a.id_dato_comun = b.id_dato_comun')
               ->join('sic_equipo_informatico c', 'a.id_bien = c.id_bien')
               ->join('sic_marcas d', 'b.id_marca = d.id_marca')
               ->where('c.tipo_computadora', $tipo)
               ->where("b.fecha_adquisicion BETWEEN '$minFecha' AND '$maxFecha'")
               ->limit($porpagina, $segmento);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          $bienes = array();

          foreach ($query->result() as $bien) {

            $bien_aux = array(
              'id_bien' => $bien->id_bien,
              'id_equipo_informatico' => $bien->id_equipo_informatico,
              'descripcion' => $bien->descripcion,
              'tipo_computadora' => $bien->tipo_computadora,
              'nombre_marca' => $bien->nombre_marca,
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
}
?>
