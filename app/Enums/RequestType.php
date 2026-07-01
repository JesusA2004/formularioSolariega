<?php

namespace App\Enums;

use App\Contracts\LabeledEnum;

enum RequestType: string implements LabeledEnum
{
    case Queja = 'queja';
    case Sugerencia = 'sugerencia';
    case Incidente = 'incidente';
    case MalTrato = 'mal_trato';
    case Acoso = 'acoso';
    case CondicionesLaborales = 'condiciones_laborales';
    case ProblemasColaboradores = 'problemas_colaboradores';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Queja => 'Queja',
            self::Sugerencia => 'Sugerencia',
            self::Incidente => 'Incidente',
            self::MalTrato => 'Mal trato',
            self::Acoso => 'Acoso',
            self::CondicionesLaborales => 'Condiciones laborales',
            self::ProblemasColaboradores => 'Problemas con colaboradores',
            self::Otro => 'Otro',
        };
    }
}
