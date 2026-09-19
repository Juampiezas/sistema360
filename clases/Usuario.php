<?php

class Usuario{

    private $id;
    private $usuario;
    private $clave;
    private $rol;

    public function __construct(
        $id,
        $usuario,
        $clave,
        $rol
    ){
        $this->id = $id;
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->rol = $rol;
    }

    // GETTERS

    public function getId(){
        return $this->id;
    }

    public function getUsuario(){
        return $this->usuario;
    }

    public function getClave(){
        return $this->clave;
    }

    public function getRol(){
        return $this->rol;
    }

    // SETTERS

    public function setUsuario($usuario){
        $this->usuario = $usuario;
    }

    public function setClave($clave){
        $this->clave = $clave;
    }

    public function setRol($rol){
        $this->rol = $rol;
    }

}

?>