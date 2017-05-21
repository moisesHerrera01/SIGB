<?php
  class User_model extends CI_Model{

    public $id_usuario;
    public $id_modulo;
    public $id_registro;
    public $operacion;
    public $fecha;
    public $hora;

    function __construct() {
        parent::__construct();
    }

    public function login($data){
      $this->db->select('*')
               ->from('org_usuario u')
               ->where('usuario', $data['username'])
               ->where('password', MD5($data['password']));
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
        return TRUE;
      } else {
        return FALSE;
      }
    }

    public function dataUser($username){
    $this->db->select('max(ei.fecha_inicio) as fecha_inicio')
               ->from('org_usuario u')
               ->join('sir_empleado e','e.nr=u.nr')
               ->join('sir_empleado_informacion_laboral ei','ei.id_empleado=e.id_empleado')
               ->where('u.usuario',$username);
      $query_0=$this->db->get();
      $info=$query_0->row();

      $this->db->select('u.id_usuario,u.nombre_completo,u.usuario,s.id_seccion,u.estado,r.id_rol,r.nombre_rol, CONCAT(e.primer_nombre,
      " ", e.segundo_nombre) as nombre_empleado,e.id_empleado,cf.funcional as cargo_funcional,l.linea_trabajo')
               ->from('org_usuario u')
               ->join('org_usuario_rol ur','ur.id_usuario=u.id_usuario')
               ->join('org_rol r','r.id_rol=ur.id_rol')
               ->join('org_rol_modulo_permiso b', 'r.id_rol = b.id_rol')
               ->join('org_modulo c', 'b.id_modulo = c.id_modulo')
               ->join('sir_empleado e','u.nr=e.nr')
               ->join('sir_empleado_informacion_laboral i','i.id_empleado=e.id_empleado')
               ->join('sir_cargo_funcional cf','cf.id_cargo_funcional=i.id_cargo_funcional')
               ->join('org_linea_trabajo l','l.id_linea_trabajo=i.id_linea_trabajo')
               ->join('org_seccion s','i.id_seccion=s.id_seccion')
               ->where('usuario', $username)
               ->where('i.fecha_inicio',$info->fecha_inicio)
               ->where('c.id_sistema = 14');
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
        return $query->row();
      } else {
        return FALSE;
      }
    }


    public function validarAccesoCrud($id_modulo, $id_usuario, $opcion_crud) {
      $id_modulo = intval($id_modulo);
      $id_usuario = intval($id_usuario);
      $opcion_crud = mb_strtolower($opcion_crud, 'UTF-8');
      $id_permiso = 0;

      switch ($opcion_crud) {
        case 'insert':
          $id_permiso = 2;
          break;
        case 'select':
          $id_permiso = 1;
          break;
        case 'delete':
          $id_permiso = 3;
          break;
        case 'update':
          $id_permiso = 4;
          break;
        default:
          $id_permiso = 0;
          break;
      }

      if ($id_modulo == 0 || $id_usuario == 0 || $id_permiso == 0) {
        return FALSE;
      } else {
        $this->db->select('count(*) AS num')
             ->from('org_modulo a')
             ->join('org_rol_modulo_permiso b', 'a.id_modulo = b.id_modulo')
             ->join('org_rol c', 'b.id_rol = c.id_rol')
             ->join('org_usuario d', 'd.id_usuario = '.$id_usuario )
             ->join('org_usuario_rol e', 'd.id_usuario = e.id_usuario')
             ->where('a.id_sistema', 14)
             ->where('a.id_modulo', $id_modulo)
             ->where('c.id_rol = e.id_rol')
             ->where('b.id_permiso', $id_permiso);
        $query = $this->db->get();
        if ($query->row('num') == 1) {
          return TRUE;
        } else {
          return FALSE;
        }
      }

    }

    public function obtenerModulo($url){
      $this->db->select('id_modulo')
               ->from('org_modulo')
               ->where('url_modulo',$url);
      $query=$this->db->get();
      $cor;
          foreach ($query->result() as $var) {
              $cor=$var->id_modulo;
          }
      return $cor;
    }

    public function obtenerModuloNombre($id){
      $this->db->select('url_modulo')
               ->from('org_modulo')
               ->where('id_modulo',$id);
      $query=$this->db->get();
      $cor;
          foreach ($query->result() as $var) {
              $cor=$var->url_modulo;
          }
      return $cor;
    }

    public function obtenerCorreoUsuario($rol, $seccion) {
      $this->db->select("a.id_usuario, a.usuario, d.correo")
           ->from("org_usuario a")
           ->join("org_usuario_rol b", "a.id_usuario = b.id_usuario")
           ->join("org_rol c", "b.id_rol = c.id_rol")
           ->join("sir_empleado d", "a.nr = d.nr")
           ->join("sir_empleado_informacion_laboral e", "d.id_empleado = e.id_empleado")
           ->where("c.id_rol", $rol)
           ->where("e.id_seccion", $seccion)
           ->order_by('e.fecha_inicio', 'desc')
           ->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->row();
      } else {
        return FALSE;
      }
    }

    public function obtenerUsuario($id) {
      $this->db->select("*")
           ->from("org_usuario a")
           ->join("sir_empleado b", "a.nr = b.nr")
           ->where("a.id_usuario", $id);
       $query = $this->db->get();
       if ($query->num_rows() > 0) {
         return $query->row();
       } else {
         return FALSE;
       }
    }

    public function obtenerUsuarioPorEmpleado($id) {
      $this->db->select("a.id_usuario, a.usuario, d.correo")
           ->from("org_usuario a")
           ->join("org_usuario_rol b", "a.id_usuario = b.id_usuario")
           ->join("org_rol c", "b.id_rol = c.id_rol")
           ->join("sir_empleado d", "a.nr = d.nr")
           ->where("d.id_empleado", $id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->row();
      } else {
        return FALSE;
      }
    }

    public function obtenerRolesSistema() {
      $this->db->select("a.id_rol")
           ->from("org_rol a")
           ->join("org_rol_modulo_permiso b", "a.id_rol = b.id_rol")
           ->join("org_modulo c", "b.id_modulo = c.id_modulo")
           ->where("c.id_sistema = 14")
           ->group_by("a.id_rol");
       $query = $this->db->get();
       if ($query->num_rows() > 0) {
         return $query->result_array();
       } else {
         return FALSE;
       }
    }

    public function insertarRastreabilidad($data){
      $this->id_usuario=$data['id_usuario'];
      $this->id_modulo=$data['id_modulo'];
      $this->id_registro=$data['id_registro'];
      $this->operacion=$data['operacion'];
      $this->fecha=$data['fecha'];
      $this->hora=$data['hora'];

      $this->db->insert('sic_rastreabilidad',$this);
    }

    public function obtenerSiguienteIdModuloIncrement($tabla){
     $this->db->select('DATABASE() as nombre');
     $query=$this->db->get();
     $base=$query->row()->nombre;
     $this->db->select('AUTO_INCREMENT as var');
     $this->db->where('TABLE_SCHEMA',$base);
     $this->db->where('TABLE_NAME',$tabla);
     $query = $this->db->get('information_schema.TABLES');
     if ($query->num_rows() > 0) {
       $nombre;
       foreach ($query->result() as $fact) {
         $nombre = $fact->var;
       }
       return  $nombre;
     }
     else {
         return FALSE;
     }
   }

   public function obtenerUsuarioRastreabilidad($registro ,$modulo, $op) {
     $this->db->select('id_usuario')
              ->from('sic_rastreabilidad')
              ->where('id_modulo', $modulo)
              ->where('id_registro', $registro)
              ->where('operacion', $op);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else{
        return FALSE;
      }
   }

   public function obtenerRastreabilidad($registro ,$modulo, $op) {
     $this->db->select('*')
              ->from('sic_rastreabilidad')
              ->where('id_modulo', $modulo)
              ->where('id_registro', $registro)
              ->where('operacion', $op);
      $query=$this->db->get();
      if ($query->num_rows()>0) {
        return $query->row();
      }else{
        return FALSE;
      }
   }

   public function obtenerRastreabilidadFiltro($fecha_inicio,$fecha_fin,$segmento,$porpagina){
     $this->db->select('r.id_registro,u.nombre_completo,m.nombre_modulo,r.fecha,r.hora,r.operacion')
              ->from('sic_rastreabilidad r')
              ->join('org_usuario u','u.id_usuario=r.id_usuario')
              ->join('org_modulo m','m.id_modulo=r.id_modulo')
              ->order_by('r.id_sic_rastreabilidad','desc')
              ->limit($segmento,$porpagina)
              ->where("r.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
     $query=$this->db->get();
     if ($query->num_rows()>0) {
       return $query->result();
     }else{
       return FALSE;
     }
   }

   public function obtenerRastreabilidadFiltroTotal($fecha_inicio,$fecha_fin){
     $this->db->select('count(*) as total')
              ->from('sic_rastreabilidad r')
              ->join('org_usuario u','u.id_usuario=r.id_usuario')
              ->join('org_modulo m','m.id_modulo=r.id_modulo')
              ->order_by('r.id_sic_rastreabilidad','desc')
              ->where("r.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
     $query=$this->db->get();
       return $query->row();
   }

   public function buscarUsuariosSICBAF($busca){
     $this->db->select('u.nombre_completo,ur.id_usuario_rol,r.nombre_rol,ur.id_usuario,ur.id_rol,u.sexo')
              ->from('org_usuario_rol ur')
              ->join('org_rol r','r.id_rol=ur.id_rol')
              ->join('org_usuario u','u.id_usuario=ur.id_usuario')
              ->join('org_rol_modulo_permiso rmp','r.id_rol=rmp.id_rol')
              ->join('org_modulo m','m.id_modulo=rmp.id_modulo')
              ->group_by('ur.id_usuario')
              ->where('u.estado',1)
              ->where('m.id_sistema',14);
     $this->db->order_by("ur.id_usuario", "asc");
     $this->db->like('u.nombre_completo', $busca);
     $this->db->or_like('r.nombre_rol', $busca);
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function obtenerUsuariosSICBAFLimit($porpagina, $segmento){
     $this->db->select('u.nombre_completo,ur.id_usuario_rol,r.nombre_rol,ur.id_usuario,ur.id_rol,u.sexo')
              ->from('org_usuario_rol ur')
              ->join('org_rol r','r.id_rol=ur.id_rol')
              ->join('org_usuario u','u.id_usuario=ur.id_usuario')
              ->join('org_rol_modulo_permiso rmp','r.id_rol=rmp.id_rol')
              ->join('org_modulo m','m.id_modulo=rmp.id_modulo')
              ->where('u.estado',1)
              ->limit($porpagina, $segmento)
              ->group_by('ur.id_usuario')
              ->where('m.id_sistema',14);
     $this->db->order_by("ur.id_usuario", "asc");
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function obtenerUsuariosSICBAFTotal(){
     $this->db->select('')
              ->from('org_usuario_rol ur')
              ->join('org_rol r','r.id_rol=ur.id_rol')
              ->join('org_usuario u','u.id_usuario=ur.id_usuario')
              ->join('org_rol_modulo_permiso rmp','r.id_rol=rmp.id_rol')
              ->join('org_modulo m','m.id_modulo=rmp.id_modulo')
              ->where('u.estado',1)
              ->group_by('u.id_usuario')
              ->where('m.id_sistema',14);
     $this->db->order_by("ur.id_usuario", "asc");
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->num_rows();
     }
     else {
         return FALSE;
     }
   }

   public function actualizarUsuarioRol($id, $data){
     $this->db->where('id_usuario_rol',$id);
     $this->db->update('org_usuario_rol', $data);
   }

   public function insertarUsuarioRol($data){
     $user=array(
       'id_usuario'=>$data['id_usuario'],
       'id_rol'=>$data['id_rol']
     );
       $this->db->insert('org_usuario_rol', $user);
       return $this->db->insert_id();
   }

   public function eliminarUsuarioRol($id){
     $this->db->delete('org_usuario_rol', array('id_usuario_rol' => $id));
   }

   public function obtenerUsuarios(){
     $this->db->select('e.id_empleado, ei.id_empleado_informacion_laboral, u.nombre_completo,u.id_usuario,ei.id_seccion,s.nombre_seccion,ei.id_traslado')
              ->from('org_usuario u')
              ->join('sir_empleado e','u.nr=e.nr')
              ->join('sir_empleado_informacion_laboral ei','e.id_empleado=ei.id_empleado')
              ->join('sir_traslado t','ei.id_traslado=t.id_traslado')
              ->join('org_seccion s','s.id_seccion=ei.id_seccion')
              ->order_by('e.id_empleado')
              ->group_by('ei.id_empleado')
              ->where('e.id_estado',1);
     $query=$this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function buscarUsuarios($busca){
     $this->db->select('e.id_empleado, ei.id_empleado_informacion_laboral, u.nombre_completo,u.id_usuario,ei.id_seccion,s.nombre_seccion,ei.id_traslado')
              ->from('org_usuario u')
              ->join('sir_empleado e','u.nr=e.nr')
              ->join('sir_empleado_informacion_laboral ei','e.id_empleado=ei.id_empleado')
              ->join('sir_traslado t','ei.id_traslado=t.id_traslado')
              ->join('org_seccion s','s.id_seccion=ei.id_seccion')
              ->order_by('ei.id_traslado','desc')
              ->group_by('ei.id_empleado')
              ->like('u.nombre_completo',$busca)
              ->or_like('u.nr',$busca)
              ->where('e.id_estado',1);
     $query=$this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function obtenerEmpleadosSeccionCorrecta($id_seccion){
     $this->db->select('u.id_usuario,lee.nombre_empleado,lee.id_seccion,lee.seccion')
              ->from('Lista_empleados_estado lee')
              ->join('org_usuario u','u.nr=lee.nr_empleado')
              ->where('lee.estado',1)
              ->where('lee.id_seccion',$id_seccion);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }
   }

   public function buscarEmpleadosSeccionCorrecta($busca,$id_seccion){
     $this->db->select('u.id_usuario,lee.nombre_empleado,lee.id_seccion,lee.seccion')
              ->from('Lista_empleados_estado lee')
              ->join('org_usuario u','u.nr=lee.nr_empleado')
              ->like('lee.nombre_empleado',$busca)
              ->where('lee.estado',1)
              ->where('lee.id_seccion',$id_seccion);
      $query=$this->db->get();
      if ($query->num_rows() > 0) {
          return  $query->result();
      }
      else {
          return FALSE;
      }

   }

   public function contarTraslados($id_empleado){
     $this->db->select('count(ei.id_empleado_informacion_laboral) as total')
              ->from('sir_empleado_informacion_laboral ei')
              ->join('sir_empleado e','e.id_empleado=ei.id_empleado')
              ->where('e.id_empleado',$id_empleado);
     $query=$this->db->get();
     return $query->row()->total;
   }

   public function obtenerRoles(){
     $this->db->select('r.id_rol,r.nombre_rol')
              ->from('org_rol r')
              ->join('org_rol_modulo_permiso rmp','r.id_rol=rmp.id_rol')
              ->join('org_modulo m','m.id_modulo=rmp.id_modulo')
              ->group_by('r.id_rol')
              ->where('m.id_sistema',14);
     $this->db->order_by("r.id_rol", "asc");
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function buscarRoles($busca){
     $this->db->select('r.id_rol,r.nombre_rol')
              ->from('org_rol r')
              ->join('org_rol_modulo_permiso rmp','r.id_rol=rmp.id_rol')
              ->join('org_modulo m','m.id_modulo=rmp.id_modulo')
              ->like('nombre_rol',$busca)
              ->group_by('r.id_rol')
              ->where('m.id_sistema',14);
     $this->db->order_by("r.id_rol", "asc");
     $query = $this->db->get();
     if ($query->num_rows() > 0) {
         return  $query->result();
     }
     else {
         return FALSE;
     }
   }

   public function empleadoTituloCargo($cargo) {
     $this->db->select('a.id_empleado, a.nr_empleado, a.nombre_empleado, a.cargo_funcional, c.titulo_academico, a.genero')
              ->from('Lista_empleados_estado a')
              ->join('sir_empleado_titulo b', 'a.id_empleado = b.id_empleado')
              ->join('sir_titulo_academico c', 'b.id_titulo_academico = c.id_titulo_academico')
              ->where('a.id_cargo_funcional', $cargo);

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return  $query->result();
    }
    else {
        return FALSE;
    }
   }
  }
?>
