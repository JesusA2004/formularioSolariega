<?php

namespace App\Exports;

use App\Exports\Sheets\EvidenceSheet;
use App\Exports\Sheets\SummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RequestsExcelExport implements WithMultipleSheets
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(private readonly array $filters = []) {}

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new SummarySheet($this->filters),
            new RequestsExport($this->filters),
            new EvidenceSheet($this->filters),
        ];
    }
}
