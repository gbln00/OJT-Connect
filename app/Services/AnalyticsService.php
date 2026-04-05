<?php
namespace App\Services;
 
use App\Models\OjtApplication;
use App\Models\HourLog;
use App\Models\Evaluation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
class AnalyticsService
{
    // ── 1. Applications submitted per month (last 12 months) ─────────────
    public function applicationsPerMonth(): array
    {
        $rows = OjtApplication::selectRaw(
                "DATE_FORMAT(created_at, '%Y-%m') as month,
                 COUNT(*) as total,
                 SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                 SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                 SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) as pending"
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
 
        // Fill all 12 months so gaps show as zero
        $months   = [];
        $total    = [];
        $approved = [];
        $rejected = [];
 
        for ($i = 11; $i >= 0; $i--) {
            $key      = now()->subMonths($i)->format('Y-m');
            $label    = now()->subMonths($i)->format('M Y');
            $row      = $rows[$key] ?? null;
            $months[] = $label;
            $total[]  = $row?->total    ?? 0;
            $approved[]= $row?->approved ?? 0;
            $rejected[]= $row?->rejected ?? 0;
        }
 
        return compact('months', 'total', 'approved', 'rejected');
    }
 
    // ── 2. Hour logs submitted per week (last 12 weeks) ──────────────────
    public function hourLogsPerWeek(): array
    {
        $weeks  = [];
        $logged = [];
        $approved = [];
 
        for ($i = 11; $i >= 0; $i--) {
            $start  = now()->subWeeks($i)->startOfWeek();
            $end    = now()->subWeeks($i)->endOfWeek();
            $label  = $start->format('M d');
 
            $weeks[]    = $label;
            $logged[]   = HourLog::whereBetween('date', [$start, $end])->count();
            $approved[] = HourLog::whereBetween('date', [$start, $end])
                                  ->where('status', 'approved')
                                  ->count();
        }
 
        return compact('weeks', 'logged', 'approved');
    }
 
    // ── 3. Pass / Fail rate (all-time) ──────────────────────────────────
    public function passFailRate(): array
    {
        $pass = Evaluation::where('recommendation', 'pass')->count();
        $fail = Evaluation::where('recommendation', 'fail')->count();
 
        return [
            'pass'    => $pass,
            'fail'    => $fail,
            'total'   => $pass + $fail,
            'pct_pass'=> ($pass + $fail) > 0
                ? round(($pass / ($pass + $fail)) * 100, 1)
                : 0,
        ];
    }
 
    // ── 4. Progress per company (top 8 by student count) ────────────────
    public function progressPerCompany(): array
    {
        $companies = OjtApplication::with('company')
            ->where('status', 'approved')
            ->select('company_id', DB::raw('COUNT(*) as student_count'))
            ->groupBy('company_id')
            ->orderByDesc('student_count')
            ->take(8)
            ->get();
 
        $labels   = [];
        $avgPct   = [];
        $students = [];
 
        foreach ($companies as $row) {
            $companyName = $row->company?->name ?? 'Unknown';
            $labels[]    = strlen($companyName) > 22
                ? substr($companyName, 0, 22) . '...'
                : $companyName;
            $students[]  = $row->student_count;
 
            // Average progress % across all interns in this company
            $applications = OjtApplication::where('company_id', $row->company_id)
                ->where('status', 'approved')
                ->with('hourLogs')
                ->get();
 
            $pcts = $applications->map(function ($app) {
                if (!$app->required_hours) return 0;
                $approved = HourLog::where('application_id', $app->id)
                    ->where('status', 'approved')
                    ->sum('total_hours');
                return min(100, round(($approved / $app->required_hours) * 100));
            });
 
            $avgPct[] = $pcts->count() ? round($pcts->avg(), 1) : 0;
        }
 
        return compact('labels', 'avgPct', 'students');
    }
 
    // ── 5. Summary KPIs (top stat strip) ────────────────────────────────
    public function summaryKpis(): array
    {
        return [
            'total_students'   => User::where('role', 'student_intern')->count(),
            'total_companies'  => Company::where('is_active', true)->count(),
            'approved_apps'    => OjtApplication::where('status', 'approved')->count(),
            'total_hours'      => round(
                HourLog::where('status', 'approved')->sum('total_hours'), 0
            ),
            'avg_grade'        => round(
                Evaluation::avg('overall_grade') ?? 0, 1
            ),
            'pass_rate'        => $this->passFailRate()['pct_pass'],
            'pending_apps'     => OjtApplication::where('status', 'pending')->count(),
            'total_logs'       => HourLog::count(),
        ];
    }
 
    // ── 6. Recent evaluation list (for table) ────────────────────────────
    public function recentEvaluations(int $limit = 6): mixed
    {
        return Evaluation::with(['student', 'application.company'])
            ->latest('submitted_at')
            ->take($limit)
            ->get();
    }
 
    // ── 7. Top students by approved hours ────────────────────────────────
    public function topStudentsByHours(int $limit = 5): mixed
    {
        return OjtApplication::with(['student', 'company'])
            ->where('status', 'approved')
            ->withSum(['hourLogs as approved_hours' => fn($q) =>
                $q->where('status', 'approved')
            ], 'total_hours')
            ->orderByDesc('approved_hours')
            ->take($limit)
            ->get();
    }
}
