<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\QrClockIn;
use Illuminate\Support\Facades\Auth;

class SupervisorQrController extends Controller
{
    /**
     * Show the QR code for this supervisor's company.
     * Auto-creates one if it doesn't exist yet.
     * Route: GET /supervisor/qr
     */
    public function show()
    {
        $supervisor = Auth::user();
        $companyId  = $supervisor->company_id;

        abort_if(! $companyId, 403, 'You are not assigned to a company.');

        $qr = QrClockIn::firstOrCreate(
            ['company_id' => $companyId],
            [
                'supervisor_id' => $supervisor->id,
                'token'         => QrClockIn::generateToken(),
                'is_active'     => true,
            ]
        );

        $scanUrl    = $qr->scanUrl();
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . urlencode($scanUrl);

        return view('supervisor.qr.show', compact('qr', 'scanUrl', 'qrImageUrl'));
    }

    /**
     * Regenerate the token (invalidates old QR immediately).
     * Route: POST /supervisor/qr/regenerate
     */
    public function regenerate()
    {
        $qr = QrClockIn::where('company_id', Auth::user()->company_id)->firstOrFail();

        $qr->update([
            'token'     => QrClockIn::generateToken(),
            'is_active' => true,
        ]);

        return back()->with('success', 'QR code regenerated. Print the new one and replace the old.');
    }

    /**
     * Toggle QR active / inactive.
     * Route: POST /supervisor/qr/toggle
     */
    public function toggle()
    {
        $qr = QrClockIn::where('company_id', Auth::user()->company_id)->firstOrFail();

        $qr->update(['is_active' => ! $qr->is_active]);

        $status = $qr->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "QR code {$status}.");
    }
}