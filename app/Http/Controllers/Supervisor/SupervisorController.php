<?php
namespace App\Http\Controllers\Supervisor;
use App\Http\Controllers\Controller;
use App\Models\OjtApplication;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        $interns   = OjtApplication::with(['student','company'])
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->get();
        return view('supervisor.dashboard', compact('interns'));
    }
}
