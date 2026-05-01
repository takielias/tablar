## Tablar

@verbatim
takielias/tablar is a Tabler-based Laravel admin starter kit. Ships master layout, top-right user dropdown, sidebar/top menu driven by `config/tablar.php`, profile + settings stubs (with soft-delete + appearance toggle), and a header/footer chrome configured by arrays. Pairs with sibling packages takielias/tablar-kit (UI components + FormBuilder), takielias/tablar-crud-generator (resource scaffolder), and takielias/laravel-ajax-builder (fluent fetch).
@endverbatim

### Install

@verbatim
<code-snippet name="install" lang="bash">
composer require takielias/tablar
php artisan tablar:install
npm install && npm run build
</code-snippet>
@endverbatim

### Conventions

- Master layout published to `resources/views/vendor/tablar/master.blade.php`. Customise here, NEVER in `vendor/`.
- Menu defined in `config/tablar.php` under the `menu` key. Each item supports `name`, `route` OR `url`, `icon` (Tabler icon class e.g. `ti ti-home`), optional `submenu`, optional `header` (renders as section label).
- `'use_route_url' => true` (default) resolves menu `route` keys via `route()`. Set `false` to treat all paths as raw URLs.
- Layout selection via `'layout'` config key (default `'horizontal'`).
- Theme: `localStorage['tablar.theme']` carries `'light' | 'dark' | 'auto'`. Inline `<head>` script in `master.blade.php` applies + syncs to Tabler core's `tabler-theme` localStorage key. Do NOT write `tabler-theme` directly — use `tablar.theme` and dispatch `window.dispatchEvent(new CustomEvent('tablar:theme-change', { detail: 'dark' }))` to update.
- Header / footer chrome configured via `'header_buttons'` and `'footer_buttons'` arrays. Empty array hides chrome entirely.
- `'enable_notifications' => true` (default) toggles the bell-icon notifications dropdown.
- Profile + settings routes named `profile` and `settings` by default (config keys `profile_url`, `setting_url`). Stub controllers + views ship via `tablar:install`.

### Common pitfalls

- Adding a route name into menu config that doesn't exist → `RouteNotFoundException` on render. Either point at a real named route OR use `'url'` with a raw path.
- Editing `vendor/takielias/tablar/resources/views/...` directly — gets clobbered on `composer update`. Always run `tablar:install` first.
- Forgetting `npm run build` after install — published `tabler.scss` will not load and the layout looks unstyled.
- Writing to `localStorage['tabler-theme']` directly — Tabler core's own `tabler-theme.js` strips `data-bs-theme` when value matches default `light`. Always go through our wrapper key.

### See also

- `tablar-installation-development` — full install/upgrade walkthrough, layout selection, post-install patches.
- `tablar-menu-development` — menu config schema + dynamic menus.
- Slash commands: `/laravel-boost:install-tablar-kit`, `/laravel-boost:install-tablar-crud-generator`, `/laravel-boost:install-laravel-ajax-builder` for sibling-package wiring.
