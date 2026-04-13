<?php

namespace App\Services;

use FPDF;
use App\Models\TenantSetting;

/**
 * OJTPdf — Tenant-branded FPDF extension.
 *
 * All colours, name, and logo are pulled from TenantSetting at construction
 * time so every exported PDF reflects the institution's current branding.
 */
class OJTPdf extends FPDF
{
    // ── Report meta ────────────────────────────────────────────────────────
    public string $reportTitle  = '';
    public string $reportSub    = '';
    public int    $totalRecords = 0;

    // ── Tenant brand ───────────────────────────────────────────────────────
    protected array  $brandRgb      = [140, 14, 3];   // Primary accent (crimson default)
    protected array  $surfaceRgb    = [14, 17, 23];   // Dark background
    protected string $brandName     = 'OJTConnect';
    protected ?string $logoPath     = null;            // Absolute filesystem path

    // ── Constructor: hydrate brand from tenant settings ────────────────────
    public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->_loadTenantBrand();
    }

    protected function _loadTenantBrand(): void
    {
        try {
            $hex     = TenantSetting::get('brand_color')           ?? '8C0E03';
            $hexBg   = TenantSetting::get('brand_color_secondary') ?? '0E1126';
            $name    = TenantSetting::get('brand_name')            ?? 'OJTConnect';
            $logoRel = TenantSetting::get('brand_logo');

            $this->brandRgb   = $this->_hexToRgb($hex);
            $this->surfaceRgb = $this->_hexToRgb($hexBg);
            $this->brandName  = $name ?: 'OJTConnect';

            if ($logoRel) {
                $abs = storage_path('app/public/' . $logoRel);
                if (file_exists($abs)) {
                    $this->logoPath = $abs;
                }
            }
        } catch (\Throwable) {
            // Silently fall back to defaults when running outside tenant context
        }
    }

    protected function _hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) return [140, 14, 3];
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    // ── Helpers: apply brand colours ──────────────────────────────────────
    protected function setBrandFill(float $opacity = 1.0): void
    {
        $this->SetFillColor(...$this->brandRgb);
    }

    protected function setBrandText(): void
    {
        $this->SetTextColor(...$this->brandRgb);
    }

    protected function setSurfaceFill(): void
    {
        $this->SetFillColor(...$this->surfaceRgb);
    }

    // ── Header ────────────────────────────────────────────────────────────
    public function Header()
    {
        [$r, $g, $b] = $this->brandRgb;
        [$sr, $sg, $sb] = $this->surfaceRgb;

        // Brand accent top bar
        $this->SetFillColor($r, $g, $b);
        $this->Rect(0, 0, 297, 3, 'F');

        // Dark header block
        $this->SetFillColor($sr, $sg, $sb);
        $this->Rect(0, 3, 297, 30, 'F');

        // ── Logo or letter circle ─────────────────────────────────────────
        if ($this->logoPath) {
            // Render institution logo in a small square
            try {
                $this->Image($this->logoPath, 10, 5, 22, 22);
            } catch (\Throwable) {
                $this->_drawLogoCircle();
            }
        } else {
            $this->_drawLogoCircle();
        }

        // Brand name
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(36, 6);
        $this->Cell(80, 7, $this->brandName, 0, 0, 'L');

        $this->SetFont('Arial', '', 7.5);
        $this->SetTextColor(156, 163, 175);
        $this->SetXY(36, 14);
        $this->Cell(100, 4.5, 'OJT Management System', 0, 0, 'L');
        $this->SetXY(36, 19.5);
        $this->Cell(100, 4.5, 'Bukidnon State University', 0, 0, 'L');

        // Report title (right-aligned)
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor($r, $g, $b);
        $this->SetXY(130, 7);
        $this->Cell(155, 7, $this->reportTitle, 0, 0, 'R');

        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(156, 163, 175);
        $this->SetXY(130, 15);
        $this->Cell(155, 5, $this->reportSub, 0, 0, 'R');
        $this->SetXY(130, 21);
        $this->Cell(155, 5, 'Generated: ' . now()->format('F d, Y  h:i A'), 0, 0, 'R');

        // Brand accent divider
        $this->SetFillColor($r, $g, $b);
        $this->Rect(0, 33, 297, 0.5, 'F');

        $this->SetY(40);
    }

    protected function _drawLogoCircle(): void
    {
        [$r, $g, $b] = $this->brandRgb;
        $this->SetFillColor($r, $g, $b);
        $this->Ellipse(21, 18, 7.5, 7.5);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(255, 255, 255);
        $letter = strtoupper(substr($this->brandName, 0, 1));
        $this->SetXY(17, 13);
        $this->Cell(8, 8, $letter, 0, 0, 'C');
    }

    // ── Footer ────────────────────────────────────────────────────────────
    public function Footer()
    {
        $this->SetY(-13);
        $this->SetFillColor(229, 231, 235);
        $this->Rect(10, $this->GetY(), 277, 0.3, 'F');
        $this->SetY($this->GetY() + 2);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(156, 163, 175);
        $this->Cell(138, 5, $this->brandName . '  |  Bukidnon State University  |  Records: ' . $this->totalRecords, 0, 0, 'L');
        $this->Cell(139, 5, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
    }

    // ── Ellipse helper ────────────────────────────────────────────────────
    public function Ellipse(float $x, float $y, float $rx, float $ry): void
    {
        $lx = (4 / 3) * (M_SQRT2 - 1) * $rx;
        $ly = (4 / 3) * (M_SQRT2 - 1) * $ry;
        $k  = $this->k;
        $h  = $this->h;
        $xk = $x * $k; $yk = ($h - $y) * $k;
        $rxk = $rx * $k; $ryk = $ry * $k;
        $lxk = $lx * $k; $lyk = $ly * $k;
        $this->_out(sprintf(
            'q %.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c f',
            $xk + $rxk, $yk,
            $xk + $rxk, $yk + $lyk, $xk + $lxk, $yk + $ryk, $xk, $yk + $ryk,
            $xk - $lxk, $yk + $ryk, $xk - $rxk, $yk + $lyk, $xk - $rxk, $yk,
            $xk - $rxk, $yk - $lyk, $xk - $lxk, $yk - $ryk, $xk, $yk - $ryk,
            $xk + $lxk, $yk - $ryk, $xk + $rxk, $yk - $lyk, $xk + $rxk, $yk
        ));
    }

    // ── Section title bar ─────────────────────────────────────────────────
    public function SectionTitle(string $text): void
    {
        [$r, $g, $b] = $this->brandRgb;
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor($r, $g, $b);
        $this->Cell(0, 7, '  ' . strtoupper($text), 0, 1, 'L', true);
        $this->Ln(2);
    }

    // ── Summary box ───────────────────────────────────────────────────────
    public function SummaryBox(
        string $label,
        mixed  $value,
        float  $x,
        float  $y,
        float  $w     = 58,
        array  $color = []
    ): void {
        if (empty($color)) $color = $this->brandRgb;

        $this->SetFillColor(249, 250, 251);
        $this->SetDrawColor(229, 231, 235);
        $this->RoundedRect($x, $y, $w, 17, 2, 'DF');

        $this->SetFillColor($color[0], $color[1], $color[2]);
        $this->Rect($x, $y, 2.5, 17, 'F');

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(107, 114, 128);
        $this->SetXY($x + 5, $y + 2.5);
        $this->Cell($w - 7, 5, $label, 0, 0, 'L');

        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor($color[0], $color[1], $color[2]);
        $this->SetXY($x + 5, $y + 7);
        $this->Cell($w - 7, 8, (string)$value, 0, 0, 'L');
    }

    // ── Rounded rect helper ───────────────────────────────────────────────
    public function RoundedRect(
        float  $x, float $y, float $w, float $h,
        float  $r, string $style = ''
    ): void {
        $op  = match ($style) { 'F' => 'f', 'FD', 'DF' => 'B', default => 'S' };
        $arc = 4 / 3 * (sqrt(2) - 1);
        $k   = $this->k;
        $hp  = $this->h;
        $this->_out(sprintf('q %.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $this->_out(sprintf('%.2F %.2F l', ($x + $w - $r) * $k, ($hp - $y) * $k));
        $this->_Arc($x + $w - $r + $r * $arc, $y - $r, $x + $w, $y - $r + $r * $arc, $x + $w, $y + $r);
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h - $r)) * $k));
        $this->_Arc($x + $w, $y + $h - $r + $r * $arc, $x + $w - $r + $r * $arc, $y + $h, $x + $w - $r, $y + $h);
        $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($x + $r - $r * $arc, $y + $h, $x, $y + $h - $r + $r * $arc, $x, $y + $h - $r);
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - ($y + $r)) * $k));
        $this->_Arc($x, $y + $r - $r * $arc, $x + $r - $r * $arc, $y, $x + $r, $y);
        $this->_out($op);
    }

    public function _Arc(
        float $x1, float $y1, float $x2, float $y2,
        float $x3, float $y3
    ): void {
        $h = $this->h;
        $k = $this->k;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $k, ($h - $y1) * $k,
            $x2 * $k, ($h - $y2) * $k,
            $x3 * $k, ($h - $y3) * $k
        ));
    }

    // ── Table header row ──────────────────────────────────────────────────
    /**
     * Render a styled column header row.
     * $cols: array of [label, width, align]
     */
    public function TableHeader(array $cols): void
    {
        [$r, $g, $b] = $this->surfaceRgb;
        $this->SetFillColor($r, $g, $b);
        $this->SetTextColor(...$this->brandRgb);
        $this->SetFont('Arial', 'B', 8);
        foreach ($cols as [$label, $width, $align]) {
            $this->Cell($width, 8, $label, 0, 0, $align, true);
        }
        $this->Ln();
    }

    // ── Table row (alternating) ────────────────────────────────────────────
    /**
     * Set the fill and text color for an alternating data row.
     * Returns the fill flag for use in Cell() calls.
     */
    public function TableRowStart(bool $fill): bool
    {
        if ($fill) {
            $this->SetFillColor(247, 248, 249);
        } else {
            $this->SetFillColor(255, 255, 255);
        }
        $this->SetTextColor(55, 65, 81);
        $this->SetDrawColor(235, 237, 240);
        $this->SetFont('Arial', '', 7.5);
        return $fill;
    }

    // ─────────────────────────────────────────────────────────────────────
    // STATIC FACTORY: Certificate
    // ─────────────────────────────────────────────────────────────────────
    public static function certificate(
        string $studentName,
        string $program,
        string $companyName,
        string $semester,
        string $schoolYear,
        float  $hoursCompleted,
        string $coordinatorName = 'OJT Coordinator'
    ): self {
        $pdf = new self('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->reportTitle  = 'Certificate of Completion';
        $pdf->reportSub    = '';
        $pdf->totalRecords = 1;
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        [$r, $g, $b] = $pdf->brandRgb;
        [$sr, $sg, $sb] = $pdf->surfaceRgb;

        // ── Background ───────────────────────────────────────────────────
        $pdf->SetFillColor($sr, $sg, $sb);
        $pdf->Rect(0, 0, 297, 210, 'F');

        // ── Outer border ─────────────────────────────────────────────────
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->SetLineWidth(1.5);
        $pdf->Rect(10, 10, 277, 190);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(13, 13, 271, 184);

        // ── Logo or initial ───────────────────────────────────────────────
        if ($pdf->logoPath) {
            try {
                $pdf->Image($pdf->logoPath, 137, 18, 22, 22);
            } catch (\Throwable) {
                $pdf->_drawCertLogo();
            }
        } else {
            $pdf->_drawCertLogo();
        }

        // ── Brand name ───────────────────────────────────────────────────
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetXY(0, 22);
        $pdf->Cell(297, 8, strtoupper($pdf->brandName), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 30);
        $pdf->Cell(297, 5, 'OJT Management System — Bukidnon State University', 0, 1, 'C');

        // ── Decorative divider ───────────────────────────────────────────
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect(88, 37, 121, 0.5, 'F');

        // ── Certificate title ────────────────────────────────────────────
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 42);
        $pdf->Cell(297, 6, 'THIS IS TO CERTIFY THAT', 0, 1, 'C');

        // ── Student name ─────────────────────────────────────────────────
        $pdf->SetFont('Arial', 'B', 28);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(0, 50);
        $pdf->Cell(297, 16, $studentName, 0, 1, 'C');

        // Name underline (brand colour)
        $pdf->SetFillColor($r, $g, $b);
        $nameWidth = $pdf->GetStringWidth($studentName) + 20;
        $pdf->Rect((297 - $nameWidth) / 2, 67, $nameWidth, 0.8, 'F');

        // ── Body text ────────────────────────────────────────────────────
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(200, 203, 210);
        $pdf->SetXY(0, 73);
        $pdf->Cell(297, 7, 'has successfully completed the On-the-Job Training program', 0, 1, 'C');

        $pdf->SetXY(0, 80);
        $pdf->Cell(297, 7, 'enrolled under ' . $program . ' — ' . $semester . ', S.Y. ' . $schoolYear, 0, 1, 'C');

        // ── Company highlight box ─────────────────────────────────────────
        $pdf->SetFillColor(25, 28, 38);
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->SetLineWidth(0.4);
        $pdf->RoundedRect(74, 90, 149, 14, 2, 'DF');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 92);
        $pdf->Cell(297, 4, 'TRAINING ESTABLISHMENT', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetXY(0, 97);
        $pdf->Cell(297, 6, $companyName, 0, 1, 'C');

        // ── Hours completed ───────────────────────────────────────────────
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(200, 203, 210);
        $pdf->SetXY(0, 108);
        $pdf->Cell(297, 6, 'having completed a total of', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(45, 212, 191);
        $pdf->SetXY(0, 114);
        $pdf->Cell(297, 10, number_format($hoursCompleted, 1) . ' HOURS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 124);
        $pdf->Cell(297, 5, 'of supervised on-the-job training', 0, 1, 'C');

        // ── Decorative divider ───────────────────────────────────────────
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect(88, 132, 121, 0.3, 'F');

        // ── Issue date ────────────────────────────────────────────────────
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 136);
        $pdf->Cell(297, 5, 'Issued on ' . now()->format('F d, Y'), 0, 1, 'C');

        // ── Signature ────────────────────────────────────────────────────
        $pdf->SetDrawColor($r, $g, $b);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(99, 162, 198, 162);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(0, 164);
        $pdf->Cell(297, 5, $coordinatorName, 0, 1, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 170);
        $pdf->Cell(297, 4, 'OJT COORDINATOR', 0, 1, 'C');

        // ── Bottom brand strip ────────────────────────────────────────────
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Rect(0, 195, 297, 3, 'F');

        return $pdf;
    }

    protected function _drawCertLogo(): void
    {
        [$r, $g, $b] = $this->brandRgb;
        $this->SetFillColor($r, $g, $b);
        $this->Ellipse(148.5, 29, 8, 8);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(255, 255, 255);
        $letter = strtoupper(substr($this->brandName, 0, 1));
        $this->SetXY(144, 24);
        $this->Cell(9, 9, $letter, 0, 0, 'C');
    }
}