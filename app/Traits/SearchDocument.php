<?php

namespace App\Traits;

trait SearchDocument
{
    public function searchDocument($tipo, $numero)
    {
        $resp = [];
        
        if ($tipo == 'dni') {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        } elseif ($tipo == 'ce') {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        } elseif ($tipo == 'ruc') {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        } else {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        }
    }

    public function searchComplete($tipo, $numero)
    {
        $resp = [];
        
        if ($tipo == 'dni') {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        } elseif ($tipo == 'ce') {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        } elseif ($tipo == 'ruc') {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        } else {
            $resp['encontrado'] = false;
            $resp['mensaje']    = 'Tipo de Documento Desconocido';
            return $resp;
        }
    }
}
