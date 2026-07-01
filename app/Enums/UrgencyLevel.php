<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum UrgencyLevel: string implements LabeledEnum
{
    case Bajo = 'bajo';
    case Medio = 'medio';
    case Alto = 'alto';
    case Critico = 'critico';

    public function label(): string
    {
        return match ($this) {
            self::Bajo => 'Bajo',
            self::Medio => 'Medio',
            self::Alto => 'Alto',
            self::Critico => 'Crítico',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Bajo => 'Comentario general o sugerencia',
            self::Medio => 'Requiere revisión del área correspondiente',
            self::Alto => 'Requiere atención prioritaria',
            self::Critico => 'Situación delicada o urgente',
        };
    }
}
