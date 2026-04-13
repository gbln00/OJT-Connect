<?php

namespace App\Http\Middleware;

use App\Models\TenantSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class ApplyTenantCustomization
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!tenant()) {
            return $next($request);
        }

        $settings = TenantSetting::allAsArray();

        // ── Logo URL ──────────────────────────────────────────────────────────
        $logoPath = $settings['brand_logo'] ?? null;
        $logoUrl  = $logoPath
            ? tenant_asset($logoPath)
            : null;

        // ── Primary color (drives --crimson) ──────────────────────────────────
        // Strip # if present; fallback to system crimson
        $brandColor = ltrim($settings['brand_color'] ?? '8C0E03', '#');

        // ── Secondary color (drives --night / sidebar / hero bg) ─────────────
        // Fallback to system dark navy
        $brandColorSecondary = ltrim($settings['brand_color_secondary'] ?? '0E1126', '#');

        // ── Font ──────────────────────────────────────────────────────────────
        // One of: barlow | inter | poppins | roboto | default (Barlow)
        $brandFont = $settings['brand_font'] ?? 'barlow';

        // ── Announcement ──────────────────────────────────────────────────────
        $announcementActive = ($settings['announcement_active'] ?? '0') === '1';

        View::share([
            'tenantSettings'              => $settings,
            'tenantLogoUrl'               => $logoUrl,
            'tenantBrandColor'            => $brandColor,
            'tenantBrandColorSecondary'   => $brandColorSecondary,
            'tenantBrandFont'             => $brandFont,
            'tenantBrandName'             => $settings['brand_name'] ?? null,
            'tenantGreeting'              => $settings['email_greeting'] ?? null,
            'tenantSignature'             => $settings['email_signature'] ?? null,
            'tenantAnnouncement'          => $settings['announcement_text'] ?? null,
            'tenantAnnouncementActive'    => $announcementActive,
        ]);

        return $next($request);
    }
}