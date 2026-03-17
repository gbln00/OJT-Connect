<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = Evaluation::with(['student', 'application.company', 'supervisor'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            );
        }

        if ($request->filled('recommendation')) {
            $query->where('recommendation', $request->recommendation);
        }

        $evaluations = $query->paginate(15)->withQueryString();

        $counts = [
            'total'  => Evaluation::count(),
            'passed' => Evaluation::where('recommendation', 'pass')->count(),
            'failed' => Evaluation::where('recommendation', 'fail')->count(),
            'avg_grade' => round(Evaluation::avg('overall_grade') ?? 0, 1),
        ];

        return view('admin.evaluations.index', compact('evaluations', 'counts'));
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['student', 'application.company', 'supervisor']);
        return view('admin.evaluations.show', compact('evaluation'));
    }
}