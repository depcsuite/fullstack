<?php

namespace App\Entidades\Sistema;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Categoria
{
    protected $table = 'categorias';
    public $timestamps = false;

    protected $fillable = [
        'idmenu', 'nombre', 'fk_idcategoria'
    ];

    protected $hidden = [

    ];

    function cargarDesdeRequest($request) {
        $this->idcategoria = $request->input('id')!="0" ? $request->input('id') : $this->idmenu;
        $this->nombre = $request->input('txtNombre');
        $this->fk_idcategoria = $request->input('id');

    }

    public function obtenerFiltrado() {
        $request = $_REQUEST;
        $columns = array(
           0 => 'A.nombre',
           1 => 'B.nombre',
           2 => 'A.url',
           3 => 'A.activo'
            );
        $sql = "SELECT DISTINCT
                    A.idmenu,
                    A.nombre,
                    B.nombre as padre,
                    A.url,
                    A.activo
                    FROM sistema_menues A
                    LEFT JOIN sistema_menues B ON A.id_padre = B.idmenu
                WHERE 1=1
                ";

        //Realiza el filtrado
        if (!empty($request['search']['value'])) { 
            $sql.=" AND ( A.nombre LIKE '%" . $request['search']['value'] . "%' ";
            $sql.=" OR B.nombre LIKE '%" . $request['search']['value'] . "%' ";
            $sql.=" OR A.url LIKE '%" . $request['search']['value'] . "%' )";
        }
        $sql.=" ORDER BY " . $columns[$request['order'][0]['column']] . "   " . $request['order'][0]['dir'];

        $lstRetorno = DB::select($sql);

        return $lstRetorno;
    }

    public function obtenerTodos() {
        $sql = "SELECT 
                  A.idcategoria,
                  A.nombre,
                  A.fk_idcategoria
                FROM categorias A";

        $sql .= " ORDER BY A.nombre";
        $lstRetorno = DB::select($sql);
        return $lstRetorno;
    }

       public function obtenerMenuPadre() {
        $sql = "SELECT DISTINCT
                  A.idmenu,
                  A.nombre
                FROM sistema_menues A
                WHERE A.id_padre = 0";

        $sql .= " ORDER BY A.nombre";
        $lstRetorno = DB::select($sql);
        return $lstRetorno;
    }

    public function obtenerSubMenu($idmenu=null){
        if($idmenu){
            $sql = "SELECT DISTINCT
                      A.idmenu,
                      A.nombre
                    FROM sistema_menues A
                    WHERE A.idmenu <> '$idmenu'";

            $sql .= " ORDER BY A.nombre";
            $resultado = DB::select($sql);
        } else {
            $resultado = $this->obtenerTodos();
        }
        return $resultado;
    }

    public function obtenerPorId($idmenu) {
        $sql = "SELECT
                idcategoria,
                nombre,
                fk_idcategoria
                FROM categorias WHERE idcategoria = '$idcategoria'";
        $lstRetorno = DB::select($sql);

        if(count($lstRetorno)>0){
            $this->idcategoria = $lstRetorno[0]->idcategoria;
            $this->nombre = $lstRetorno[0]->nombre;
            $this->fk_idcategoria = $lstRetorno[0]->fk_idcategoria;
            return $this;
        }
        return null;
    }

    public function guardar() {
        $sql = "UPDATE sistema_menues SET
            nombre='$this->nombre',
            fk_idcategoria='$this->fk_idcategoria'
            WHERE idcategoria=?";
        $affected = DB::update($sql, [$this->idcategoria]);
    }

    public  function eliminar() {
        $sql = "DELETE FROM categoriass WHERE 
            idcategoria=?";
        $affected = DB::delete($sql, [$this->idcategoria]);
    }

    public function insertar() {
        $sql = "INSERT INTO categorias (
                nombre,
                fk_idcategoria
            ) VALUES (?, ?);";
       $result = DB::insert($sql, [
            $this->nombre, 
            $this->fk_idcategoria 

        ]);
       return $this->idcategoria = DB::getPdo()->lastInsertId();
    }

    public function obtenerMenuDelGrupo($idGrupo){
        $sql = "SELECT DISTINCT
        A.idmenu,
        A.nombre,
        A.id_padre,
        A.orden,
        A.url,
        A.css
        FROM sistema_menues A
        INNER JOIN sistema_menu_area B ON A.idmenu = B.fk_idmenu
        WHERE A.activo = '1' AND B.fk_idarea = $idGrupo ORDER BY A.orden";
        $resultado = DB::select($sql);
        return $resultado;
    }

}
