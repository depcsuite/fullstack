<?php

namespace App\Entidades\Sistema\Producto;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Producto
{
    protected $table = 'productos';
    public $timestamps = false;

    protected $fillable = [
        'idproducto', 'nombre', 'precio', 'descripcion', 'foto', 'fk_idcategorias', 'stock', 'activo'
    ];

    protected $hidden = [

    ];

    function cargarDesdeRequest($request) {
        $this->idproducto = $request->input('id')!="0" ? $request->input('id') : $this->idmenu;
        $this->nombre = $request->input('txtNombre');
        $this->precio = $request->input('txtPrecio');
        $this->descripcion = $request->input('txtDescripcion') != "" ? $request->input('txtDescripcion') : 0;
        $this->foto = $request->input('txtFoto');
        $this->fk_idcategorias = $request->input('lstCategorias');
        $this->stock = $request->input('txtStock');
        $this->activo = $request->input('lstActivo');
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
                  A.idproducto,
                  A.nombre,
                  A.precio,
                  A.descripcion,
                  A.foto,
                  A.fk_idcategoria,
                  A.stock,
                  A.activo
                FROM productos A";

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

    public function obtenerPorId($idproducto) {
        $sql = "SELECT
                idproducto,
                nombre,
                precio,
                descripcion,
                foto,
                fk_idcategoria,
                stock,
                activo
                FROM productos WHERE idproducto = '$idproducto'";
        $lstRetorno = DB::select($sql);

        if(count($lstRetorno)>0){
            $this->idproducto = $lstRetorno[0]->idproducto;
            $this->nombre = $lstRetorno[0]->nombre;
            $this->precio = $lstRetorno[0]->precio;
            $this->descripcion = $lstRetorno[0]->descripcion;
            $this->foto = $lstRetorno[0]->foto;
            $this->fk_idcategoria = $lstRetorno[0]->fk_idcategoria;
            $this->stock = $lstRetorno[0]->stock;
            $this->activo = $lstRetorno[0]->activo;
            return $this;
        }
        return null;
    }

    public function guardar() {
        $sql = "UPDATE productos SET
            nombre='$this->nombre',
            precio='$this->precio',
            descripcion=$this->descripcion,
            foto='$this->foto',
            fk_idcategoria='$this->fk_idcategoria',
            stock='$this->stock',
            activo='$this->activo'
            WHERE idproducto=?";
        $affected = DB::update($sql, [$this->idproducto]);
    }

    public  function eliminar() {
        $sql = "DELETE FROM productos WHERE 
            idproducto=?";
        $affected = DB::delete($sql, [$this->idprodcto]);
    }

    public function insertar() {
        $sql = "INSERT INTO productos (
                idproducto,
                nombre,
                precio,
                descripcion,
                foto,
                fk_idcategoria,
                stock,
                activo
            ) VALUES (?, ?, ?, ?, ?, ?, ?);";
       $result = DB::insert($sql, [
            $this->nombre, 
            $this->precio, 
            $this->descripcion, 
            $this->foto, 
            $this->fk_idcategoria,
            $this->stock,
            $this->activo
        ]);
       return $this->idproducto = DB::getPdo()->lastInsertId();
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
