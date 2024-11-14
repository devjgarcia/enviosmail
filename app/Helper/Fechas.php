<?php 

namespace App\Helper;

class Fechas
{
    public static function obtenerFechasSemana()
    {
        $fechas = [];
        //retorna la ultima semana con la fecha del lunes y viernes
        $dia_actual = date('w');

        if( $dia_actual <= 1 ){
            //es lunes
            $fechas['desde'] = date('Y-m-d', strtotime('previous monday'));
            $fechas['hasta'] = date('Y-m-d', strtotime('previous friday'));
        }
        if( $dia_actual == 5 ){
            //es viernes
            $fechas['desde'] = date('Y-m-d', strtotime('previous monday'));
            $fechas['hasta'] = date('Y-m-d');
        }
        else{
            $fechas['desde'] = date('Y-m-d', strtotime('previous monday'));
            $fechas['hasta'] = date('Y-m-d');
        }
        
    }
}