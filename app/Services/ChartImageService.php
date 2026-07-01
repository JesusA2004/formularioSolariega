<?php

namespace App\Services;

use GdImage;

/**
 * Renders small chart PNGs (bar / donut) with GD so the PDF report can show
 * the same breakdowns as the dashboard, without depending on JS execution.
 */
class ChartImageService
{
    private const INK = [0x17, 0x17, 0x17];

    private const MUTED = [0x6B, 0x64, 0x57];

    private const GRID = [0xE7, 0xE2, 0xD8];

    /**
     * @param  array<int, array{label: string, value: int}>  $points
     * @param  array<int, string>  $colors
     */
    public function verticalBarChart(array $points, array $colors, int $width = 680, int $height = 300): string
    {
        if ($points === [] || array_sum(array_column($points, 'value')) === 0) {
            return $this->emptyState($width, $height);
        }

        $im = $this->canvas($width, $height);

        $left = 40;
        $right = 20;
        $top = 20;
        $bottom = 60;
        $plotWidth = $width - $left - $right;
        $plotHeight = $height - $top - $bottom;

        $max = max(1, ...array_column($points, 'value'));
        $this->gridLines($im, $left, $top, $plotWidth, $plotHeight, $max);

        $count = max(1, count($points));
        $slot = $plotWidth / $count;
        $barWidth = min(64, $slot * 0.55);

        foreach (array_values($points) as $i => $point) {
            $color = $this->allocate($im, $colors[$i % count($colors)]);
            $barHeight = $max > 0 ? ($point['value'] / $max) * $plotHeight : 0;
            $x = $left + ($i * $slot) + ($slot - $barWidth) / 2;
            $y = $top + $plotHeight - $barHeight;

            imagefilledrectangle($im, (int) $x, (int) $y, (int) ($x + $barWidth), (int) ($top + $plotHeight), $color);

            $this->centeredText($im, (string) $point['value'], (int) ($x + $barWidth / 2), (int) $y - 14, self::INK, 8, true);
            $this->rotatedLabel($im, $this->truncate($point['label'], 14), (int) ($x + $barWidth / 2), $top + $plotHeight + 8);
        }

        return $this->toDataUri($im);
    }

    /**
     * @param  array<int, array{label: string, value: int}>  $points
     * @param  array<int, string>  $colors
     */
    public function horizontalBarChart(array $points, array $colors, int $width = 680, int $height = 300): string
    {
        if ($points === [] || array_sum(array_column($points, 'value')) === 0) {
            return $this->emptyState($width, $height);
        }

        $im = $this->canvas($width, $height);

        $left = 170;
        $right = 50;
        $top = 15;
        $bottom = 15;
        $plotWidth = $width - $left - $right;
        $plotHeight = $height - $top - $bottom;

        $max = max(1, ...array_column($points, 'value'));
        $count = max(1, count($points));
        $slot = $plotHeight / $count;
        $barHeight = min(28, $slot * 0.6);

        foreach (array_values($points) as $i => $point) {
            $color = $this->allocate($im, $colors[$i % count($colors)]);
            $barWidth = $max > 0 ? ($point['value'] / $max) * $plotWidth : 0;
            $y = $top + ($i * $slot) + ($slot - $barHeight) / 2;

            imagefilledrectangle($im, $left, (int) $y, (int) ($left + $barWidth), (int) ($y + $barHeight), $color);

            $this->text($im, $this->truncate($point['label'], 20), $left - 8, (int) ($y + $barHeight / 2) + 4, self::INK, 9, true, true);
            $this->text($im, (string) $point['value'], (int) ($left + $barWidth + 8), (int) ($y + $barHeight / 2) + 4, self::INK, 9);
        }

        return $this->toDataUri($im);
    }

    /**
     * @param  array<int, array{label: string, value: int}>  $points
     * @param  array<int, string>  $colors
     */
    public function donutChart(array $points, array $colors, int $width = 460, int $height = 260): string
    {
        if ($points === [] || array_sum(array_column($points, 'value')) === 0) {
            return $this->emptyState($width, $height);
        }

        $im = $this->canvas($width, $height);

        $size = $height - 20;
        $cx = 10 + (int) ($size / 2);
        $cy = (int) ($height / 2);
        $radius = (int) ($size / 2);

        $total = max(1, array_sum(array_column($points, 'value')));
        $start = 0.0;

        foreach (array_values($points) as $i => $point) {
            $color = $this->allocate($im, $colors[$i % count($colors)]);
            $sweep = ($point['value'] / $total) * 360;
            $end = $start + max($sweep, $point['value'] > 0 ? 0.5 : 0);

            if ($point['value'] > 0) {
                imagefilledarc($im, $cx, $cy, $radius * 2, $radius * 2, (int) $start, (int) ceil($end), $color, IMG_ARC_PIE);
            }

            $start = $end;
        }

        $hole = $this->allocate($im, '#FFFFFF');
        imagefilledellipse($im, $cx, $cy, (int) ($radius * 1.1), (int) ($radius * 1.1), $hole);

        $legendX = $size + 40;
        $legendY = 20;
        $lineHeight = 22;

        foreach (array_values($points) as $i => $point) {
            $color = $this->allocate($im, $colors[$i % count($colors)]);
            $percent = round(($point['value'] / $total) * 100);

            imagefilledrectangle($im, $legendX, $legendY + ($i * $lineHeight), $legendX + 12, $legendY + 12 + ($i * $lineHeight), $color);
            $this->text($im, "{$point['label']} ({$point['value']} · {$percent}%)", $legendX + 18, $legendY + 10 + ($i * $lineHeight), self::INK, 9);
        }

        return $this->toDataUri($im);
    }

