<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OjtApplication;
use App\Models\HourLog;
use App\Models\WeeklyReport;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Company;
use App\Models\TenantSetting;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color as XlsColor;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Services\OJTPdf;

class ExportController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────

    /** Return the tenant's primary brand colour as a 6-char hex (no #). */
    private function brandHex(): string
    {
        return TenantSetting::get('brand_color') ?? '8C0E03';
    }

    /** Return the tenant's brand name. */
    private function brandName(): string
    {
        return TenantSetting::get('brand_name') ?? 'OJTConnect';
    }

    /** Return the tenant logo absolute path (or null). */
    private function logoPath(): ?string
    {
        $rel = TenantSetting::get('brand_logo');
        if (!$rel) return null;
        $abs = storage_path('app/public/' . $rel);
        return file_exists($abs) ? $abs : null;
    }

    // ─────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────

    public function index()
    {
        $stats = [
            'total_students'     => User::where('role', 'student_intern')->count(),
            'total_companies'    => Company::count(),
            'total_applications' => OjtApplication::count(),
            'total_hours'        => HourLog::where('status', 'approved')->sum('total_hours'),
            'total_reports'      => WeeklyReport::count(),
            'total_evaluations'  => Evaluation::count(),
        ];

        return view('admin.exports.index', compact('stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // PDF — Student Summary
    // ─────────────────────────────────────────────────────────────────────

    public function pdfStudents()
    {
        $semester   = request('semester');
        $schoolYear = request('school_year');

        $applications = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved')
            ->when($semester,   fn($q) => $q->where('semester', $semester))
            ->when($schoolYear, fn($q) => $q->where('school_year', $schoolYear))
            ->get();

        $pdf = new OJTPdf('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->reportTitle  = 'Student OJT Summary';
        $pdf->reportSub    = 'Approved interns with hour progress';
        $pdf->totalRecords = $applications->count();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();

        // ── Summary boxes ─────────────────────────────────────────────────
        $brandRgb = $this->_hexToRgbArr($this->brandHex());
        $pdf->SummaryBox('Total Students',   $applications->count(),    10,  42);
        $pdf->SummaryBox('Approved Hours',   number_format(HourLog::where('status', 'approved')->sum('total_hours'), 1), 72,  42, 58, [45, 212, 191]);
        $pdf->SummaryBox('Companies',        Company::where('is_active', true)->count(), 134, 42, 58, [96, 165, 250]);
        $pdf->SummaryBox('Report Date',      now()->format('M d, Y'),   196, 42, 58, [156, 163, 175]);
        $pdf->Ln(26);

        // ── Filter info bar (when filters applied) ────────────────────────
        if ($semester || $schoolYear) {
            $filterLabel = implode('  ·  ', array_filter([$semester, $schoolYear]));
            $pdf->SetFont('Arial', 'I', 7.5);
            $pdf->SetTextColor(107, 114, 128);
            $pdf->SetFillColor(249, 250, 251);
            $pdf->SetDrawColor(229, 231, 235);
            $pdf->Cell(0, 6, '  Filtered by: ' . $filterLabel, 'B', 1, 'L', true);
            $pdf->Ln(3);
        }

        $pdf->SectionTitle('Intern Records');

        $cols = [
            ['#',          9,  'C'],
            ['Student Name', 50, 'L'],
            ['Email',      52, 'L'],
            ['Company',    46, 'L'],
            ['Program',    30, 'C'],
            ['Semester',   22, 'C'],
            ['Req. Hrs',   22, 'C'],
            ['Appr. Hrs',  22, 'C'],
            ['Progress',   30, 'C'],
        ];

        $pdf->TableHeader($cols);

        $fill = false;

        foreach ($applications as $i => $app) {
            $approved = HourLog::where('application_id', $app->id)
                ->where('status', 'approved')
                ->sum('total_hours');
            $pct = $app->required_hours > 0
                ? min(100, round(($approved / $app->required_hours) * 100))
                : 0;

            $pdf->TableRowStart($fill);
            $rowY = $pdf->GetY();

            $pdf->Cell(9,  7, $i + 1,                              'B', 0, 'C', $fill);
            $pdf->Cell(50, 7, $app->student->name,                 'B', 0, 'L', $fill);
            $pdf->Cell(52, 7, $app->student->email,                'B', 0, 'L', $fill);
            $pdf->Cell(46, 7, $app->company->name,                 'B', 0, 'L', $fill);
            $pdf->Cell(30, 7, $app->program,                       'B', 0, 'C', $fill);
            $pdf->Cell(22, 7, $app->semester,                      'B', 0, 'C', $fill);
            $pdf->Cell(22, 7, number_format($app->required_hours), 'B', 0, 'C', $fill);
            $pdf->Cell(22, 7, number_format($approved, 1),         'B', 0, 'C', $fill);

            $barCellX = $pdf->GetX();
            $pdf->Cell(30, 7, '', 'B', 0, 'C', $fill);

            // Progress bar
            $bx = $barCellX + 2; $by = $rowY + 2.2; $bw = 26; $bh = 2.8;
            $pdf->SetFillColor(220, 222, 226);
            $pdf->Rect($bx, $by, $bw, $bh, 'F');
            $fw = max(0, ($pct / 100) * $bw);
            if ($pct >= 100)    $pdf->SetFillColor(45, 212, 191);
            elseif ($pct >= 50) $pdf->SetFillColor(96, 165, 250);
            else                $pdf->SetFillColor(...$brandRgb);
            if ($fw > 0) $pdf->Rect($bx, $by, $fw, $bh, 'F');
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetTextColor(107, 114, 128);
            $pdf->SetXY($bx, $by + 3.2);
            $pdf->Cell($bw, 3, $pct . '%', 0, 0, 'C');

            $pdf->SetFont('Arial', '', 7.5);
            $pdf->Ln();
            $fill = !$fill;
        }

        // Empty state
        if ($applications->isEmpty()) {
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->SetTextColor(156, 163, 175);
            $pdf->Cell(0, 14, 'No approved applications found for the selected filters.', 0, 1, 'C');
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ojt_students_' . now()->format('Ymd') . '.pdf"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PDF — Evaluations
    // ─────────────────────────────────────────────────────────────────────

    public function pdfEvaluations()
    {
        $semester   = request('semester');
        $schoolYear = request('school_year');

        $evaluations = Evaluation::with(['student', 'application.company', 'supervisor'])
            ->whereHas('application', function ($q) use ($semester, $schoolYear) {
                $q->when($semester,   fn($q) => $q->where('semester', $semester))
                  ->when($schoolYear, fn($q) => $q->where('school_year', $schoolYear));
            })
            ->get();

        $passed   = $evaluations->where('recommendation', 'pass')->count();
        $avgGrade = round($evaluations->avg('overall_grade') ?? 0, 1);

        $pdf = new OJTPdf('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->reportTitle  = 'Evaluations Summary';
        $pdf->reportSub    = 'Student performance ratings and grades';
        $pdf->totalRecords = $evaluations->count();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();

        $pdf->SummaryBox('Total Evaluations', $evaluations->count(),           10,  42);
        $pdf->SummaryBox('Passed',            $passed,                          72,  42, 58, [45, 212, 191]);
        $pdf->SummaryBox('Failed',            $evaluations->count() - $passed, 134,  42, 58, [248, 113, 113]);
        $pdf->SummaryBox('Average Grade',     $avgGrade,                       196,  42, 58, [96, 165, 250]);
        $pdf->Ln(26);

        if ($semester || $schoolYear) {
            $filterLabel = implode('  ·  ', array_filter([$semester, $schoolYear]));
            $pdf->SetFont('Arial', 'I', 7.5);
            $pdf->SetTextColor(107, 114, 128);
            $pdf->SetFillColor(249, 250, 251);
            $pdf->SetDrawColor(229, 231, 235);
            $pdf->Cell(0, 6, '  Filtered by: ' . $filterLabel, 'B', 1, 'L', true);
            $pdf->Ln(3);
        }

        $pdf->SectionTitle('Evaluation Records');

        $cols = [
            ['#',            9,  'C'],
            ['Student Name', 50, 'L'],
            ['Company',      46, 'L'],
            ['Supervisor',   42, 'L'],
            ['Attendance',   26, 'C'],
            ['Performance',  26, 'C'],
            ['Grade',        22, 'C'],
            ['Result',       24, 'C'],
            ['Rating',       28, 'C'],
        ];

        $pdf->TableHeader($cols);

        $fill = false;

        foreach ($evaluations as $i => $eval) {
            $avg = ($eval->attendance_rating + $eval->performance_rating) / 2;
            $rl  = match (true) {
                $avg >= 5 => 'Excellent',
                $avg >= 4 => 'Very Good',
                $avg >= 3 => 'Good',
                $avg >= 2 => 'Fair',
                default   => 'Poor',
            };

            $pdf->TableRowStart($fill);

            $pdf->Cell(9,  7, $i + 1,                              'B', 0, 'C', $fill);
            $pdf->Cell(50, 7, $eval->student->name,                'B', 0, 'L', $fill);
            $pdf->Cell(46, 7, $eval->application->company->name,   'B', 0, 'L', $fill);
            $pdf->Cell(42, 7, $eval->supervisor->name,             'B', 0, 'L', $fill);
            $pdf->Cell(26, 7, $eval->attendance_rating . '/5',     'B', 0, 'C', $fill);
            $pdf->Cell(26, 7, $eval->performance_rating . '/5',    'B', 0, 'C', $fill);

            // Grade — colour-coded
            if ($eval->overall_grade >= 90)     $pdf->SetTextColor(45,  212, 191);
            elseif ($eval->overall_grade >= 75) $pdf->SetTextColor(96,  165, 250);
            elseif ($eval->overall_grade >= 60) $pdf->SetTextColor(240, 180,  41);
            else                                $pdf->SetTextColor(248, 113, 113);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(22, 7, number_format($eval->overall_grade, 1), 'B', 0, 'C', $fill);

            // Result — pass/fail coloured
            $pass = $eval->recommendation === 'pass';
            $pdf->SetFont('Arial', 'B', 7.5);
            $pdf->SetTextColor($pass ? 45 : 248, $pass ? 212 : 113, $pass ? 191 : 113);
            $pdf->Cell(24, 7, ucfirst($eval->recommendation), 'B', 0, 'C', $fill);

            $pdf->SetFont('Arial', '', 7.5);
            $pdf->SetTextColor(107, 114, 128);
            $pdf->Cell(28, 7, $rl, 'B', 1, 'C', $fill);

            $fill = !$fill;
        }

        if ($evaluations->isEmpty()) {
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->SetTextColor(156, 163, 175);
            $pdf->Cell(0, 14, 'No evaluations found for the selected filters.', 0, 1, 'C');
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ojt_evaluations_' . now()->format('Ymd') . '.pdf"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // EXCEL — Full Report  (tenant-branded)
    // ─────────────────────────────────────────────────────────────────────

    public function excelFull()
    {
        $brandHex  = strtoupper($this->brandHex());
        $brandName = $this->brandName();
        $semester  = request('semester');
        $schoolYear = request('school_year');

        // ── Shared style definitions ──────────────────────────────────────
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $brandHex],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => false,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $oddRowStyle = [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F9FAFB'],
            ],
        ];

        $evenRowStyle = [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFFFF'],
            ],
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle($brandName . ' — OJT Full Report')
            ->setSubject('OJT Report')
            ->setDescription('Generated by OJTConnect on ' . now()->toDateTimeString())
            ->setCreator($brandName)
            ->setCompany($brandName);

        // ─────────────────────────────────────────────────────────────────
        // Sheet 1: Students
        // ─────────────────────────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Students');

        // Title row
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $brandName . ' — Student OJT Summary');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $brandHex]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Meta row
        $sheet->mergeCells('A2:J2');
        $filterText = '';
        if ($semester || $schoolYear) {
            $filterText = ' | Filtered by: ' . implode(', ', array_filter([$semester, $schoolYear]));
        }
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('F d, Y h:i A') . $filterText);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '9CA3AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(14);

        // Column headers (row 3)
        $headers = ['#', 'Name', 'Email', 'Company', 'Program', 'School Year', 'Semester', 'Required Hours', 'Approved Hours', 'Progress %'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '3', $h);
            $sheet->getStyle($col . '3')->applyFromArray($headerStyle);
        }
        $sheet->getRowDimension(3)->setRowHeight(22);

        // Data
        $applications = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved')
            ->when($semester,    fn($q) => $q->where('semester', $semester))
            ->when($schoolYear,  fn($q) => $q->where('school_year', $schoolYear))
            ->get();

        foreach ($applications as $i => $app) {
            $row      = $i + 4;
            $approved = HourLog::where('application_id', $app->id)->where('status', 'approved')->sum('total_hours');
            $pct      = $app->required_hours > 0 ? min(100, round(($approved / $app->required_hours) * 100)) : 0;

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $app->student->name);
            $sheet->setCellValue('C' . $row, $app->student->email);
            $sheet->setCellValue('D' . $row, $app->company->name);
            $sheet->setCellValue('E' . $row, $app->program);
            $sheet->setCellValue('F' . $row, $app->school_year);
            $sheet->setCellValue('G' . $row, $app->semester);
            $sheet->setCellValue('H' . $row, $app->required_hours);
            $sheet->setCellValue('I' . $row, round($approved, 1));
            $sheet->setCellValue('J' . $row, $pct . '%');

            $rowStyle = $i % 2 === 0 ? $oddRowStyle : $evenRowStyle;
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($rowStyle);

            // Colour-code progress column
            if ($pct >= 100)     $pctColor = '2DD4BF';
            elseif ($pct >= 50)  $pctColor = '60A5FA';
            else                 $pctColor = $brandHex;
            $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB($pctColor);
            $sheet->getStyle('J' . $row)->getFont()->setBold(true);

            $sheet->getRowDimension($row)->setRowHeight(18);
        }

        foreach (range('A', 'J') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(32);

        // ─────────────────────────────────────────────────────────────────
        // Sheet 2: Evaluations
        // ─────────────────────────────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet()->setTitle('Evaluations');

        $sheet2->mergeCells('A1:J1');
        $sheet2->setCellValue('A1', $brandName . ' — Evaluations Summary');
        $sheet2->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $brandHex]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet2->getRowDimension(1)->setRowHeight(28);
        $sheet2->mergeCells('A2:J2');
        $sheet2->setCellValue('A2', 'Generated: ' . now()->format('F d, Y h:i A') . $filterText);
        $sheet2->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '9CA3AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet2->getRowDimension(2)->setRowHeight(14);

        $evalHeaders = ['#', 'Student', 'Email', 'Company', 'Supervisor', 'Attendance (1-5)', 'Performance (1-5)', 'Overall Grade', 'Recommendation', 'Rating'];
        foreach ($evalHeaders as $i => $h) {
            $col = chr(65 + $i);
            $sheet2->setCellValue($col . '3', $h);
            $sheet2->getStyle($col . '3')->applyFromArray($headerStyle);
        }
        $sheet2->getRowDimension(3)->setRowHeight(22);

        $evaluations = Evaluation::with(['student', 'application.company', 'supervisor'])->get();

        foreach ($evaluations as $i => $eval) {
            $row = $i + 4;
            $avg = ($eval->attendance_rating + $eval->performance_rating) / 2;
            $rl  = match (true) {
                $avg >= 5 => 'Excellent',
                $avg >= 4 => 'Very Good',
                $avg >= 3 => 'Good',
                $avg >= 2 => 'Fair',
                default   => 'Poor',
            };

            $sheet2->setCellValue('A' . $row, $i + 1);
            $sheet2->setCellValue('B' . $row, $eval->student->name);
            $sheet2->setCellValue('C' . $row, $eval->student->email);
            $sheet2->setCellValue('D' . $row, $eval->application->company->name);
            $sheet2->setCellValue('E' . $row, $eval->supervisor->name);
            $sheet2->setCellValue('F' . $row, $eval->attendance_rating);
            $sheet2->setCellValue('G' . $row, $eval->performance_rating);
            $sheet2->setCellValue('H' . $row, round($eval->overall_grade, 1));
            $sheet2->setCellValue('I' . $row, ucfirst($eval->recommendation));
            $sheet2->setCellValue('J' . $row, $rl);

            $rowStyle = $i % 2 === 0 ? $oddRowStyle : $evenRowStyle;
            $sheet2->getStyle('A' . $row . ':J' . $row)->applyFromArray($rowStyle);

            // Grade colour
            $grade = $eval->overall_grade;
            if ($grade >= 90)     $gradeColor = '2DD4BF';
            elseif ($grade >= 75) $gradeColor = '60A5FA';
            elseif ($grade >= 60) $gradeColor = 'F0B429';
            else                  $gradeColor = 'F87171';
            $sheet2->getStyle('H' . $row)->getFont()->getColor()->setRGB($gradeColor);
            $sheet2->getStyle('H' . $row)->getFont()->setBold(true);

            // Recommendation colour
            $passColor = $eval->recommendation === 'pass' ? '2DD4BF' : 'F87171';
            $sheet2->getStyle('I' . $row)->getFont()->getColor()->setRGB($passColor);
            $sheet2->getStyle('I' . $row)->getFont()->setBold(true);

            $sheet2->getRowDimension($row)->setRowHeight(18);
        }

        foreach (range('A', 'J') as $col) $sheet2->getColumnDimension($col)->setAutoSize(true);
        $sheet2->getColumnDimension('B')->setWidth(28);
        $sheet2->getColumnDimension('C')->setWidth(32);

        // ─────────────────────────────────────────────────────────────────
        // Sheet 3: Hour Logs
        // ─────────────────────────────────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet()->setTitle('Hour Logs');

        $sheet3->mergeCells('A1:H1');
        $sheet3->setCellValue('A1', $brandName . ' — Hour Logs');
        $sheet3->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $brandHex]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet3->getRowDimension(1)->setRowHeight(28);
        $sheet3->mergeCells('A2:H2');
        $sheet3->setCellValue('A2', 'Generated: ' . now()->format('F d, Y h:i A'));
        $sheet3->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '9CA3AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet3->getRowDimension(2)->setRowHeight(14);

        $logHeaders = ['#', 'Student', 'Date', 'Time In', 'Time Out', 'Total Hours', 'Description', 'Status'];
        foreach ($logHeaders as $i => $h) {
            $col = chr(65 + $i);
            $sheet3->setCellValue($col . '3', $h);
            $sheet3->getStyle($col . '3')->applyFromArray($headerStyle);
        }
        $sheet3->getRowDimension(3)->setRowHeight(22);

        $logs = HourLog::with('student')->orderBy('date', 'desc')->get();

        foreach ($logs as $i => $log) {
            $row = $i + 4;
            $sheet3->setCellValue('A' . $row, $i + 1);
            $sheet3->setCellValue('B' . $row, $log->student->name);
            $sheet3->setCellValue('C' . $row, $log->date->format('M d, Y'));
            $sheet3->setCellValue('D' . $row, \Carbon\Carbon::parse($log->time_in)->format('h:i A'));
            $sheet3->setCellValue('E' . $row, \Carbon\Carbon::parse($log->time_out)->format('h:i A'));
            $sheet3->setCellValue('F' . $row, round($log->total_hours, 1));
            $sheet3->setCellValue('G' . $row, $log->description ?? '');
            $sheet3->setCellValue('H' . $row, ucfirst($log->status));

            $rowStyle = $i % 2 === 0 ? $oddRowStyle : $evenRowStyle;
            $sheet3->getStyle('A' . $row . ':H' . $row)->applyFromArray($rowStyle);

            // Status colour
            $statusColor = match ($log->status) {
                'approved' => '2DD4BF',
                'pending'  => 'F0B429',
                'rejected' => 'F87171',
                default    => '9CA3AF',
            };
            $sheet3->getStyle('H' . $row)->getFont()->getColor()->setRGB($statusColor);
            $sheet3->getStyle('H' . $row)->getFont()->setBold(true);

            $sheet3->getRowDimension($row)->setRowHeight(18);
        }

        foreach (range('A', 'H') as $col) $sheet3->getColumnDimension($col)->setAutoSize(true);
        $sheet3->getColumnDimension('B')->setWidth(28);
        $sheet3->getColumnDimension('G')->setWidth(36);

        // ── Activate first sheet and output ───────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);

        $writer   = new Xlsx($spreadsheet);
        $filename = 'ojt_full_report_' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PDF — Certificate
    // ─────────────────────────────────────────────────────────────────────

    public function certificate(OjtApplication $application)
    {
        $application->load(['student', 'company', 'evaluation']);

        if (!$application->evaluation || $application->evaluation->recommendation !== 'pass') {
            return back()->with('error', 'Certificate is only available for students with a passing evaluation.');
        }

        $approvedHours  = HourLog::where('application_id', $application->id)
            ->where('status', 'approved')
            ->sum('total_hours');

        $coordinator = User::where('role', 'ojt_coordinator')->first();

        $pdf = OJTPdf::certificate(
            studentName:     $application->student->name,
            program:         $application->program,
            companyName:     $application->company->name,
            semester:        $application->semester,
            schoolYear:      $application->school_year,
            hoursCompleted:  $approvedHours,
            coordinatorName: $coordinator?->name ?? 'OJT Coordinator',
        );

        $filename = 'certificate_' . str_replace(' ', '_', $application->student->name) . '_' . now()->format('Ymd') . '.pdf';

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────

    private function _hexToRgbArr(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}