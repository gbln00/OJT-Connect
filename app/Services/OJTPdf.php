<?php

namespace App\Services;

use FPDF;

// ── Extended FPDF with branded header/footer ──────────────────────
class OJTPdf extends FPDF
{
    public string $reportTitle  = '';
    public string $reportSub    = '';
    public int    $totalRecords = 0;

    public function Header()
    {
        // Gold top bar
        $this->SetFillColor(240, 180, 41);
        $this->Rect(0, 0, 297, 3, 'F');

        // Dark header block
        $this->SetFillColor(15, 17, 23);
        $this->Rect(0, 3, 297, 30, 'F');

        // Logo circle
        $this->SetFillColor(240, 180, 41);
        $this->Ellipse(18, 18, 7, 7);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(15, 17, 23);
        $this->SetXY(14, 13);
        $this->Cell(8, 8, 'O', 0, 0, 'C');

        // Brand name
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(28, 6);
        $this->Cell(70, 7, 'OJTConnect', 0, 0, 'L');

        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(156, 163, 175);
        $this->SetXY(28, 14);
        $this->Cell(100, 5, 'OJT Management System', 0, 0, 'L');
        $this->SetXY(28, 20);
        $this->Cell(100, 5, 'Bukidnon State University', 0, 0, 'L');

        // Report title (right)
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(240, 180, 41);
        $this->SetXY(130, 7);
        $this->Cell(155, 7, $this->reportTitle, 0, 0, 'R');

        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(156, 163, 175);
        $this->SetXY(130, 15);
        $this->Cell(155, 5, $this->reportSub, 0, 0, 'R');
        $this->SetXY(130, 21);
        $this->Cell(155, 5, 'Generated: ' . now()->format('F d, Y  h:i A'), 0, 0, 'R');

        // Gold divider line
        $this->SetFillColor(240, 180, 41);
        $this->Rect(0, 33, 297, 0.5, 'F');

        $this->SetY(40);
    }

