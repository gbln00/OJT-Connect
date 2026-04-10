<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OjtApplication;
use App\Models\HourLog;
use App\Models\WeeklyReport;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Services\OJTPdf; 


// ── Controller ────────────────────────────────────────────────────
class ExportController extends Controller
{
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

    public function pdfStudents()
    {
        $applications = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved')->get();

        $pdf = new OJTPdf('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->reportTitle  = 'Student OJT Summary';
        $pdf->reportSub    = 'Approved interns with hour progress';
        $pdf->totalRecords = $applications->count();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();

        // Summary boxes
        $pdf->SummaryBox('Total Students',   $applications->count(),           10,  42);
        $pdf->SummaryBox('Approved Hours',   number_format(HourLog::where('status','approved')->sum('total_hours'),1), 72, 42, 58, [45,212,191]);
        $pdf->SummaryBox('Companies',        Company::where('is_active',true)->count(), 134, 42, 58, [96,165,250]);
        $pdf->SummaryBox('Report Date',      now()->format('M d, Y'),          196, 42, 58, [156,163,175]);
        $pdf->Ln(26);

        $pdf->SectionTitle('Intern Records');

        $cols = [
            ['#',9,'C'],['Student Name',52,'L'],['Email',56,'L'],
            ['Company',48,'L'],['Program',30,'C'],['Semester',22,'C'],
            ['Req. Hrs',22,'C'],['Appr. Hrs',22,'C'],['Progress',30,'C'],
        ];

        $pdf->SetFillColor(25, 28, 38);
        $pdf->SetTextColor(240, 180, 41);
        $pdf->SetFont('Arial', 'B', 8);
        foreach ($cols as [$l,$w,$a]) $pdf->Cell($w, 8, $l, 0, 0, $a, true);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 7.5);
        $pdf->SetDrawColor(235, 237, 240);
        $fill = false;

        foreach ($applications as $i => $app) {
            $approved = HourLog::where('application_id', $app->id)->where('status','approved')->sum('total_hours');
            $pct = $app->required_hours > 0 ? min(100, round(($approved/$app->required_hours)*100)) : 0;

            $pdf->SetFillColor($fill ? 247 : 255, $fill ? 248 : 255, $fill ? 249 : 255);
            $pdf->SetTextColor(55, 65, 81);

            $rowY = $pdf->GetY();
            $pdf->Cell(9,  7, $i+1,                             'B', 0, 'C', $fill);
            $pdf->Cell(52, 7, $app->student->name,               'B', 0, 'L', $fill);
            $pdf->Cell(56, 7, $app->student->email,              'B', 0, 'L', $fill);
            $pdf->Cell(48, 7, $app->company->name,               'B', 0, 'L', $fill);
            $pdf->Cell(30, 7, $app->program,                     'B', 0, 'C', $fill);
            $pdf->Cell(22, 7, $app->semester,                    'B', 0, 'C', $fill);
            $pdf->Cell(22, 7, number_format($app->required_hours),'B',0, 'C', $fill);
            $pdf->Cell(22, 7, number_format($approved, 1),       'B', 0, 'C', $fill);

            $barCellX = $pdf->GetX();
            $pdf->Cell(30, 7, '', 'B', 0, 'C', $fill);

            // Draw progress bar
            $bx = $barCellX + 2; $by = $rowY + 2.2; $bw = 26; $bh = 2.8;
            $pdf->SetFillColor(220, 222, 226);
            $pdf->Rect($bx, $by, $bw, $bh, 'F');
            $fw = max(0, ($pct/100)*$bw);
            if ($pct >= 100)    $pdf->SetFillColor(45,212,191);
            elseif ($pct >= 50) $pdf->SetFillColor(96,165,250);
            else                $pdf->SetFillColor(240,180,41);
            if ($fw > 0) $pdf->Rect($bx, $by, $fw, $bh, 'F');
            $pdf->SetFont('Arial','',6);
            $pdf->SetTextColor(107,114,128);
            $pdf->SetXY($bx, $by+3.2);
            $pdf->Cell($bw, 3, $pct.'%', 0, 0, 'C');

            $pdf->SetFont('Arial','',7.5);
            $pdf->Ln();
            $fill = !$fill;
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ojt_students_'.now()->format('Ymd').'.pdf"',
        ]);
    }

    public function pdfEvaluations()
    {
        $evaluations = Evaluation::with(['student','application.company','supervisor'])->get();
        $passed   = $evaluations->where('recommendation','pass')->count();
        $avgGrade = round($evaluations->avg('overall_grade') ?? 0, 1);

        $pdf = new OJTPdf('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->reportTitle  = 'Evaluations Summary';
        $pdf->reportSub    = 'Student performance ratings and grades';
        $pdf->totalRecords = $evaluations->count();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();

        $pdf->SummaryBox('Total Evaluations', $evaluations->count(),          10,  42);
        $pdf->SummaryBox('Passed',            $passed,                        72,  42, 58, [45,212,191]);
        $pdf->SummaryBox('Failed',            $evaluations->count()-$passed,  134, 42, 58, [248,113,113]);
        $pdf->SummaryBox('Average Grade',     $avgGrade,                      196, 42, 58, [96,165,250]);
        $pdf->Ln(26);

        $pdf->SectionTitle('Evaluation Records');

        $cols = [
            ['#',9,'C'],['Student Name',52,'L'],['Company',48,'L'],
            ['Supervisor',44,'L'],['Attendance',26,'C'],['Performance',26,'C'],
            ['Grade',22,'C'],['Result',24,'C'],['Rating',26,'C'],
        ];

        $pdf->SetFillColor(25,28,38);
        $pdf->SetTextColor(240,180,41);
        $pdf->SetFont('Arial','B',8);
        foreach ($cols as [$l,$w,$a]) $pdf->Cell($w, 8, $l, 0, 0, $a, true);
        $pdf->Ln();

        $pdf->SetFont('Arial','',7.5);
        $pdf->SetDrawColor(235,237,240);
        $fill = false;

        foreach ($evaluations as $i => $eval) {
            $avg = ($eval->attendance_rating + $eval->performance_rating) / 2;
            $rl  = match(true) {
                $avg >= 5 => 'Excellent', $avg >= 4 => 'Very Good',
                $avg >= 3 => 'Good',      $avg >= 2 => 'Fair', default => 'Poor',
            };

            $pdf->SetFillColor($fill ? 247:255, $fill ? 248:255, $fill ? 249:255);
            $pdf->SetTextColor(55,65,81);

            $pdf->Cell(9,  7, $i+1,                               'B',0,'C',$fill);
            $pdf->Cell(52, 7, $eval->student->name,                'B',0,'L',$fill);
            $pdf->Cell(48, 7, $eval->application->company->name,   'B',0,'L',$fill);
            $pdf->Cell(44, 7, $eval->supervisor->name,              'B',0,'L',$fill);
            $pdf->Cell(26, 7, $eval->attendance_rating.'/5',        'B',0,'C',$fill);
            $pdf->Cell(26, 7, $eval->performance_rating.'/5',       'B',0,'C',$fill);

            if ($eval->overall_grade >= 90)     $pdf->SetTextColor(45,212,191);
            elseif ($eval->overall_grade >= 75) $pdf->SetTextColor(96,165,250);
            elseif ($eval->overall_grade >= 60) $pdf->SetTextColor(240,180,41);
            else                                $pdf->SetTextColor(248,113,113);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(22, 7, number_format($eval->overall_grade,1),'B',0,'C',$fill);

            $pdf->SetFont('Arial','B',7.5);
            $pdf->SetTextColor($eval->recommendation==='pass' ? 45:248, $eval->recommendation==='pass'?212:113, $eval->recommendation==='pass'?191:113);
            $pdf->Cell(24, 7, ucfirst($eval->recommendation),        'B',0,'C',$fill);

            $pdf->SetFont('Arial','',7.5);
            $pdf->SetTextColor(107,114,128);
            $pdf->Cell(26, 7, $rl,                                   'B',1,'C',$fill);

            $fill = !$fill;
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ojt_evaluations_'.now()->format('Ymd').'.pdf"',
        ]);
    }

    public function excelFull()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('OJTConnect Full Report')->setCreator('OJTConnect');

        $headerStyle = [
            'font'      => ['bold'=>true,'color'=>['rgb'=>'F0B429'],'size'=>11],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F1117']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'333333']]],
        ];
        $oddRow = ['fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'F9FAFB']]];

        // Sheet 1: Students
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Students');
        foreach (['#','Name','Email','Company','Program','School Year','Semester','Required Hours','Approved Hours','Progress %'] as $i => $h) {
            $col = chr(65+$i);
            $sheet->setCellValue($col.'1',$h);
            $sheet->getStyle($col.'1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(20);
        }
        $applications = OjtApplication::with(['student','company'])->where('status','approved')->get();
        foreach ($applications as $i => $app) {
            $row = $i+2;
            $approved = HourLog::where('application_id',$app->id)->where('status','approved')->sum('total_hours');
            $pct = $app->required_hours > 0 ? min(100,round(($approved/$app->required_hours)*100)) : 0;
            $sheet->setCellValue('A'.$row,$i+1);
            $sheet->setCellValue('B'.$row,$app->student->name);
            $sheet->setCellValue('C'.$row,$app->student->email);
            $sheet->setCellValue('D'.$row,$app->company->name);
            $sheet->setCellValue('E'.$row,$app->program);
            $sheet->setCellValue('F'.$row,$app->school_year);
            $sheet->setCellValue('G'.$row,$app->semester);
            $sheet->setCellValue('H'.$row,$app->required_hours);
            $sheet->setCellValue('I'.$row,round($approved,1));
            $sheet->setCellValue('J'.$row,$pct.'%');
            if ($i%2===0) $sheet->getStyle('A'.$row.':J'.$row)->applyFromArray($oddRow);
        }
        foreach (range('A','J') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        // Sheet 2: Evaluations
        $sheet2 = $spreadsheet->createSheet()->setTitle('Evaluations');
        foreach (['#','Student','Email','Company','Supervisor','Attendance (1-5)','Performance (1-5)','Overall Grade','Recommendation','Rating'] as $i => $h) {
            $col = chr(65+$i);
            $sheet2->setCellValue($col.'1',$h);
            $sheet2->getStyle($col.'1')->applyFromArray($headerStyle);
        }
        $evaluations = Evaluation::with(['student','application.company','supervisor'])->get();
        foreach ($evaluations as $i => $eval) {
            $row = $i+2;
            $avg = ($eval->attendance_rating+$eval->performance_rating)/2;
            $rl  = match(true){$avg>=5=>'Excellent',$avg>=4=>'Very Good',$avg>=3=>'Good',$avg>=2=>'Fair',default=>'Poor'};
            $sheet2->setCellValue('A'.$row,$i+1);
            $sheet2->setCellValue('B'.$row,$eval->student->name);
            $sheet2->setCellValue('C'.$row,$eval->student->email);
            $sheet2->setCellValue('D'.$row,$eval->application->company->name);
            $sheet2->setCellValue('E'.$row,$eval->supervisor->name);
            $sheet2->setCellValue('F'.$row,$eval->attendance_rating);
            $sheet2->setCellValue('G'.$row,$eval->performance_rating);
            $sheet2->setCellValue('H'.$row,round($eval->overall_grade,1));
            $sheet2->setCellValue('I'.$row,ucfirst($eval->recommendation));
            $sheet2->setCellValue('J'.$row,$rl);
            if ($i%2===0) $sheet2->getStyle('A'.$row.':J'.$row)->applyFromArray($oddRow);
        }
        foreach (range('A','J') as $col) $sheet2->getColumnDimension($col)->setAutoSize(true);

        // Sheet 3: Hour Logs
        $sheet3 = $spreadsheet->createSheet()->setTitle('Hour Logs');
        foreach (['#','Student','Date','Time In','Time Out','Total Hours','Description','Status'] as $i => $h) {
            $col = chr(65+$i);
            $sheet3->setCellValue($col.'1',$h);
            $sheet3->getStyle($col.'1')->applyFromArray($headerStyle);
        }
        $logs = HourLog::with('student')->orderBy('date','desc')->get();
        foreach ($logs as $i => $log) {
            $row = $i+2;
            $sheet3->setCellValue('A'.$row,$i+1);
            $sheet3->setCellValue('B'.$row,$log->student->name);
            $sheet3->setCellValue('C'.$row,$log->date->format('M d, Y'));
            $sheet3->setCellValue('D'.$row,\Carbon\Carbon::parse($log->time_in)->format('h:i A'));
            $sheet3->setCellValue('E'.$row,\Carbon\Carbon::parse($log->time_out)->format('h:i A'));
            $sheet3->setCellValue('F'.$row,round($log->total_hours,1));
            $sheet3->setCellValue('G'.$row,$log->description??'');
            $sheet3->setCellValue('H'.$row,ucfirst($log->status));
            if ($i%2===0) $sheet3->getStyle('A'.$row.':H'.$row)->applyFromArray($oddRow);
        }
        foreach (range('A','H') as $col) $sheet3->getColumnDimension($col)->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $filename = 'ojt_full_report_'.now()->format('Ymd').'.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function certificate(OjtApplication $application)
    {
        $application->load(['student', 'company', 'evaluation']);

        // Guard: must be completed with a passing evaluation
        if (!$application->evaluation || $application->evaluation->recommendation !== 'pass') {
            return back()->with('error', 'Certificate is only available for students with a passing evaluation.');
        }

        $approvedHours = HourLog::where('application_id', $application->id)
            ->where('status', 'approved')
            ->sum('total_hours');

        // Get coordinator name
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
}