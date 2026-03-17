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
}

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
}