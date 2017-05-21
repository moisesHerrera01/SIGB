<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breadcrump.php
 */

 class Breadcrumb {

   private $ci;

   function __construct() {
     $this->ci =& get_instance();
   }

   public function build_breadcrump($url) {
     $url = $this->limpiar_url($url);
     $breadcrump = $this->build_array_breadcrump($url);

     $breadcrump_out = "";
     if ($breadcrump != '') {

       $breadcrump_out .= '<ol class="breadcrumb">';

       foreach ($breadcrump as $modulo) {
         $href;
         if ($modulo['url_modulo'] == '') {
           $href = "#";
         } else {
           $href = base_url("/index.php") . "/" . $modulo['url_modulo'];
         }

        $breadcrump_out .= '<li class="breadcrumb-item"><a href="'.$href.'">'.$modulo['nombre_modulo'].'</a></li>';
       }

       $breadcrump_out .= '</ol>';

     }
     return $breadcrump_out;
   }

   public function build_array_breadcrump($url) {
     $breadcrump = array();
     $modulo = $this->obtenerModulo($url);
     if ($modulo) {

       array_push($breadcrump, $modulo);
       $dependencia = (int) $modulo['dependencia'];
       while ($dependencia != 0) {
         $mod_dep = $this->obtenerDependencia($dependencia);
         array_push($breadcrump, $mod_dep);

         $dependencia = (int) $mod_dep['dependencia'];
       }
       array_push($breadcrump, array('dependencia' => 0, 'nombre_modulo' => 'Home', 'url_modulo' => 'dashboard'));
     }

     return array_reverse($breadcrump);
   }

   public function obtenerModulo($url) {
     $this->ci->db->select("a.dependencia, a.nombre_modulo, a.url_modulo")
          ->from("org_modulo a")
          ->where("a.url_modulo", $url)
          ->limit(1);
     $query = $this->ci->db->get();
     if ($query->num_rows() == 1) {
       return $query->row_array();
     } else {
       return FALSE;
     }
   }

   public function obtenerDependencia($id) {
     $this->ci->db->select("a.dependencia, a.nombre_modulo, a.url_modulo")
          ->from("org_modulo a")
          ->where("a.id_modulo", $id)
          ->limit(1);
     $query = $this->ci->db->get();
     if ($query->num_rows() == 1) {
       return $query->row_array();
     } else {
       return FALSE;
     }
   }

   public function limpiar_url($url) {
     $segments = explode("/", $url);

     $aux = array();
     for ($i=0; $i < count($segments); $i++) {
       if ($segments[$i] == 'index') {
         break;
       } else {
         array_push($aux, $segments[$i]);
       }
     }

     $urls = implode("/", $aux);
     return $urls;
   }
 }
