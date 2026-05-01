---
name: tablar-menu-development
description: Author tablar sidebar/top menus via config/tablar.php — full item schema (text, url/route, icon, submenu, can, label, type), filter pipeline (GateFilter, HrefFilter, ActiveFilter, etc.), permission gating with Laravel Gate, special widget items (search, darkmode, fullscreen, notification), dynamic runtime menus.
---

# Tablar — Menu

## When to use this skill

- Adding/removing menu items in `config/tablar.php`.
- Adding nested submenus.
- Conditionally hiding items by Laravel Gate ability.
- Building runtime-driven menus (database-backed).
- Adding badges, search widgets, theme/fullscreen toggles, or notifications to the navbar.

## Item schema

Every menu item is an associative array. Every field is optional unless noted. All keys are read by either the rendering partials in `resources/views/partials/navbar/` or the filter pipeline in `src/Menu/Filters/`.

| Key | Type | Notes |
|---|---|---|
| `text` | string | Display label. Required for clickable items. |
| `url` | string | Raw URL or path. Resolved via `UrlGenerator::to()` by `HrefFilter`. |
| `route` | string OR `[name, params]` | Named route. `HrefFilter` resolves via `route()`. Use this OR `url`, not both. |
| `icon` | string | Tabler icon class, e.g. `ti ti-home`. |
| `icon_color` | string | Tabler color suffix; renders as `text-{color}`. |
| `submenu` | array | Nested item array. Each child follows the same schema. |
| `header` | bool | Renders as a non-clickable section header. Use with `text`. |
| `id` | string | DOM id on the rendered `<li>` (or trigger). |
| `class` | string | Extra CSS classes on the menu item. |
| `submenu_class` | string | Classes on the submenu wrapper. |
| `target` | string | `_blank` etc. Renders as `target` attr. |
| `label` | string | Badge label (e.g. `New`). |
| `label_color` | string | Badge color suffix; renders as `bg-{color}`. |
| `can` | string OR string[] | Laravel Gate ability/abilities. `GateFilter` calls `Gate::any($can, $model)`. Item filtered out if denied. |
| `model` | mixed | Extra arg passed to `Gate::any()` — typically a model instance for policy checks. |
| `type` | string | Special widget item. Values: `'navbar-search'`, `'fullscreen-widget'`, `'darkmode-widget'`, `'navbar-notification'`. See "Special widget items" below. |

After the filter pipeline runs:
- `href` is added (computed from `url` or `route`).
- `restricted` is added (truthy when `can` denies — item is dropped before render).

## Filter pipeline

`config('tablar.filters')` lists filter classes applied in order:

```php
'filters' => [
    \TakiElias\Tablar\Menu\Filters\GateFilter::class,        // applies `can`, sets `restricted`
    \TakiElias\Tablar\Menu\Filters\HrefFilter::class,        // resolves url/route → href
    \TakiElias\Tablar\Menu\Filters\SearchFilter::class,      // search widgets
    \TakiElias\Tablar\Menu\Filters\ActiveFilter::class,      // marks active item
    \TakiElias\Tablar\Menu\Filters\ClassesFilter::class,     // merges class arrays
    \TakiElias\Tablar\Menu\Filters\LangFilter::class,        // translates `text` via __()
    \TakiElias\Tablar\Menu\Filters\DataFilter::class,        // emits data-* attrs
],
```

Custom filters: implement `TakiElias\Tablar\Menu\Filters\FilterInterface` (single method `transform(array $item): array`) and append to the array.

## Recipes

### 1. Static menu with submenu + badge

```php
'menu' => [
    ['text' => 'Home', 'icon' => 'ti ti-home', 'route' => 'home'],
    ['text' => 'Admin', 'icon' => 'ti ti-shield', 'submenu' => [
        ['text' => 'Users', 'route' => 'users.index'],
        ['text' => 'Roles', 'route' => 'roles.index', 'label' => 'New', 'label_color' => 'green'],
    ]],
],
```

### 2. Permission-gated menu via Laravel Gate

```php
['text' => 'Admin', 'icon' => 'ti ti-shield', 'can' => 'manage-admin', 'submenu' => [
    ['text' => 'Users', 'route' => 'users.index', 'can' => 'view-users'],
    ['text' => 'Reports', 'route' => 'reports.index', 'can' => ['view-reports', 'export-reports']],
]],
```

