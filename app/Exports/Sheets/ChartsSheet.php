<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Hoja con las mismas gráficas del dashboard (tipo, departamento, estado,
 * evidencia) como gráficas nativas de Excel, con los mismos colores.
 */
class ChartsSheet implements WithCharts, WithEvents, WithTitle
{
    private const STATUS_COLORS = ['3B82F6', 'D4AF37', '22C55E', '64748B', 'EF4444'];

    private const EVIDENCE_COLORS = ['8B5CF6', '64748B'];

    private const PALETTE = ['3B82F6', 'D4AF37', '22C55E', 'F97316', '8B5CF6', 'EF4444', '64748B', '171717'];

    /** @var array<string, array{title: string, data: array<int, array{label: string, value: int}>, colors: array<int, string>, type: string, topLeft: string, bottomRight: string, headerRow: int, firstDataRow: int, lastDataRow: int, titleRow: int}> */
    private array $blocks;

    /**
     * @param  array<int, array{label: string, value: int}>  $byType
     * @param  array<int, array{label: string, value: int}>  $byDepartment
     * @param  array<int, array{label: string, value: int}>  $byStatus
     * @param  array<int, array{label: string, value: int}>  $byEvidence
     */
    public function __construct(
        array $byType,
        array $byDepartment,
        array $byStatus,
        array $byEvidence,
    ) {
        $definitions = [
            'tipo' => ['title' => 'Mensajes por tipo', 'data' => $byType, 'colors' => self::PALETTE, 'type' => DataSeries::TYPE_BARCHART, 'topLeft' => 'D3', 'bottomRight' => 'K18'],
            'departamento' => ['title' => 'Mensajes por departamento', 'data' => $byDepartment, 'colors' => self::PALETTE, 'type' => DataSeries::TYPE_BARCHART, 'topLeft' => 'D20', 'bottomRight' => 'K35'],
            'estado' => ['title' => 'Mensajes por estado', 'data' => $byStatus, 'colors' => self::STATUS_COLORS, 'type' => DataSeries::TYPE_DOUGHNUTCHART, 'topLeft' => 'D37', 'bottomRight' => 'K52'],
            'evidencia' => ['title' => 'Evidencia', 'data' => $byEvidence, 'colors' => self::EVIDENCE_COLORS, 'type' => DataSeries::TYPE_DOUGHNUTCHART, 'topLeft' => 'D54', 'bottomRight' => 'K69'],
        ];

        $row = 3;
        $this->blocks = [];

        foreach ($definitions as $key => $definition) {
            $titleRow = $row;
            $headerRow = $row + 1;
            $firstDataRow = $row + 2;
            $lastDataRow = max($firstDataRow, $firstDataRow + count($definition['data']) - 1);

            $this->blocks[$key] = $definition + [
                'titleRow' => $titleRow,
                'headerRow' => $headerRow,
                'firstDataRow' => $firstDataRow,
                'lastDataRow' => $lastDataRow,
            ];

            $row = $lastDataRow + 2;
        }
    }

    public function title(): string
    {
        return 'Graficas';
    }

    /**
     * @return array<int, Chart>
     */
    public function charts(): array
    {
        return array_map(
            fn (array $block) => $this->buildChart($block),
            array_values($this->blocks),
        );
    }

    /**
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'Gráficas de mensajes');
                $sheet->mergeCells('A1:B1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)
                    ->getColor()->setRGB('D4AF37');
                $sheet->getStyle('A1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('171717');

                foreach ($this->blocks as $block) {
                    $this->writeBlock($sheet, $block);
                }

                $sheet->getColumnDimension('A')->setWidth(28);
                $sheet->getColumnDimension('B')->setWidth(12);
            },
        ];
    }

    /**
     * @param  array{title: string, data: array<int, array{label: string, value: int}>, titleRow: int, headerRow: int, firstDataRow: int}  $block
     */
    private function writeBlock(Worksheet $sheet, array $block): void
    {
        $sheet->setCellValue("A{$block['titleRow']}", $block['title']);
        $sheet->getStyle("A{$block['titleRow']}")->getFont()->setBold(true)->getColor()->setRGB('171717');

        $sheet->setCellValue("A{$block['headerRow']}", 'Categoría');
        $sheet->setCellValue("B{$block['headerRow']}", 'Mensajes');
        $sheet->getStyle("A{$block['headerRow']}:B{$block['headerRow']}")->getFont()->setBold(true);

        $row = $block['firstDataRow'];

        foreach ($block['data'] as $point) {
            $sheet->setCellValue("A{$row}", $point['label']);
            $sheet->setCellValue("B{$row}", $point['value']);
            $row++;
        }
    }

    /**
     * @param  array{title: string, data: array<int, array{label: string, value: int}>, colors: array<int, string>, type: string, topLeft: string, bottomRight: string, headerRow: int, firstDataRow: int, lastDataRow: int}  $block
     */
    private function buildChart(array $block): Chart
    {
        $sheetName = "'".$this->title()."'";
        $count = max(1, count($block['data']));

        $values = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "{$sheetName}!\$B\${$block['firstDataRow']}:\$B\${$block['lastDataRow']}",
            null,
            $count,
        );
        $values->setFillColor(array_slice($block['colors'], 0, $count));
        $values->setDataValues(array_column($block['data'], 'value'));

        $labels = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "{$sheetName}!\$B\${$block['headerRow']}",
            null,
            1,
            [$block['title']],
        )];

        $categories = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "{$sheetName}!\$A\${$block['firstDataRow']}:\$A\${$block['lastDataRow']}",
            null,
            $count,
        )];
        $categories[0]->setDataValues(array_column($block['data'], 'label'));

        $series = new DataSeries(
            $block['type'],
            $block['type'] === DataSeries::TYPE_BARCHART ? DataSeries::GROUPING_CLUSTERED : null,
            [0],
            $labels,
            $categories,
            [$values],
        );

        if ($block['type'] === DataSeries::TYPE_BARCHART) {
            $series->setPlotDirection(DataSeries::DIRECTION_BAR);
        }

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $chartTitle = new Title($block['title']);

        $chart = new Chart('chart_'.md5($block['title']), $chartTitle, $legend, $plotArea);
        $chart->setTopLeftPosition($block['topLeft']);
        $chart->setBottomRightPosition($block['bottomRight']);

        return $chart;
    }
}
