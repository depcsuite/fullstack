<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entidades\Sistema\Usuario;
use App\Entidades\Sistema\Patente;
use App\Entidades\Sistema\Producto\Producto;

require app_path().'/start/constants.php';
use Session;

class ControladorProducto extends Controller{
    
    public function nuevo(){
            $titulo = "Nuevo Producto";
            //$entidad = new Producto();
            //$array_producto = $entidad->obtenerProductoPadre();
            return view ('sistema.producto-nuevo', compact('titulo'));

    }

}