`can` accepts a single ability or an array; `GateFilter` calls `Gate::any($abilities, $model ?? [])`. Array semantics = OR. For AND semantics, define a single composite ability in a Gate definition.

For policy checks against a model instance:

```php
['text' => 'My Posts', 'route' => 'posts.mine', 'can' => 'viewAny', 'model' => Post::class],
```

If you use spatie/laravel-permission, register Gate abilities for each permission name in `AppServiceProvider`:

```php
Gate::before(fn ($user, $ability) => $user->hasPermissionTo($ability) ?: null);
```

After that, `'can' => 'edit articles'` in menu config works against spatie permissions.

### 3. Section header

```php
['header' => true, 'text' => 'Application'],
['text' => 'Dashboard', 'icon' => 'ti ti-dashboard', 'route' => 'dashboard'],
```

Headers are not clickable and have no `href`.

### 4. External link

```php
['text' => 'Docs', 'icon' => 'ti ti-book', 'url' => 'https://example.com/docs', 'target' => '_blank'],
```

### 5. Route with parameters

```php
['text' => 'Edit User', 'route' => ['users.edit', ['user' => auth()->id()]]],
```

`HrefFilter` unpacks the array as `[$name, $params]` and calls `route($name, $params)`.

### 6. Dynamic menu (runtime override)

In `App\Providers\AppServiceProvider::boot()`:

```php
public function boot(): void
{
    config(['tablar.menu' => $this->buildMenu()]);
}

protected function buildMenu(): array
{
    return Cache::remember('tablar.menu.'.auth()->id(), 60, function () {
        $base = config('tablar.menu', []);
        $extra = MenuItem::orderBy('position')->get()->map(fn ($m) => [
            'text' => $m->name, 'route' => $m->route, 'icon' => $m->icon,
        ])->all();
        return array_merge($base, $extra);
    });
}
```

Filter pipeline still runs over the merged config — gate/href/active filters all apply to dynamic items.

## Special widget items

Set `'type'` to opt into a non-clickable navbar widget:

| `type` | What renders |
|---|---|
| `'navbar-search'` | Custom search widget (also requires `'text'`). |
| `'fullscreen-widget'` | Toggle button — switches browser fullscreen. |
| `'darkmode-widget'` | Light/dark theme switcher (writes to `localStorage['tablar.theme']`). |
| `'navbar-notification'` | Notification dropdown. Requires `id`, `icon`, `type`, plus `url` OR `route`. |

Example:

```php
'menu_navbar' => [
    ['type' => 'darkmode-widget'],
    ['type' => 'fullscreen-widget'],
    ['id' => 'notifications', 'icon' => 'ti ti-bell', 'type' => 'navbar-notification', 'route' => 'notifications.index'],
],
```

(Verify the exact config key your app uses for the navbar slot — defaults vary across published configs.)

## Common pitfalls

- **`'route'` value is a URL, not a route name** — `HrefFilter` will throw `RouteNotFoundException`. Either fix the value OR switch the key to `'url'`.
- **Menu item with both `url` and `route`** — `HrefFilter` returns `url` first; `route` is silently ignored. Keep one.
- **Empty `submenu` array shows parent as a clickable item** — clear the key entirely or guard with a runtime check before assigning.
- **`can` ignored** — `GateFilter` requires `config('tablar.filters')` to include it. If the filter array was overridden (e.g. by a published config from an older version), restore it via `php artisan tablar:export-config --force`.
- **Icon classes silent fail** — Tabler icon font must be loaded (`tablar:install` + `npm run build`). Verify `<link rel="stylesheet" href="...tabler-icons.css">` is in the `<head>` after build.

## Configuration reference

Top-level keys related to menus in `config/tablar.php`:

```php
'menu'    => [...],         // sidebar / main menu items
'filters' => [...],         // filter pipeline classes
```

Some published configs also expose `'menu_navbar'`, `'menu_topnav'`, `'menu_topnav_right'` — verify against your `config/tablar.php` after `tablar:install`.

## Related

- Skill `tablar-installation-development` — install + post-install patches.
- Source: `resources/views/partials/navbar/`, `src/Menu/Filters/`, `src/Helpers/MenuItemHelper.php`, `src/Helpers/NavbarItemHelper.php`.
