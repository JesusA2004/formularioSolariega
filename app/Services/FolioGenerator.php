<?php

namespace App\Services;

use App\Models\Request as BuzonRequest;

class FolioGenerator
{
    /**
     * Generate the next sequential folio for the current year, e.g. BZ-2026-000001.
     *
     * Must be called from within a database transaction to guarantee uniqueness
     * under concurrent submissions, since it locks the matching rows for update.
     */
    public static function next(): string
    {
        $year = now()->year;
        $prefix = "BZ-{$year}-";

        $lastFolio = BuzonRequest::query()
            ->where('folio', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('folio')
            ->value('folio');

        $nextNumber = $lastFolio
            ? ((int) substr($lastFolio, strlen($prefix))) + 1
            : 1;

        return $prefix.str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
