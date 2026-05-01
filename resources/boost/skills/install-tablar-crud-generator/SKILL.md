---
name: install-tablar-crud-generator
description: Step-by-step interactive installer for takielias/tablar-crud-generator (dev dep) into a Laravel app already running tablar + tablar-kit — composer require dev, optional config publish, smoke `make:crud` scaffold, route + view registration. Pause for user approval at every shell command.
---

# Install Tablar CRUD Generator

Slash command: `/laravel-boost:install-tablar-crud-generator`

Invoked explicitly by the user. Walk one step at a time. **Pause for user approval before every shell command.**

## Pre-flight checks

1. **tablar installed**:
   ```bash
   composer show takielias/tablar
   ```
   Generated views extend tablar layout. If missing → abort with hint to install tablar first.

2. **tablar-kit installed**:
   ```bash
   composer show takielias/tablar-kit
   ```
   Generated forms use tablar-kit FormBuilder + components. If missing → tell user to invoke `/laravel-boost:install-tablar-kit` first, then retry.

3. **Laravel version**:
   ```bash
   php artisan --version
   ```
   Should be 11.x / 12.x / 13.x.

4. **Already installed?**:
   ```bash
   composer show takielias/tablar-crud-generator
   ```
   If present → skip step 1, jump to step 3 (smoke).

If all checks pass, summarize state and ask for approval.

## Step 1 — Composer require (dev)

```bash
composer require takielias/tablar-crud-generator --dev
```

**Important: install as a dev dependency** — generators are tooling, not a runtime requirement. Adding to production composer.json bloats deploys.

Service provider `Tablar\CrudGenerator\CrudServiceProvider` is auto-discovered.

## Step 2 — Publish config (optional)

The package ships defaults that work without publishing. Publish only if user wants to customize stub paths or naming conventions:

```bash
php artisan vendor:publish --tag=crud
```

Writes `config/crud.php`.

For first install, suggest skipping unless user explicitly asks. Defer publishing until user hits a customization need.

## Step 3 — Smoke scaffold

Verify the generator works end-to-end. Pick a throwaway resource name (e.g. `Demo`) and confirm with user before running.

The artisan signature is:

```
make:crud {name : Table name}
          {--route= : Custom route name}
          {--crud-name= : Custom crud name}
          {--lang= : language}
```

Note: the **first argument is the database table name** (typically plural snake_case), NOT a model name. Generator infers model name + crud name from the table name unless `--crud-name` is passed.

Example:
```bash
php artisan make:crud demos
```

Walk the user through what gets generated:
- `app/Models/Demo.php`
- `database/migrations/{ts}_create_demos_table.php`
- `app/Http/Controllers/DemosController.php`
- `app/Http/Requests/{Store,Update}DemoRequest.php`
- `resources/views/demos/{index,create,edit,show}.blade.php`
- Routes appended to `routes/web.php`

(Verify exact list against the published stubs after install — paths may differ across versions.)

## Step 4 — Run migration

```bash
php artisan migrate
```

If user is using DDEV: `ddev artisan migrate`.

## Step 5 — Visit the route

Suggest visiting `/{plural-name}` (e.g. `http://localhost/demos`). Confirm:
- Index page renders with tablar layout chrome.
- Create / edit forms use tablar-kit FormBuilder fields.
- Action buttons render.

## Step 6 — Cleanup (only if smoke resource was Demo)

If user used "Demo" as a throwaway, offer cleanup:
```bash
# Roll back migration
php artisan migrate:rollback --step=1

# Manual: delete generated files
rm app/Models/Demo.php
rm app/Http/Controllers/DemosController.php
rm app/Http/Requests/{Store,Update}DemoRequest.php
rm -rf resources/views/demos/
```

Plus: open `routes/web.php` and remove the appended Demo route block.

If user wants to keep Demo, skip cleanup.

## Recipes for real use (post-install)

### Generate with custom route + crud name

```bash
php artisan make:crud users --route=admin/users --crud-name=AdminUser
```

### Localized labels

```bash
php artisan make:crud products --lang=en
```

(Check if package supports multiple locales. If not, `--lang=` is a stub-template selector.)

## Idempotent rerun

- Pre-flight skips step 1 if already installed.
- `make:crud` against an existing table will overwrite generated files. Generator does not check for existing customizations — re-running on a finished resource is destructive. Always commit before re-running.

## Common pitfalls

- **`make:crud` not found** — service provider not registered. Run `php artisan optimize:clear`.
- **Generated form throws "Method [flatPicker] not found"** — tablar-kit version mismatch. Generator emits FormBuilder calls assuming current tablar-kit method names. Bump tablar-kit to latest.
- **Routes appended twice on rerun** — generator appends to `routes/web.php` without checking for existing block. Search for the resource's route group and remove duplicates manually.
- **Migration runs but page 500s** — usually missing `belongsTo` / `hasMany` relationship the generator inferred but didn't fully wire. Check the generated Model.

## Configuration recap

`config/crud.php` (only present if user published in step 2):
- Stub paths.
- Default namespace mapping.
- Naming overrides.

Defaults work for most apps.

## Related

- Skill `tablar-crud-generator-development` — full flag list, stub customization, regeneration strategy.
- Skill `tablar-kit-forms-development` — FormBuilder methods used by generated forms.
- Slash command `/laravel-boost:install-laravel-ajax-builder` — sibling for AJAX flows.
- Source: `packages/takielias/tablar-crud-generator/src/Commands/CrudGenerator.php`, `src/Commands/GeneratorCommand.php`.

## Out of scope

- Authoring custom stubs — defer to `tablar-crud-generator-development` skill after install.
- Regeneration safety / diff strategy — defer.
