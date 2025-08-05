<?php

declare(strict_types=1);

abstract class Auto{

    private int $id;
    private string $marca;
    private string $modelo;

    private string $color;

    private int $anio;

    public function __construct(int $idC, string $marcaC, string $modeloC, string $colorC, int $anioC) {
        $this->id = $idC;
        $this->marca = $marcaC;
        $this->modelo = $modeloC;
        $this->color = $colorC;
        $this->anio = $anioC;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }
    
    public function getMarca(): string {
        return $this->marca;
    }
    public function getModelo(): string {
        return $this->modelo;
    }
    public function getColor(): string {
        return $this->color;
    }
    public function getAnio(): int {
        return $this->anio;
    }

    // Setters
    public function setId(int $idIn): void {
        $this->id = $idIn;
    }

    public function setMarca(string $marcaIn): void {
        $marcaTemp = trim($marcaIn);
        if(trim($marcaIn) === ''){

            throw new InvalidArgumentException("La marca no puede ser nula");
        }
        $this->marca = trim($marcaIn);
    }

    
    public function setModelo(string $modelo): void {
        $modeloTemp = trim($modelo);
        if(trim($modelo) === ''){

            throw new InvalidArgumentException("El modelo no puede ser nulo");
        }
        $this->marca = trim($modelo);
    }

    
    public function setColor(string $color): void {
        $modeloTemp = trim($color);
        if(trim($color) === ''){

            throw new InvalidArgumentException("La marca no puede ser nula");
        }
        $this->marca = trim($color);
    }

        public function setAnio(string $anio): void {
        $modeloTemp = trim($anio);
        if($anio < 1900 || $anio > ((int) date('Y') +1)){

            throw new InvalidArgumentException("El año no puede ser nulo");

        }
        $this->marca = trim($anio);
    }
}

    //
 /*   abstract public function TipoCombustible(): string;




}
 */



?>