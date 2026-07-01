<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum Department: string implements LabeledEnum
{
    case Administracion = 'administracion';
    case RecursosHumanos = 'recursos_humanos';
    case Sistemas = 'sistemas';
    case Operaciones = 'operaciones';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Administracion => 'Administración',
            self::RecursosHumanos => 'Recursos humanos',
            self::Sistemas => 'Sistemas',
            self::Operaciones => 'Operaciones',
            self::Otro => 'Otro',
        };
    }
}
