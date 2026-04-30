# RELEASE CHECKLIST

Use this checklist for every tenant update release (example: `v1.4.4`).

---

## 1) Pre-release (Local)

- [ ] Checkout latest main branch.
- [ ] Implement code changes.
- [ ] If needed, add update patch class:
      `app/Updates/VX_Y_Z/DataPatch.php`
- [ ] If needed, add versioned migrations folder and files.
- [ ] Validate app compiles and key flows work.

### Commands

```bash
git checkout main
git pull origin main
php artisan optimize:clear
```

---

## 2) Commit and Push to GitHub

- [ ] Stage only intended files.
- [ ] Commit with clear version intent.
- [ ] Push to `main`.

### Commands

```bash
git add .
git commit -m "feat: release vX.Y.Z update"
git push origin main
```

---

## 3) Super Admin Version Record (UI)

- [ ] Login to central Super Admin panel.
- [ ] Go to **Versions** -> **Create**.
- [ ] Fill fields:
  - `version`: `X.Y.Z`
  - `label`: release title
  - `type`: `major | minor | patch | hotfix`
  - `changelog`: tenant-visible notes
  - `is_critical`: true/false
  - `requires_version`: optional previous version
  - `migration_folder`: optional version migration folder
- [ ] Save draft.
- [ ] Click **Publish**.

Expected result:
- `system_versions.is_published = true`
- `tenant_updates` rows created with `pending` status for active tenants
- tenant admins receive update notification

---

## 4) Server Deployment (Host Machine)

- [ ] Pull latest code to the running host machine.
- [ ] Clear caches.
- [ ] Restart service/queue if your environment needs it.

### Commands

```bash
git pull origin main
php artisan optimize:clear
php artisan queue:restart
```

Optional (if using your deploy command):

```bash
php artisan system:deploy
```

---

## 5) Tenant Installation Flow

- [ ] Tenant admin logs in.
- [ ] Open **What's New**.
- [ ] Click **Install vX.Y.Z**.
- [ ] Wait for status to change:
  - `pending` -> `in_progress` -> `completed`
- [ ] Validate new feature/behavior is now available.

---

## 6) Verification Commands

Run from project root on host machine.

### Verify published version

```bash
php artisan tinker --execute='dump(\App\Models\SystemVersion::where("version","X.Y.Z")->first(["id","version","is_published","is_current","published_at"]));'
```

### Verify tenant update status

```bash
php artisan tinker --execute='dump(\App\Models\TenantUpdate::where("tenant_id","demo")->latest("id")->first(["version_id","status","installed_at","installed_by"]));'
```

### Inspect failed updates (if any)

```bash
php artisan tinker --execute='dump(\App\Models\TenantUpdate::where("status","failed")->latest("id")->take(5)->get(["tenant_id","version_id","status","error_log"])->toArray());'
```

---

## 7) Rollback/Recovery Notes

- If install fails:
  - tenant status shows `failed`
  - tenant can retry from **What's New**
- Check logs and `error_log` column in `tenant_updates`.
- Fix issue in code, push patch, then retry installation.

---

## 8) GitHub Release Notes Template

Create a GitHub release/tag after push (recommended for milestones).

### Suggested Tag

- `vX.Y.Z`

### Suggested Title

- `vX.Y.Z - <short release name>`

### Release Notes Body (copy/paste)

```md
## Summary
- <1-2 line overview of what this release delivers>

## Highlights
- <feature/fix 1>
- <feature/fix 2>
- <feature/fix 3>

## Tenant Update Flow
- Published from Super Admin Versions panel
- Tenants install via **What's New**
- Status transitions: `pending -> in_progress -> completed`

## Migrations / Data Patch
- Migration folder: `<none or path>`
- Data patch class: `<none or App\Updates\VX_Y_Z\DataPatch>`

## Breaking Changes
- <none or details>

## Verification
- [ ] Version visible in Super Admin
- [ ] Tenant update record created
- [ ] Tenant installation completes
- [ ] New feature accessible after install
```

---

## 9) v1.4.4 Demo Notes (Current Project)

- Mock feature lab is gated behind `v1.4.4`.
- Tenant must install update first from **What's New**.
- Demo tenant URL:
  - `http://demo.10.0.0.58.nip.io:8000`
