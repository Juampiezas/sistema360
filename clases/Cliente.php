<?php

class Cliente{

    private $id;
    private $nombre;
    private $telefono;
    private $direccion;

    public function __construct(
        $id,
        $nombre,
        $telefono,
        $direccion
    ){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
    }

    // GETTERS

    public function getId(){
        return $this->id;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    // SETTERS

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function setDireccion($direccion){
        $this->direccion = $direccion;
    }

}

?>