    public function Footer()
    {
        $this->SetY(-13);
        $this->SetFillColor(229, 231, 235);
        $this->Rect(10, $this->GetY(), 277, 0.3, 'F');
        $this->SetY($this->GetY() + 2);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(156, 163, 175);
        $this->Cell(138, 5, 'OJTConnect  |  Bukidnon State University  |  Records: ' . $this->totalRecords, 0, 0, 'L');
        $this->Cell(139, 5, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
    }

    public function Ellipse($x, $y, $rx, $ry)
    {
        $lx = (4/3) * (M_SQRT2 - 1) * $rx;
        $ly = (4/3) * (M_SQRT2 - 1) * $ry;
        $k  = $this->k;
        $h  = $this->h;
        $xk = $x * $k; $yk = ($h - $y) * $k;
        $rxk = $rx * $k; $ryk = $ry * $k;
        $lxk = $lx * $k; $lyk = $ly * $k;
        $this->_out(sprintf(
            'q %.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c f',
            $xk+$rxk,$yk,
            $xk+$rxk,$yk+$lyk,$xk+$lxk,$yk+$ryk,$xk,$yk+$ryk,
            $xk-$lxk,$yk+$ryk,$xk-$rxk,$yk+$lyk,$xk-$rxk,$yk,
            $xk-$rxk,$yk-$lyk,$xk-$lxk,$yk-$ryk,$xk,$yk-$ryk,
            $xk+$lxk,$yk-$ryk,$xk+$rxk,$yk-$lyk,$xk+$rxk,$yk
        ));
    }

    public function SectionTitle($text)
    {
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetTextColor(15, 17, 23);
        $this->SetFillColor(240, 180, 41);
        $this->Cell(0, 7, '  ' . strtoupper($text), 0, 1, 'L', true);
        $this->Ln(2);
    }

    public function SummaryBox($label, $value, $x, $y, $w = 58, $color = [240, 180, 41])
    {
        // Box background
        $this->SetFillColor(249, 250, 251);
        $this->SetDrawColor(229, 231, 235);
        $this->RoundedRect($x, $y, $w, 17, 2, 'DF');

        // Left color accent
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

    public function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $op = match($style) { 'F' => 'f', 'FD', 'DF' => 'B', default => 'S' };
        $arc = 4/3*(sqrt(2)-1);
        $k = $this->k; $hp = $this->h;
        $this->_out(sprintf('q %.2F %.2F m', ($x+$r)*$k, ($hp-$y)*$k));
        $this->_out(sprintf('%.2F %.2F l', ($x+$w-$r)*$k, ($hp-$y)*$k));
        $this->_Arc($x+$w-$r+$r*$arc,$y-$r,$x+$w,$y-$r+$r*$arc,$x+$w,$y+$r);
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-($y+$h-$r))*$k));
        $this->_Arc($x+$w,$y+$h-$r+$r*$arc,$x+$w-$r+$r*$arc,$y+$h,$x+$w-$r,$y+$h);
        $this->_out(sprintf('%.2F %.2F l', ($x+$r)*$k, ($hp-($y+$h))*$k));
        $this->_Arc($x+$r-$r*$arc,$y+$h,$x,$y+$h-$r+$r*$arc,$x,$y+$h-$r);
        $this->_out(sprintf('%.2F %.2F l', $x*$k, ($hp-($y+$r))*$k));
        $this->_Arc($x,$y+$r-$r*$arc,$x+$r-$r*$arc,$y,$x+$r,$y);
        $this->_out($op);
    }

    public function _Arc($x1,$y1,$x2,$y2,$x3,$y3)
    {
        $h = $this->h; $k = $this->k;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1*$k,($h-$y1)*$k,$x2*$k,($h-$y2)*$k,$x3*$k,($h-$y3)*$k));
    }
    
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

        // Background
        $pdf->SetFillColor(15, 17, 23);
        $pdf->Rect(0, 0, 297, 210, 'F');

        // Gold border frame
        $pdf->SetDrawColor(240, 180, 41);
        $pdf->SetLineWidth(1.5);
        $pdf->Rect(10, 10, 277, 190);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(13, 13, 271, 184);

        // Header brand
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(240, 180, 41);
        $pdf->SetXY(0, 22);
        $pdf->Cell(297, 8, 'OJTCONNECT', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 30);
        $pdf->Cell(297, 5, 'OJT Management System — Bukidnon State University', 0, 1, 'C');

        // Decorative line
        $pdf->SetFillColor(240, 180, 41);
        $pdf->Rect(88, 37, 121, 0.5, 'F');

        // Title
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 42);
        $pdf->Cell(297, 6, 'THIS IS TO CERTIFY THAT', 0, 1, 'C');

        // Student Name
        $pdf->SetFont('Arial', 'B', 28);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(0, 50);
        $pdf->Cell(297, 16, $studentName, 0, 1, 'C');

        // Gold underline
        $pdf->SetFillColor(240, 180, 41);
        $nameWidth = $pdf->GetStringWidth($studentName) + 20;
        $pdf->Rect((297 - $nameWidth) / 2, 67, $nameWidth, 0.8, 'F');

        // Body text
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(200, 203, 210);
        $pdf->SetXY(0, 73);
        $pdf->Cell(297, 7, 'has successfully completed the On-the-Job Training program', 0, 1, 'C');

        $pdf->SetXY(0, 80);
        $pdf->Cell(297, 7, 'enrolled under ' . $program . ' — ' . $semester . ', S.Y. ' . $schoolYear, 0, 1, 'C');

        // Company highlight box
        $pdf->SetFillColor(25, 28, 38);
        $pdf->SetDrawColor(240, 180, 41);
        $pdf->SetLineWidth(0.4);
        $pdf->RoundedRect(74, 90, 149, 14, 2, 'DF');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 92);
        $pdf->Cell(297, 4, 'TRAINING ESTABLISHMENT', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(240, 180, 41);
        $pdf->SetXY(0, 97);
        $pdf->Cell(297, 6, $companyName, 0, 1, 'C');

        // Hours completed
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

        // Decorative divider
        $pdf->SetFillColor(240, 180, 41);
        $pdf->Rect(88, 132, 121, 0.3, 'F');

        // Date
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->SetXY(0, 136);
        $pdf->Cell(297, 5, 'Issued on ' . now()->format('F d, Y'), 0, 1, 'C');

        // Signature line
        $pdf->SetDrawColor(240, 180, 41);
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

        // Bottom brand strip
        $pdf->SetFillColor(240, 180, 41);
        $pdf->Rect(0, 195, 297, 3, 'F');

        return $pdf;
    }
}
