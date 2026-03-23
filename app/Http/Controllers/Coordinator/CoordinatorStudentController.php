<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Models\OjtApplication;
use Illuminate\Http\Request;

class CoordinatorStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved');

        if ($request->filled('search')) {
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
            );
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('coordinator.students.index', compact('students'));
    }
}