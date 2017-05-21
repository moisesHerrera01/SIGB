<?php
  class Doc_ampara_model extends CI_Model{

    public $nombre_doc_ampara;

    function __construct() {
        parent::__construct();
    }

    public function insertarDocumento($data){
        $this->nombre_doc_ampara = $data['nombre_doc_ampara'];
        $this->db->insert('sic_doc_ampara', $this);
        return $this->db->insert_id();
    }

    public function obtenerDocumentos(){
      $this->db->order_by("id_doc_ampara", "asc");
      $query = $this->db->get('sic_doc_ampara');
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function buscarDocumentos($busca){
      $this->db->order_by("id_doc_ampara", "desc");
      $this->db->like('nombre_doc_ampara', $busca);
      $this->db->or_like('id_doc_ampara', $busca);
      $query = $this->db->get('sic_doc_ampara', 10);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }

    public function actualizarDocumento($id, $data){
      $this->db->where('id_doc_ampara',$id);
      $this->db->update('sic_doc_ampara', $data);
    }

    public function eliminarDocumento($id){
      $this->db->delete('sic_doc_ampara', array('id_doc_ampara' => $id));
    }

    function totalDocumentos(){
      return $this->db->count_all('sic_doc_ampara');
    }

    public function obtenerDocumentosLimit($porpagina, $segmento){
      $this->db->order_by("id_doc_ampara", "desc");
      $query = $this->db->get('sic_doc_ampara', $porpagina, $segmento);
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
    }
    public function contieneDatoComun($id){
      $this->db->select('count(id_doc_ampara) as asociados')
               ->from('sic_datos_comunes')
               ->where('id_doc_ampara',$id);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else {
        return FALSE;
      }
    }
  }
?>
