<?php

class Venta{

    private $id;
    private $cliente;
    private $fecha;
    private $total;

    public function __construct(
        $id,
        $cliente,
        $fecha,
        $total
    ){
        $this->id = $id;
        $this->cliente = $cliente;
        $this->fecha = $fecha;
        $this->total = $total;
    }

    // GETTERS

    public function getId(){
        return $this->id;
    }

    public function getCliente(){
        return $this->cliente;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function getTotal(){
        return $this->total;
    }

    // SETTERS

    public function setCliente($cliente){
        $this->cliente = $cliente;
    }

    public function setFecha($fecha){
        $this->fecha = $fecha;
    }

    public function setTotal($total){
        $this->total = $total;
    }

}

?>