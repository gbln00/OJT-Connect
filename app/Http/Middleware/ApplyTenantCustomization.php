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
        // Only run inside a tenant context
        if (!tenant()) {
            return $next($request);
        }
 
        $settings = TenantSetting::allAsArray();
 
        // Resolve logo URL (returns null if not set)
        $logoUrl = isset($settings['brand_logo'])
            ? Storage::url($settings['brand_logo'])
            : null;
 
        // Brand color — strip # if saved with it, fallback to system crimson
        $brandColor = ltrim($settings['brand_color'] ?? '8C0E03', '#');
 
        View::share([
            'tenantSettings'  => $settings,
            'tenantLogoUrl'   => $logoUrl,
            'tenantBrandColor'=> $brandColor,
            'tenantBrandName' => $settings['brand_name'] ?? null,
            'tenantGreeting'  => $settings['email_greeting'] ?? null,
            'tenantSignature' => $settings['email_signature'] ?? null,
            'tenantAnnouncement'       => $settings['announcement_text'] ?? null,
            'tenantAnnouncementActive' => ($settings['announcement_active'] ?? '0') === '1',
        ]);
 
        return $next($request);
    }
}
