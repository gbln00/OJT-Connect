<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Models\TenantSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
 
class TenantCustomizationController extends Controller
{
    // ── Keys we read/write ─────────────────────────────────────────
    private const KEYS = [
        'brand_name', 'brand_color', 'brand_logo',
        'email_greeting', 'email_signature',
        'ojt_required_hours', 'ojt_passing_grade',
        'announcement_text', 'announcement_active',
    ];
 
    public function index()
    {
        $settings = TenantSetting::allAsArray();
        return view('admin.customization.index', compact('settings'));
    }
 
    public function update(Request $request)
    {
        $validated = $request->validate([
            'brand_name'         => ['nullable', 'string', 'max:120'],
            'brand_color'        => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'brand_logo'         => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'email_greeting'     => ['nullable', 'string', 'max:200'],
            'email_signature'    => ['nullable', 'string', 'max:200'],
            'ojt_required_hours' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'ojt_passing_grade'  => ['nullable', 'numeric', 'min:1', 'max:100'],
            'announcement_text'  => ['nullable', 'string', 'max:300'],
            'announcement_active'=> ['nullable', 'in:0,1'],
        ]);
 
        // ── Handle logo upload ─────────────────────────────────────
        if ($request->hasFile('brand_logo')) {
            $old = TenantSetting::get('brand_logo');
            if ($old) Storage::delete($old);
 
            $path = $request->file('brand_logo')
                ->store('logos/' . tenancy()->tenant->id, 'public');
            TenantSetting::set('brand_logo', $path);
        }
 
        // ── Strip # from color before saving ──────────────────────
        if (!empty($validated['brand_color'])) {
            $validated['brand_color'] = ltrim($validated['brand_color'], '#');
        }
 
        // ── Checkbox: announcement_active defaults to 0 if unchecked
        $validated['announcement_active'] = $request->input('announcement_active', '0');
 
        // ── Save all text settings ─────────────────────────────────
        foreach (self::KEYS as $key) {
            if ($key === 'brand_logo') continue; // handled above
            if (array_key_exists($key, $validated)) {
                TenantSetting::set($key, $validated[$key] ?? '');
            }
        }
 
        return redirect()->route('admin.customization.index')
            ->with('success', 'Customization settings saved successfully.');
    }
 
    public function deleteLogo()
    {
        $old = TenantSetting::get('brand_logo');
        if ($old) Storage::disk('public')->delete($old);
        TenantSetting::where('key', 'brand_logo')->delete();
 
        return back()->with('success', 'Logo removed.');
    }
}
