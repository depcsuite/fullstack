<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entidades\Sistema\Usuario;
use App\Entidades\Sistema\Patente;
use App\Entidades\Producto\Categoria;

require app_path().'/start/constants.php';
use Session;

class ControladorCategoria extends Controller{

    public function nuevo(){
            $titulo = "Nueva Categoria";
            $entidad = new Categoria();
            $array_categorias = $entidad->obtenerTodos();
            return view('producto.categoria-nuevo', compact('titulo','array_categorias'));
    }

}

?>