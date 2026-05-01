---
name: tablar-installation-development
description: Install, upgrade, and configure the takielias/tablar starter kit — composer require, tablar:install, layout selection, post-install patches (SoftDeletes on User, base Controller class for L11+), and recovery from common install failures.
---

# Tablar — Installation

## When to use this skill

- User runs `composer require takielias/tablar` and asks how to finish setup.
- User wants to switch layout (`horizontal`, `condensed`, `combined`, etc.).
- User upgrades tablar across major versions.
- User reports broken styling, missing routes, or `Route [profile] not defined` after install.

## Install flow

```bash
composer require takielias/tablar
php artisan tablar:install
npm install && npm run dev      # or: npm run build
php artisan tablar:export-auth  # publishes Breeze-style auth controllers + views
```

`tablar:install` flags:

- `--force` — overwrite user-modified files without prompting. Without it, install will diff each destination against the stub and prompt per file.
- `--no-credits` — suppress the "Star us on GitHub" credits line.

After install completes, the command prints:
```
✅ Tablar installed (Laravel {N}).
Next: npm install && npm run dev
Then: php artisan tablar:export-auth
```

## What `tablar:install` does

1. Runs `TablarPreset::install()`:
   - Updates `package.json` dependencies (Tabler core, Vite, sass-embedded, tabler-icons).
   - Updates `vite.config.js` and `resources/sass/tabler.scss`.
   - Updates `resources/js/app.js` bootstrapping.
   - Updates `resources/views/welcome.blade.php`.
   - Removes `node_modules/` (forces a clean `npm install`).
2. Runs `TablarPreset::exportConfig()` — publishes `config/tablar.php`.
3. Calls `checkController()` — Laravel 11+ only: rewrites `app/Http/Controllers/Controller.php` to extend `\Illuminate\Routing\Controller`. Idempotent.
4. Calls `patchUserModelForSoftDeletes()` — adds `Illuminate\Database\Eloquent\SoftDeletes` import + trait to `App\Models\User`. Idempotent. Both single-line `use HasFactory, Notifiable;` and multi-line `use HasFactory;\nuse Notifiable;` patterns handled.

`safeCopy()` applies to every published stub: if the destination matches the stub hash → skip silently; if user-modified → prompt or honor `--force`.

## Companion artisan commands

| Command | Purpose |
|---|---|
| `tablar:install` | Full install (config, packages, view bootstrap, controller patch, soft-deletes patch). |
| `tablar:export-auth` | Publish auth controllers, views, FormRequests (Profile/Settings/Login/Register/etc.). Run after `tablar:install`. |
| `tablar:export-config` | Re-publish `config/tablar.php` only. |
| `tablar:export-views` | Re-publish all view stubs. |
| `tablar:export-assets` | Re-publish SCSS/JS assets. |
| `tablar:export-js` | JS-only re-publish. |
| `tablar:export-all` | Re-publish everything (views + assets + JS + auth). |
| `tablar:update` | Refresh package deps + assets without re-running scaffolding. |
| `tablar:doctor` | One-shot environment snapshot for debugging install issues. |

## Layout selection

`config('tablar.layout')` — default `'horizontal'`.

Other supported values resolve view partials at `resources/views/layouts/{layout}.blade.php` (verify by reading `resources/views/page.blade.php` line 16 — `@includeIf('tablar::layouts.'. config('tablar.layout'))`).

Auxiliary keys interact with layout choice:
- `'layout_light_topbar'` (true / false / null) — `data-bs-theme` on the top bar.
- `'layout_light_sidebar'` (true / false / null) — `data-bs-theme` on the sidebar.
- `'layout_enable_top_header'` (bool) — render top header row in sidebar layouts.

For RTL: set `'layout' => 'rtl'` — `master.blade.php` adds `dir="rtl"` to `<html>` automatically.

## Post-install patches

### User model — SoftDeletes

