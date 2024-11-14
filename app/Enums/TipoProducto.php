<?php

namespace App\Enums;

abstract class TipoProducto extends Enum
{
    const SE = 'SE';
    const CE = 'CE';
    const SCL = 'SCL';

    protected static $definitions = [
        self::SE  => 'Saca Estándar',
        self::CE  => 'Saca Corporativo',
        self::SCL => 'Saca Móvil',
    ];

    public static function getTiposProducto() {
        $tipos = [];

        foreach( self::$definitions as $k => $tipo ){
            $tipos[] = ['value' => $k, 'text' => $tipo];
        }

        return $tipos;

        /*return [
            ['value' => self::SE, 'text' => self::$definitions[self::SE]],
            ['value' => self::DEMO,   'text' => self::$definitions[self::DEMO]],
        ];*/
    }

}