<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;

class CoordinatorEvaluationController extends Controller
{
    /**
     * 📊 Show all evaluations
     */
    public function index()
    {
        $evaluations = Evaluation::with(['student', 'application', 'supervisor'])
            ->latest('submitted_at')
            ->paginate(10);

        return view('coordinator.evaluations.index', compact('evaluations'));
    }

    /**
     * 🔍 View a single evaluation
     */
    public function show($id)
    {
        $evaluation = Evaluation::with(['student', 'application', 'supervisor'])
            ->findOrFail($id);

        return view('coordinator.evaluations.show', compact('evaluation'));
    }

    /**
     * ✅ Mark OJT as completed (based on evaluation)
     */
    public function complete($id)
    {
        $evaluation = Evaluation::with('application')->findOrFail($id);

        // Example: mark application as completed
        if ($evaluation->application) {
            $evaluation->application->update([
                'status' => 'completed'
            ]);
        }

        return redirect()
            ->route('coordinator.evaluations.index')
            ->with('success', 'OJT marked as completed.');
    }
}