After install:
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
}
```

Migration to add `deleted_at` is shipped at `database/migrations/2014_10_12_100000_add_soft_deletes_to_users_table.php` via `tablar:export-auth`.

### Base Controller class (L11+)

Laravel 11+ ships streamlined `app/Http/Controllers/Controller.php` as `abstract class Controller {}` (no parent). Tablar patches it to:
```php
abstract class Controller extends \Illuminate\Routing\Controller
{
}
```

Without this patch, controller traits + middleware helpers break.

### Profile + Settings controllers

`tablar:export-auth` publishes:
- `app/Http/Controllers/ProfileController.php` — name-only profile form (email read-only).
- `app/Http/Controllers/SettingsController.php` — appearance + password + delete-account.
- `app/Http/Requests/{UpdateProfileRequest,UpdatePasswordRequest,DeleteAccountRequest}.php` — FormRequests with named error bags (`updatePassword`, `deleteAccount`) so validation errors surface inside Tabler tabs.

Routes published to `routes/auth.php` under `auth` middleware:
```
GET     /profile          → ProfileController@show       (profile)
PATCH   /profile          → ProfileController@update     (profile.update)
GET     /settings         → SettingsController@show      (settings)
PUT     /settings/password→ SettingsController@updatePassword (settings.password)
DELETE  /settings         → SettingsController@destroy   (settings.destroy)
```

## Recipes

### 1. Fresh install (Laravel 12, no auth scaffolding yet)

```bash
laravel new myapp && cd myapp
composer require takielias/tablar
php artisan tablar:install
npm install && npm run dev
php artisan tablar:export-auth
php artisan migrate
```

Visit `/login`, `/profile`, `/settings`.

### 2. Adding tablar to an existing Breeze app

```bash
composer require takielias/tablar
php artisan tablar:install --force=false   # prompts per file
```

When `tablar:install` prompts about `Controller.php` or `User.php`:
- `Controller.php` — accept overwrite. The patch only adds `extends \Illuminate\Routing\Controller`; keeps the rest.
- `User.php` — accept. Patch is regex-based and adds SoftDeletes alongside Breeze's existing traits.

Skip `tablar:export-auth` — Breeze's auth scaffolding already exists. Profile/Settings stubs from Breeze remain in place.

### 3. Switching layout post-install

Edit `config/tablar.php`:
```php
'layout' => 'condensed-top',  // or other variant — see published config comments
'layout_light_topbar' => null,
'layout_light_sidebar' => true,
```

No file republish needed — value is read at render time.

### 4. Re-running install after upstream package update

```bash
composer update takielias/tablar
php artisan tablar:update
npm install && npm run dev
```

`tablar:update` refreshes deps + assets without re-running scaffolding (won't touch `Controller.php`, `User.php`, or auth views).

## Common pitfalls

- **`Route [profile] not defined`** — `tablar:export-auth` not run after `tablar:install`. Either run it OR set `'profile_url' => false` in `config/tablar.php` to hide the dropdown link.
- **Unstyled layout** — `npm run dev` (or `build`) not run. Vite manifest missing. The published `tabler.scss` and `tabler-init.js` need to be compiled.
- **`tabler-icons` font not loading** — `npm install` skipped or `node_modules/` not removed. `tablar:install` removes `node_modules/` to force a clean install; if you skipped that step, run `rm -rf node_modules && npm install`.
- **Theme toggle resets on navigation** — Tabler core's `tabler-theme.js` strips `data-bs-theme` on default `light`. Master layout's inline `<head>` script syncs `tablar.theme` → `tabler-theme` on every load to compensate. Don't write `tabler-theme` directly.
- **Controller patch reverts on every `composer install`** — published `Controller.php` lives in your app. If you re-publish via `--force`, it re-patches. Should not happen in CI as long as `app/` is committed.
- **Multiple `use SoftDeletes` insertions on rerun** — patcher is idempotent; checks `\bSoftDeletes\b` before inserting. Safe.

## Configuration reference (top keys)

```php
'use_route_url' => true,           // resolve menu route keys via route() vs raw URL
'profile_url'   => 'profile',      // route name OR path; false to hide dropdown link
'setting_url'   => 'settings',
'enable_notifications' => true,    // bell-icon dropdown visibility
'header_buttons' => [...],         // empty array hides chrome
'footer_buttons' => [...],
'layout'        => 'horizontal',
'menu'          => [...],
```

## Related

- Skill `tablar-menu-development` — menu schema and authoring.
- Slash command `/laravel-boost:install-tablar-kit` — wire tablar-kit on top of tablar.
- Source: `src/Console/TablarInstallCommand.php`, `src/TablarPreset.php`, `src/stubs/`.
