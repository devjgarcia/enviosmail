<?php

namespace App\Enums;

abstract class EstadoUsuarios extends Enum
{
    const ACTIVO    = 'Activo';
    const INACTIVO  = 'Inactivo';
    const BLOQUEADO = 'Bloqueado';

    protected static $definitions = [
        self::ACTIVO    => 'Activo',
        self::INACTIVO  => 'Inactivo',
        self::BLOQUEADO => 'Bloqueado',
    ];

    public static function getClassEstado( $estado )
    {
        return [
            self::ACTIVO    => 'success',
            self::INACTIVO  => 'danger',
            self::BLOQUEADO => 'warning',
        ][$estado] ?? 'warning';
    }

    public static function getEstados()
    {
        return [
            ['value' => self::ACTIVO, 'text' => self::ACTIVO ],
            ['value' => self::INACTIVO, 'text' => self::INACTIVO ],
            ['value' => self::BLOQUEADO, 'text' => self::BLOQUEADO ],
        ];
    }

}