    private function emptyState(int $width, int $height): string
    {
        $im = $this->canvas($width, $height);
        $centerX = (int) ($width / 2);
        $iconY = (int) ($height / 2) - 34;

        $muted = $this->rgbColor($im, self::GRID);
        $barHeights = [16, 28, 20];
        $barWidth = 14;
        $gap = 8;
        $totalWidth = (count($barHeights) * $barWidth) + ((count($barHeights) - 1) * $gap);
        $startX = $centerX - (int) ($totalWidth / 2);
        $baseY = $iconY + 30;

        foreach ($barHeights as $i => $barHeight) {
            $x = $startX + ($i * ($barWidth + $gap));
            imagefilledrectangle($im, $x, $baseY - $barHeight, $x + $barWidth, $baseY, $muted);
        }

        $this->centeredText(
            $im,
            'Aún no hay datos suficientes para mostrar esta gráfica.',
            $centerX,
            $baseY + 26,
            self::MUTED,
            10,
        );

        return $this->toDataUri($im);
    }

    private function canvas(int $width, int $height): GdImage
    {
        $im = imagecreatetruecolor(max(1, $width), max(1, $height));
        imagesavealpha($im, true);
        $white = $this->allocate($im, '#FFFFFF');
        imagefilledrectangle($im, 0, 0, $width, $height, $white);
        imageantialias($im, true);

        return $im;
    }

    private function gridLines(GdImage $im, int $left, int $top, int $plotWidth, int $plotHeight, int $max): void
    {
        $grid = $this->allocate($im, '#'.implode('', array_map(fn ($c) => str_pad(dechex($c), 2, '0', STR_PAD_LEFT), self::GRID)));
        $steps = 4;

        for ($i = 0; $i <= $steps; $i++) {
            $y = $top + $plotHeight - (int) (($i / $steps) * $plotHeight);
            imageline($im, $left, $y, $left + $plotWidth, $y, $grid);
            $value = (int) round(($i / $steps) * $max);
            $this->text($im, (string) $value, $left - 8, $y + 3, self::MUTED, 7, true, true);
        }
    }

    private function allocate(GdImage $im, string $hex): int
    {
        $hex = ltrim($hex, '#');

        return $this->rgbColor($im, [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ]);
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    private function rgbColor(GdImage $im, array $rgb): int
    {
        $color = imagecolorallocate(
            $im,
            max(0, min(255, $rgb[0])),
            max(0, min(255, $rgb[1])),
            max(0, min(255, $rgb[2])),
        );

        return $color === false ? 0 : $color;
    }

    /**
     * @return array<int, int>
     */
    private function bbox(int $size, int $angle, string $font, string $text): array
    {
        $box = imagettfbbox($size, $angle, $font, $text);

        return $box === false ? array_fill(0, 8, 0) : $box;
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    private function text(GdImage $im, string $text, int $x, int $y, array $rgb, int $size, bool $bold = false, bool $rightAlign = false): void
    {
        $font = $this->fontPath($bold);
        $color = $this->rgbColor($im, $rgb);

        if ($rightAlign) {
            $box = $this->bbox($size, 0, $font, $text);
            $x = max(4, $x - ($box[2] - $box[0]));
        }

        imagettftext($im, $size, 0, $x, $y, $color, $font, $text);
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    private function centeredText(GdImage $im, string $text, int $centerX, int $y, array $rgb, int $size, bool $bold = false): void
    {
        $font = $this->fontPath($bold);
        $box = $this->bbox($size, 0, $font, $text);
        $textWidth = $box[2] - $box[0];
        $this->text($im, $text, (int) ($centerX - $textWidth / 2), $y, $rgb, $size, $bold);
    }

    private function rotatedLabel(GdImage $im, string $text, int $centerX, int $y): void
    {
        $font = $this->fontPath(false);
        $size = 8;
        $color = $this->rgbColor($im, self::MUTED);
        $box = $this->bbox($size, 45, $font, $text);
        $textWidth = $box[2] - $box[0];

        imagettftext($im, $size, 45, (int) ($centerX - $textWidth / 2), $y + 10, $color, $font, $text);
    }

    private function truncate(string $value, int $length): string
    {
        return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 1).'…' : $value;
    }

    private function fontPath(bool $bold): string
    {
        return base_path('vendor/dompdf/dompdf/lib/fonts/'.($bold ? 'DejaVuSans-Bold.ttf' : 'DejaVuSans.ttf'));
    }

    private function toDataUri(GdImage $im): string
    {
        ob_start();
        imagepng($im);
        $data = ob_get_clean();
        imagedestroy($im);

        return 'data:image/png;base64,'.base64_encode((string) $data);
    }
}
