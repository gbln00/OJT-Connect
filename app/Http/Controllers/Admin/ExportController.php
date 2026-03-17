<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OjtApplication;
use App\Models\HourLog;
use App\Models\WeeklyReport;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use FPDF;

class ExportController extends Controller
{
    // ── Export page ───────────────────────────────────────────────

    public function index()
    {
        $stats = [
            'total_students'  => User::where('role', 'student_intern')->count(),
            'total_companies' => \App\Models\Company::count(),
            'total_applications' => OjtApplication::count(),
            'total_hours'     => HourLog::where('status', 'approved')->sum('total_hours'),
            'total_reports'   => WeeklyReport::count(),
            'total_evaluations' => Evaluation::count(),
        ];

        return view('admin.exports.index', compact('stats'));
    }

    // ── PDF: Student OJT Summary ──────────────────────────────────

    public function pdfStudents(Request $request)
    {
        $applications = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved')
            ->get();

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetMargins(12, 12, 12);

        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(15, 17, 23);
        $pdf->Cell(0, 10, 'OJTConnect - Student OJT Summary', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(107, 114, 128);
        $pdf->Cell(0, 6, 'Bukidnon State University  |  Generated: ' . now()->format('F d, Y'), 0, 1, 'C');
        $pdf->Ln(4);

        // Table header
        $pdf->SetFillColor(15, 17, 23);
        $pdf->SetTextColor(240, 180, 41);
        $pdf->SetFont('Arial', 'B', 9);

        $cols = [
            ['#',           10],
            ['Student Name', 55],
            ['Email',        60],
            ['Company',      55],
            ['Program',      35],
            ['Semester',     25],
            ['Req. Hours',   25],
            ['Approved Hrs', 27],
        ];

        foreach ($cols as [$label, $width]) {
            $pdf->Cell($width, 8, $label, 0, 0, 'C', true);
        }
        $pdf->Ln();

        // Table rows
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(55, 65, 81);
        $fill = false;

        foreach ($applications as $i => $app) {
            $approvedHours = HourLog::where('application_id', $app->id)
                ->where('status', 'approved')
                ->sum('total_hours');

            $pdf->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 251 : 255);

            $pdf->Cell(10,  7, $i + 1,                              1, 0, 'C', $fill);
            $pdf->Cell(55,  7, $app->student->name,                 1, 0, 'L', $fill);
            $pdf->Cell(60,  7, $app->student->email,                1, 0, 'L', $fill);
            $pdf->Cell(55,  7, $app->company->name,                 1, 0, 'L', $fill);
            $pdf->Cell(35,  7, $app->program,                       1, 0, 'C', $fill);
            $pdf->Cell(25,  7, $app->semester,                      1, 0, 'C', $fill);
            $pdf->Cell(25,  7, number_format($app->required_hours), 1, 0, 'C', $fill);
            $pdf->Cell(27,  7, number_format($approvedHours, 1),    1, 1, 'C', $fill);

            $fill = !$fill;
        }

        // Footer
        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->Cell(0, 5, 'Total students: ' . $applications->count() . '  |  OJTConnect OJT Management System', 0, 0, 'C');

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ojt_students_' . now()->format('Ymd') . '.pdf"',
        ]);
    }

    // ── PDF: Evaluations Summary ──────────────────────────────────

    public function pdfEvaluations(Request $request)
    {
        $evaluations = Evaluation::with(['student', 'application.company', 'supervisor'])->get();

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetMargins(12, 12, 12);

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(15, 17, 23);
        $pdf->Cell(0, 10, 'OJTConnect - Evaluations Summary', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(107, 114, 128);
        $pdf->Cell(0, 6, 'Bukidnon State University  |  Generated: ' . now()->format('F d, Y'), 0, 1, 'C');
        $pdf->Ln(4);

        // Table header
        $pdf->SetFillColor(15, 17, 23);
        $pdf->SetTextColor(240, 180, 41);
        $pdf->SetFont('Arial', 'B', 9);

        $cols = [
            ['#',            10],
            ['Student',      55],
            ['Company',      55],
            ['Supervisor',   50],
            ['Attendance',   28],
            ['Performance',  28],
            ['Grade',        22],
            ['Result',       24],
        ];

        foreach ($cols as [$label, $width]) {
            $pdf->Cell($width, 8, $label, 0, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(55, 65, 81);
        $fill = false;

        foreach ($evaluations as $i => $eval) {
            $pdf->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 251 : 255);
            $pdf->Cell(10,  7, $i + 1,                                         1, 0, 'C', $fill);
            $pdf->Cell(55,  7, $eval->student->name,                            1, 0, 'L', $fill);
            $pdf->Cell(55,  7, $eval->application->company->name,               1, 0, 'L', $fill);
            $pdf->Cell(50,  7, $eval->supervisor->name,                         1, 0, 'L', $fill);
            $pdf->Cell(28,  7, $eval->attendance_rating  . ' / 5',              1, 0, 'C', $fill);
            $pdf->Cell(28,  7, $eval->performance_rating . ' / 5',              1, 0, 'C', $fill);
            $pdf->Cell(22,  7, number_format($eval->overall_grade, 1),          1, 0, 'C', $fill);
            $pdf->Cell(24,  7, ucfirst($eval->recommendation),                  1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $passed = $evaluations->where('recommendation', 'pass')->count();
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(15, 17, 23);
        $pdf->Cell(0, 6, 'Total: ' . $evaluations->count() . '  |  Passed: ' . $passed . '  |  Failed: ' . ($evaluations->count() - $passed) . '  |  Average Grade: ' . round($evaluations->avg('overall_grade'), 1), 0, 0, 'C');

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ojt_evaluations_' . now()->format('Ymd') . '.pdf"',
        ]);
    }

    // ── Excel: Full OJT Report ────────────────────────────────────

    public function excelFull(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('OJTConnect Full Report')
            ->setCreator('OJTConnect');

        // ── Sheet 1: Students ─────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Students');

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'F0B429'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F1117']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $headers = ['#', 'Name', 'Email', 'Company', 'Program', 'School Year', 'Semester', 'Required Hours', 'Approved Hours', 'Status'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        $applications = OjtApplication::with(['student', 'company'])->where('status', 'approved')->get();
        foreach ($applications as $i => $app) {
            $row = $i + 2;
            $approvedHours = HourLog::where('application_id', $app->id)->where('status', 'approved')->sum('total_hours');
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $app->student->name);
            $sheet->setCellValue('C' . $row, $app->student->email);
            $sheet->setCellValue('D' . $row, $app->company->name);
            $sheet->setCellValue('E' . $row, $app->program);
            $sheet->setCellValue('F' . $row, $app->school_year);
            $sheet->setCellValue('G' . $row, $app->semester);
            $sheet->setCellValue('H' . $row, $app->required_hours);
            $sheet->setCellValue('I' . $row, round($approvedHours, 1));
            $sheet->setCellValue('J' . $row, ucfirst($app->status));

            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                ]);
            }
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Sheet 2: Evaluations ──────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Evaluations');

        $evalHeaders = ['#', 'Student', 'Email', 'Company', 'Supervisor', 'Attendance (1-5)', 'Performance (1-5)', 'Overall Grade', 'Recommendation'];
        foreach ($evalHeaders as $i => $header) {
            $col = chr(65 + $i);
            $sheet2->setCellValue($col . '1', $header);
            $sheet2->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        $evaluations = Evaluation::with(['student', 'application.company', 'supervisor'])->get();
        foreach ($evaluations as $i => $eval) {
            $row = $i + 2;
            $sheet2->setCellValue('A' . $row, $i + 1);
            $sheet2->setCellValue('B' . $row, $eval->student->name);
            $sheet2->setCellValue('C' . $row, $eval->student->email);
            $sheet2->setCellValue('D' . $row, $eval->application->company->name);
            $sheet2->setCellValue('E' . $row, $eval->supervisor->name);
            $sheet2->setCellValue('F' . $row, $eval->attendance_rating);
            $sheet2->setCellValue('G' . $row, $eval->performance_rating);
            $sheet2->setCellValue('H' . $row, round($eval->overall_grade, 1));
            $sheet2->setCellValue('I' . $row, ucfirst($eval->recommendation));
        }

        foreach (range('A', 'I') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Sheet 3: Hour Logs ────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Hour Logs');

        $logHeaders = ['#', 'Student', 'Date', 'Time In', 'Time Out', 'Total Hours', 'Description', 'Status'];
        foreach ($logHeaders as $i => $header) {
            $col = chr(65 + $i);
            $sheet3->setCellValue($col . '1', $header);
            $sheet3->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        $logs = HourLog::with('student')->orderBy('date', 'desc')->get();
        foreach ($logs as $i => $log) {
            $row = $i + 2;
            $sheet3->setCellValue('A' . $row, $i + 1);
            $sheet3->setCellValue('B' . $row, $log->student->name);
            $sheet3->setCellValue('C' . $row, $log->date->format('M d, Y'));
            $sheet3->setCellValue('D' . $row, \Carbon\Carbon::parse($log->time_in)->format('h:i A'));
            $sheet3->setCellValue('E' . $row, \Carbon\Carbon::parse($log->time_out)->format('h:i A'));
            $sheet3->setCellValue('F' . $row, round($log->total_hours, 1));
            $sheet3->setCellValue('G' . $row, $log->description ?? '');
            $sheet3->setCellValue('H' . $row, ucfirst($log->status));
        }

        foreach (range('A', 'H') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = 'ojt_full_report_' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}