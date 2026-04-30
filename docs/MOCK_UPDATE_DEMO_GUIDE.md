# Mock Update Demo Guide (Super Admin -> GitHub -> Tenant)

This guide demonstrates a tenant-gated feature rollout using update `v1.4.4`.

## What this demo proves

- Super admin can publish an update to tenants.
- Tenant sees update in **What's New**.
- Tenant cannot use the mock feature until update is installed.
- After install, feature unlocks automatically through `DataPatch`.

## Prerequisites

- Central app running on LAN (`php artisan serve --host=0.0.0.0 --port=8000`)
- Tenant exists (example: `demo`)
- Tenant admin can access `admin/whats-new`

## 1) Super admin creates and publishes update

From Super Admin panel:

1. Go to **Versions**.
2. Create version:
   - `version`: `1.4.4`
   - `label`: `Mock Feature Lab Rollout`
   - `type`: `minor`
   - `changelog`: mention mock feature gating demo
   - `migration_folder`: leave empty
   - `requires_version`: optional (set if enforcing order)
3. Click **Publish**.

What happens:

- A `tenant_updates` record is created per active tenant with `status=pending`.
- Tenant receives a notification.

## 2) GitHub / deployment step

Push backend code containing:

- `app/Updates/V1_4_4/DataPatch.php`
- `AdminController@mockLab`
- route `admin.mock-lab.index`
- `resources/views/admin/mock-lab/index.blade.php`

Then deploy/pull on server that hosts central+tenant app.

## 3) Tenant installs update

Tenant admin flow:

1. Open tenant URL, e.g. `http://demo.10.0.0.58.nip.io:8000`
2. Login as tenant admin.
3. Go to **What's New**.
4. Click **Install v1.4.4**.

Install job behavior:

- Sets update status to `in_progress`
- Runs `DataPatch` class `App\Updates\V1_4_4\DataPatch`
- Marks status `completed` on success

## 4) Feature gating behavior

- Before install: opening **Mock Feature Lab** redirects tenant to **What's New** with message.
- After install: tenant can access **Mock Feature Lab** (`/admin/mock-lab`).

## 5) Quick verification checklist

- `tenant_updates.status` for `v1.4.4` = `completed`
- Tenant notification shows unlock message
- `tenant_settings` contains:
  - `mock_feature.enabled = 1`
  - `mock_feature.activated_at = <timestamp>`
- Tenant can open `admin.mock-lab.index`
