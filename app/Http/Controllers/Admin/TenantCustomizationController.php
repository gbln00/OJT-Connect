<?php

namespace App\Http\Controllers\Admin;

use App\Models\TenantSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class TenantCustomizationController extends Controller
{
    private const FONT_OPTIONS = ['barlow', 'inter', 'poppins', 'roboto'];

    private const KEYS = [
        'brand_name',
        'brand_color',
        'brand_color_secondary',
        'brand_logo',
        'brand_font',
        'email_greeting',
        'email_signature',
        'ojt_required_hours',
        'ojt_passing_grade',
        'announcement_text',
        'announcement_active',
        'brand_color_light', 
        'light_bg_color', 
        'light_text_color',
        'light_border_color', 
        'light_sidebar_color', 
        'light_surface_color',
        'dark_text_color', 
        'dark_border_color',
        'font_size_base', 
        'ui_density', 
        'heading_style',
        'session_morning_start', 
        'session_morning_end',
        'session_afternoon_start', 
        'session_afternoon_end',
        'allow_edit_rejected', 
        'require_log_description',
    ];

    public function index()
    {
        $settings    = TenantSetting::allAsArray();
        $fontOptions = self::FONT_OPTIONS;

        $logoPath      = $settings['brand_logo'] ?? null;
        $tenantLogoUrl = $logoPath
            ? tenant_asset($logoPath)
            : null;

        return view('admin.customization.index', compact('settings', 'fontOptions', 'tenantLogoUrl'));
    }

    public function update(Request $request)
    {
        // if ($request->hasFile('brand_logo')) {
        //     dd([
        //         'original_name' => $request->file('brand_logo')->getClientOriginalName(),
        //         'is_valid'      => $request->file('brand_logo')->isValid(),
        //         'disk_root'     => Storage::disk('public')->path(''),
        //         'tenant_id'     => tenancy()->tenant->id,
        //     ]);
        // }
        $validated = $request->validate([
            'brand_name'             => ['nullable', 'string', 'max:120'],
            'brand_color'            => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'brand_color_secondary'  => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'brand_logo'             => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'brand_font'             => ['nullable', 'in:barlow,inter,poppins,roboto'],
            'email_greeting'         => ['nullable', 'string', 'max:200'],
            'email_signature'        => ['nullable', 'string', 'max:200'],
            'ojt_required_hours'     => ['nullable', 'integer', 'min:1', 'max:2000'],
            'ojt_passing_grade'      => ['nullable', 'numeric', 'min:1', 'max:100'],
            'announcement_text'      => ['nullable', 'string', 'max:300'],
            'announcement_active'    => ['nullable', 'in:0,1'],
            'brand_color_light'     => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'light_bg_color'        => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'light_text_color'      => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'light_border_color'    => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'light_sidebar_color'   => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'light_surface_color'   => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'dark_text_color'       => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'dark_border_color'     => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'font_size_base'        => ['nullable', 'integer', 'min:10', 'max:24'],
            'ui_density'           => ['nullable', 'in:comfortable,compact'],
            'heading_style'        => ['nullable', 'in:normal,underline'],
            'session_morning_start'=> ['nullable', 'date_format:H:i'],
            'session_morning_end'  => ['nullable', 'date_format:H:i'],
            'session_afternoon_start'=> ['nullable', 'date_format:H:i'],
            'session_afternoon_end'=> ['nullable', 'date_format:H:i'],
            'allow_edit_rejected'  => ['nullable', 'in:0,1'],
            'require_log_description' => ['nullable', 'in:0,1'],
        ]);

        // ── Logo upload ───────────────────────────────────────────────────────
        if ($request->hasFile('brand_logo')) {
            $old = TenantSetting::get('brand_logo');
            if ($old) Storage::disk('public')->delete($old);

            $path = $request->file('brand_logo')
                ->store('logos/' . tenancy()->tenant->id, 'public');
            TenantSetting::set('brand_logo', $path);
        }

        // ── Strip # from color fields ─────────────────────────────────────────
        foreach (['brand_color', 'brand_color_secondary'] as $colorKey) {
            if (!empty($validated[$colorKey])) {
                $validated[$colorKey] = ltrim($validated[$colorKey], '#');
            }
        }

        // ── Checkbox default ──────────────────────────────────────────────────
        $validated['announcement_active'] = $request->input('announcement_active', '0');

        // ── Save all text/select settings ─────────────────────────────────────
        foreach (self::KEYS as $key) {
            if ($key === 'brand_logo') continue;
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

    public function reset()
    {
        // Reset to system defaults by deleting all branding keys
        TenantSetting::whereIn('key', ['brand_color', 'brand_color_secondary', 'brand_font', 'brand_name', 'brand_logo'])->delete();

        $old = TenantSetting::get('brand_logo');
        if ($old) Storage::disk('public')->delete($old);

        return back()->with('success', 'Branding reset to system defaults.');
    }
}