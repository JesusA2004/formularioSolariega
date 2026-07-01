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
            self::Queja => 'Comentario',
            self::Sugerencia => 'Sugerencia',
            self::Incidente => 'Situación a revisar',
            self::MalTrato => 'Inconformidad',
            self::Acoso => 'Inconformidad',
            self::CondicionesLaborales => 'Situación a revisar',
            self::ProblemasColaboradores => 'Solicitud de seguimiento',
            self::Otro => 'Otro',
        };
    }

    /**
     * Subset con etiqueta única que se ofrece en el formulario público.
     *
     * @return array<int, self>
     */
    public static function publicOptions(): array
    {
        return [
            self::Queja,
            self::Sugerencia,
            self::Incidente,
            self::MalTrato,
            self::ProblemasColaboradores,
            self::Otro,
        ];
    }
}
