<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum SenderType: string implements LabeledEnum
{
    case Cliente = 'cliente';
    case Colaborador = 'colaborador';
    case Visitante = 'visitante';
    case Proveedor = 'proveedor';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Cliente => 'Cliente',
            self::Colaborador => 'Colaborador',
            self::Visitante => 'Visitante',
            self::Proveedor => 'Proveedor',
            self::Otro => 'Otro',
        };
    }
}
