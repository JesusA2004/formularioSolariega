<?php

namespace App\Exports;

use App\Exports\Sheets\ChartsSheet;
use App\Exports\Sheets\EvidenceSheet;
use App\Exports\Sheets\SummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RequestsExcelExport implements WithMultipleSheets
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<int, array{label: string, value: int}>  $byType
     * @param  array<int, array{label: string, value: int}>  $byDepartment
     * @param  array<int, array{label: string, value: int}>  $byStatus
     * @param  array<int, array{label: string, value: int}>  $byEvidence
     */
    public function __construct(
        private readonly array $filters = [],
        private readonly array $byType = [],
        private readonly array $byDepartment = [],
        private readonly array $byStatus = [],
        private readonly array $byEvidence = [],
    ) {}

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new SummarySheet($this->filters),
            new RequestsExport($this->filters),
            new EvidenceSheet($this->filters),
            new ChartsSheet($this->byType, $this->byDepartment, $this->byStatus, $this->byEvidence),
        ];
    }
}
