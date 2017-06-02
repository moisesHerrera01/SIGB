<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Menu_dinamico.php
 */
class Menu_dinamico {

  private $ci;

  function __construct() {
    $this->ci =& get_instance();
  }

  public function build_menu_vertical($user, $segment) {
    if (is_array($user) && !empty($user) && !empty($segment)) {
      $query = $this->obtenerMenu(0, $user);

      $html_out = "";
      $submenu = array();
      $j=0;
      if ($query->num_rows() > 0) {
        $html_out .= "<nav>";
          $html_out .= "<ul class='cbp-vimenu'>";
            $html_out .= '<li><a href="#" class="icon-logo pull"></a></li>';
            $html_out .= '<li><a href="'.base_url("/index.php/dashboard").'" class="icon-home"><span class="cbp-vimenu-oc">Home</span></a></li>';
            foreach ($query->result() as $opmenu) {
              $j++;
              $url = base_url("/index.php") . "/" . $opmenu->url_modulo;
              $clase = "";
              /*SUBMENU*/
              /*Consulta para sub menu*/
              $query1 = $this->obtenerMenu($opmenu->id_modulo, $user);
              /*construye el submenu*/
              if ($query1->num_rows() > 0) {
                $submenu_out = "";
                $submenu_out .= "<ul class='cbp-submenu submenu-".$j."'>";
                $submenu_out .= '<li><a href="#" class="icon-logo pull"></a></li>';
                $submenu_out .= '<li><a href="#" class="icon-undo2 undo"><span class="cbp-vimenu-oc">Retroceder</span></a></li>';
                $k = 0;
                foreach ($query1->result() as $opsubmenu) {
                  $k++;
                  $url1 = base_url("/index.php") . "/" . $opsubmenu->url_modulo;
                  $clase1 = "";
                  $query2 = $this->obtenerMenu($opsubmenu->id_modulo, $user);

                  if ($query2->num_rows() > 0) {
                    $subsubmenu_out = "";
                    $subsubmenu_out .= "<ul class='cbp-submenu submenu-".$j."-".$k."'>";
                    $subsubmenu_out .= '<li><a href="#" class="icon-logo pull"></a></li>';
                    $subsubmenu_out .= '<li><a href="#" class="icon-undo2 undo"><span class="cbp-vimenu-oc">Retroceder</span></a></li>';
                    foreach ($query2->result() as $opsubsubmenu) {
                      $subsubmenu_out .= "<li>";
                        $subsubmenu_out .= "<a href='". base_url("/index.php") . "/" . $opsubsubmenu->url_modulo ."' class='".$opsubsubmenu->img_modulo."'>";
                          $subsubmenu_out .= "<span class='cbp-vimenu-oc'>".$opsubsubmenu->nombre_modulo."</span>";
                        $subsubmenu_out .= "</a>";
                      $subsubmenu_out .= "</li>";
                    }
                    $subsubmenu_out .= "</ul>";
                    array_push($submenu, $subsubmenu_out);
                    $clase1 = "content-subsubmenu";
                    $url1 = "#";
                  }

                  $submenu_out .= "<li class='".$clase1."'>";
                    $submenu_out .= "<a href='". $url1 ."' class='".$opsubmenu->img_modulo."'>";
                      $submenu_out .= "<span class='cbp-vimenu-oc'>".$opsubmenu->nombre_modulo."</span>";
                    $submenu_out .= "</a>";
                  $submenu_out .= "</li>";
                }
                $submenu_out .= "</ul>";
                array_push($submenu, $submenu_out);
                $clase = "content-submenu";
                $url = "#";
              }
              /*TERMINA SUBMENU*/

              $html_out .= "<li class='".$clase."'>";
                $html_out .= "<a href='". $url ."' class='".$opmenu->img_modulo."'>";
                  $html_out .= "<span class='cbp-vimenu-oc'>".$opmenu->nombre_modulo."</span>";
                $html_out .= "</a>";
              $html_out .= "</li>";
            }
          $html_out .= "</ul>";

          /*imprimir submenus*/
          for ($i=0; $i < count($submenu); $i++) {
            $html_out .= $submenu[$i];
          }
        $html_out .= "</nav>";
        $html_out .= "<a href='#' id='pull' class='icon-logo pull'></a>";
        return $html_out;
      } else {
        # code...
      }

    } else {

    }

  }

  public function build_menu_horizontal($user) {
    if (is_array($user) && !empty($user)){
      $html_out = "<nav>";
        $html_out .= "<ul class='hbp-vimenu'>";
          $html_out .= "<li>";
            $html_out .= "<span><span><img src='".base_url("assets/image/sicbaf_logo.png")."' width='32px' /></span>SICBAF</span>";
          $html_out .= "</li>";
          // $html_out .= "<li>";
          //   $html_out .= "<span class='icono icon-user'></span><span id='usuario_sistema'>&nbsp;" . $user['nombre_completo'] . "</span>";
          // $html_out .= "</li>";
          // $html_out .= "<li>";
          //   $html_out .= "<a id='notice' class='icono icon-bell'><span class='badge'>0</span></a>";
          // $html_out .= "</li>";
          $html_out .= "<li>";
            $html_out .= "<a href=".base_url("index.php/login/logout")." class='icono icon-exit'></a>";
          $html_out .= "</li>";
        $html_out .= "</ul>";
      $html_out .= "</nav>";

      $html_out .= "<div class='content-area-notice'>";
        $html_out .= "<div class='name'>Notificaciones</div>";
      $html_out .= "</div>";

      return $html_out;
    }
  }

  public function menus($p1,$p2){
    $menu = $this->build_menu_vertical($p1, $p2).$this->build_menu_horizontal($p1);
    return $menu;
  }

  public function obtenerMenu($dependencia, $user) {
    $this->ci->db->select("a.id_modulo, a.nombre_modulo, a.url_modulo, a.img_modulo")
         ->from("org_modulo a")
         ->join("org_rol_modulo_permiso b", "a.id_modulo = b.id_modulo")
         ->join("org_rol c", "b.id_rol = c.id_rol")
         ->join("org_usuario d", "d.id_usuario = ".$user['id'])
         ->join("org_usuario_rol e", "d.id_usuario = e.id_usuario")
         ->where("c.id_rol = e.id_rol")
         ->where("a.id_sistema = 14")
         ->where("a.dependencia", $dependencia)
         ->where("a.img_modulo !='' ")
         ->order_by("orden", "asc")
         ->group_by('a.id_modulo');
    return $this->ci->db->get();
  }
}
?>
