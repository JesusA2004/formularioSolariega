<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum RequestStatus: string implements LabeledEnum
{
    case Recibido = 'recibido';
    case EnRevision = 'en_revision';
    case Atendido = 'atendido';
    case Cerrado = 'cerrado';
    case Descartado = 'descartado';

    public function label(): string
    {
        return match ($this) {
            self::Recibido => 'Recibido',
            self::EnRevision => 'En revisión',
            self::Atendido => 'Atendido',
            self::Cerrado => 'Cerrado',
            self::Descartado => 'Descartado',
        };
    }
}
