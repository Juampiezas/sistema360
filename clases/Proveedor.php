<?php

class Proveedor{

    private $id;
    private $nombre;
    private $empresa;
    private $telefono;

    public function __construct(
        $id,
        $nombre,
        $empresa,
        $telefono
    ){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->empresa = $empresa;
        $this->telefono = $telefono;
    }

    // GETTERS

    public function getId(){
        return $this->id;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getEmpresa(){
        return $this->empresa;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    // SETTERS

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function setEmpresa($empresa){
        $this->empresa = $empresa;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

}

?>