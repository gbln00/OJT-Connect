<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\SystemVersion;
use App\Models\Tenant;
use App\Models\TenantUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ── 1. Verify GitHub signature ──────────────────────────────
        $secret    = config('services.github.webhook_secret');
        $signature = $request->header('X-Hub-Signature-256', '');
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expected, $signature)) {
            Log::warning('GitHub webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // ── 2. Only handle "release" events ────────────────────────
        $event = $request->header('X-GitHub-Event');
        if ($event !== 'release') {
            return response()->json(['ok' => true, 'skipped' => 'not a release event']);
        }

        $payload = $request->json()->all();
        $action  = $payload['action'] ?? '';

        // Only act when a release is published (not drafted, edited, deleted)
        if ($action !== 'published') {
            return response()->json(['ok' => true, 'skipped' => "action=$action"]);
        }

        // ── 3. Parse release data ───────────────────────────────────
        $release = $payload['release'];

        // Skip pre-releases (optional — remove if you want to allow them)
        if ($release['prerelease'] ?? false) {
            return response()->json(['ok' => true, 'skipped' => 'prerelease']);
        }

        $tag      = ltrim($release['tag_name'], 'v');  // "v1.3.0" → "1.3.0"
        $name     = $release['name'] ?? "Release v{$tag}";
        $body     = $release['body'] ?? '';             // Your markdown changelog
        $htmlUrl  = $release['html_url'] ?? '';
        $releaseId = (string) $release['id'];

        // Detect type from tag: 1.x.0 = minor, 1.0.0 = major, 1.0.1 = patch
        $type = $this->detectType($tag);

        // ── 4. Avoid duplicates ─────────────────────────────────────
        if (SystemVersion::where('github_release_id', $releaseId)->exists()) {
            return response()->json(['ok' => true, 'skipped' => 'already processed']);
        }

        // ── 5. Create SystemVersion ─────────────────────────────────
        $version = SystemVersion::create([
            'version'           => $tag,
            'label'             => $name,
            'type'              => $type,
            'changelog'         => $body ?: "No changelog provided.",
            'is_published'      => true,
            'published_at'      => now(),
            'github_release_id' => $releaseId,
            'github_tag'        => $release['tag_name'],
            'github_html_url'   => $htmlUrl,
            // migration_folder must match: database/migrations/versions/v{tag-with-dashes}
            'migration_folder'  => 'versions/v' . str_replace('.', '-', $tag),
        ]);

        $version->markAsCurrent();

        // ── 6. Create TenantUpdate rows + notify ───────────────────
        $tenants  = Tenant::where('status', 'active')->orWhereNull('status')->get();
        $notified = 0;

        foreach ($tenants as $tenant) {
            // Create the install record
            TenantUpdate::firstOrCreate(
                ['tenant_id' => $tenant->id, 'version_id' => $version->id],
                ['status' => 'pending']
            );

            // Notify tenant admins
            try {
                tenancy()->initialize($tenant);
                \App\Models\TenantNotification::notify(
                    title:      "New Update Available: v{$version->version}",
                    message:    $version->label ?? "A new system update is ready to install.",
                    type:       'info',
                    targetRole: 'admin'
                );
                tenancy()->end();
                $notified++;
            } catch (\Throwable $e) {
                tenancy()->end();
                Log::error("Webhook notify failed for tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        Log::info("GitHub release v{$tag} processed. {$notified} tenants notified.");

        return response()->json([
            'ok'       => true,
            'version'  => $tag,
            'notified' => $notified,
        ]);
    }

    private function detectType(string $tag): string
    {
        // Semantic versioning: MAJOR.MINOR.PATCH
        $parts = explode('.', $tag);
        $major = (int) ($parts[0] ?? 0);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        if ($minor === 0 && $patch === 0) return 'major';
        if ($patch === 0) return 'minor';
        if ($patch <= 5)  return 'patch';
        return 'hotfix';
    }
}<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\SystemVersion;
use App\Models\Tenant;
use App\Models\TenantUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ── 1. Verify GitHub signature ──────────────────────────────
        $secret    = config('services.github.webhook_secret');
        $signature = $request->header('X-Hub-Signature-256', '');
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expected, $signature)) {
            Log::warning('GitHub webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // ── 2. Only handle "release" events ────────────────────────
        $event = $request->header('X-GitHub-Event');
        if ($event !== 'release') {
            return response()->json(['ok' => true, 'skipped' => 'not a release event']);
        }

        $payload = $request->json()->all();
        $action  = $payload['action'] ?? '';

        // Only act when a release is published (not drafted, edited, deleted)
        if ($action !== 'published') {
            return response()->json(['ok' => true, 'skipped' => "action=$action"]);
        }

        // ── 3. Parse release data ───────────────────────────────────
        $release = $payload['release'];

        // Skip pre-releases (optional — remove if you want to allow them)
        if ($release['prerelease'] ?? false) {
            return response()->json(['ok' => true, 'skipped' => 'prerelease']);
        }

        $tag      = ltrim($release['tag_name'], 'v');  // "v1.3.0" → "1.3.0"
        $name     = $release['name'] ?? "Release v{$tag}";
        $body     = $release['body'] ?? '';             // Your markdown changelog
        $htmlUrl  = $release['html_url'] ?? '';
        $releaseId = (string) $release['id'];

        // Detect type from tag: 1.x.0 = minor, 1.0.0 = major, 1.0.1 = patch
        $type = $this->detectType($tag);

        // ── 4. Avoid duplicates ─────────────────────────────────────
        if (SystemVersion::where('github_release_id', $releaseId)->exists()) {
            return response()->json(['ok' => true, 'skipped' => 'already processed']);
        }

        // ── 5. Create SystemVersion ─────────────────────────────────
        $version = SystemVersion::create([
            'version'           => $tag,
            'label'             => $name,
            'type'              => $type,
            'changelog'         => $body ?: "No changelog provided.",
            'is_published'      => true,
            'published_at'      => now(),
            'github_release_id' => $releaseId,
            'github_tag'        => $release['tag_name'],
            'github_html_url'   => $htmlUrl,
            // migration_folder must match: database/migrations/versions/v{tag-with-dashes}
            'migration_folder'  => 'versions/v' . str_replace('.', '-', $tag),
        ]);

        $version->markAsCurrent();

        // ── 6. Create TenantUpdate rows + notify ───────────────────
        $tenants  = Tenant::where('status', 'active')->orWhereNull('status')->get();
        $notified = 0;

        foreach ($tenants as $tenant) {
            // Create the install record
            TenantUpdate::firstOrCreate(
                ['tenant_id' => $tenant->id, 'version_id' => $version->id],
                ['status' => 'pending']
            );

            // Notify tenant admins
            try {
                tenancy()->initialize($tenant);
                \App\Models\TenantNotification::notify(
                    title:      "New Update Available: v{$version->version}",
                    message:    $version->label ?? "A new system update is ready to install.",
                    type:       'info',
                    targetRole: 'admin'
                );
                tenancy()->end();
                $notified++;
            } catch (\Throwable $e) {
                tenancy()->end();
                Log::error("Webhook notify failed for tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        Log::info("GitHub release v{$tag} processed. {$notified} tenants notified.");

        return response()->json([
            'ok'       => true,
            'version'  => $tag,
            'notified' => $notified,
        ]);
    }

    private function detectType(string $tag): string
    {
        // Semantic versioning: MAJOR.MINOR.PATCH
        $parts = explode('.', $tag);
        $major = (int) ($parts[0] ?? 0);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        if ($minor === 0 && $patch === 0) return 'major';
        if ($patch === 0) return 'minor';
        if ($patch <= 5)  return 'patch';
        return 'hotfix';
    }
}