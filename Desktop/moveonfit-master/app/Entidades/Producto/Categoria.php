<?php

namespace App\Entidades\Sistema;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Categoria extends Model
{
    protected $table = 'categorias';
    public $timestamps = false;

    protected $fillable = [
        'idcategoria',
         'nombre'
    ];

    protected $hidden = [

    ];

    function cargarDesdeRequest($request) {
        $this->idcategoria = $request->input('id')!="0" ? $request->input('id') : $this->idcategoria;
        $this->nombre = $request->input('txtNombre');
    }


    public function obtenerTodos() {
        $sql = "SELECT 
                  idcategoria,
                  nombre
                FROM categorias";
        $lstRetorno = DB::select($sql);

        $aFicha = array();
        if($lstRetorno){
            while($fila = $lstRetorno->fetch_assoc()){
                $entidad = new Categoria();
                $entidad->idcategoria = $fila["idcategoria"];
                $entidad->nombre = $fila["nombre"];
                $aFicha[] = $entidad;
            }
        }
        return $aFicha;
    }

    public function obtenerPorId($idcategoria) {
        $sql = "SELECT
                idcategoria,
                nombre
                FROM categorias WHERE idcategoria = '$idcategoria'";
        $lstRetorno = DB::select($sql);

        if(count($lstRetorno)>0){
            $this->idcategoria = $lstRetorno[0]->idcategoria;
            $this->nombre = $lstRetorno[0]->nombre;
            return $this;
        }
        return null;
    }

    public function guardar() {
        $sql = "UPDATE categorias SET
            nombre='$this->nombre'
            WHERE idcategoria=?";
        $affected = DB::update($sql, [$this->idcategoria]);
    }

    public  function eliminar() {
        $sql = "DELETE FROM categorias WHERE 
            idcategoria=?";
        $affected = DB::delete($sql, [$this->idcategoria]);
    }

    public function insertar() {
        $sql = "INSERT INTO categorias (
                nombre
            ) VALUES (?);";
       $result = DB::insert($sql, [
            $this->nombre
        ]);
       return $this->idcategoria = DB::getPdo()->lastInsertId();
    }

}
