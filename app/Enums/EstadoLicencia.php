<?php

namespace App\Enums;

abstract class EstadoLicencia extends Enum
{
    const ACTIVA   = 1;
    const INACTIVA = 0;
    const DEMO     = 2;
    const VENCIDA  = 3;

    protected static $definitions = [
        self::ACTIVA   => 'Activa',
        self::INACTIVA => 'Inactiva',
        self::DEMO     => 'Demo',
        self::VENCIDA  => 'Vencida',
    ];

    public static function getEstado( $estado )
    {
        return self::$definitions[$estado];
    }

    public static function getClassEstado( $estado )
    {
        return [
            0 => 'danger',
            1 => 'success',
            2 => 'primary',
            3 => 'danger',
        ][$estado] ?? 'warning';
    }

    public static function getEstadoLicencia() {
        return [
            ['value' => self::ACTIVA, 'text' => self::$definitions[self::ACTIVA]],
            ['value' => self::DEMO,   'text' => self::$definitions[self::DEMO]],
        ];
    }
}
