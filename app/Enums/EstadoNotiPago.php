<?php

namespace App\Enums;

abstract class EstadoNotiPago extends Enum
{
    
    const EN_ESPERA = 0;
    const REVISADO  = 2;
    const APROBADO  = 1;
    const RECHAZADO = 3;

    protected static $definitions = [
        self::EN_ESPERA => 'En Espera',
        self::REVISADO  => 'Revisado',
        self::APROBADO  => 'Aprobado',
        self::RECHAZADO => 'Rechazado',
    ];

    public static function getEstado( $estado )
    {
        return self::$definitions[$estado];
    }

    public static function getClassEstado( $estado )
    {
        return [
            0 => 'background-color: #6f6f6f; color: white;',
            1 => 'background-color: #449d44; color: white;',
            2 => 'background-color: #1f91f3; color: white;',
            3 => 'background-color: #fb483a; color: white;',
        ][$estado] ?? 'background-color: #FFC107; color: black;';
    }

    public static function getEstadoNotiPago() {
        return [
            ['value' => self::APROBADO,  'text' => self::$definitions[self::APROBADO]],
            ['value' => self::RECHAZADO, 'text' => self::$definitions[self::RECHAZADO]],
        ];
    }
}
