<?php

namespace App\Utils;

use Illuminate\Contracts\Support\Arrayable;

class Respuesta implements Arrayable
{
    public $estado;
    public $data;
    public $mensaje;

    public function __construct($estado = false, $data = null, $mensaje = "")
    {
        $this->estado = $estado;
        $this->data = $data;
        $this->mensaje = $mensaje;
    }

    /**
     * Crea una respuesta exitosa
     * @param mixed $data
     * @param string $mensaje
     * @return Respuesta
     */
    public static function success($data = null, $mensaje = "Operación exitosa")
    {
        return new self(true, $data, $mensaje);
    }

    /**
     * Crea una respuesta de error
     * @param string $mensaje
     * @param mixed $data
     * @return Respuesta
     */
    public static function error($data = null, $mensaje = "Ocurrió un error")
    {
        return new self(false, $data, $mensaje);
    }

    /**
     * Convierte la respuesta a un array
     * @return array
     */
    public function toArray()
    {
        return [
            'estado' => $this->estado,
            'data' => $this->data,
            'mensaje' => $this->mensaje,
        ];
    }

    /**
     * Convierte la respuesta a JSON
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

}
