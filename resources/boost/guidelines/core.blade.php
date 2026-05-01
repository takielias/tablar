## Tablar

@verbatim
takielias/tablar — Tabler-based Laravel admin starter kit. Master layout, sidebar/top menu driven by `config/tablar.php`, profile + settings stubs, header/footer chrome arrays. Pairs with siblings tablar-kit, tablar-crud-generator, takielias/lab.
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

- Layout published to `resources/views/vendor/tablar/master.blade.php`. Customise here, NEVER in `vendor/`.
- Menu config key `menu` in `config/tablar.php`. Item keys: `text`, `url`/`route`, `icon` (`ti ti-*`), `submenu`, `header`, `can` (Laravel Gate), `target`, `label` (badge).
- Theme: `localStorage['tablar.theme']` carries `'light'|'dark'|'auto'`. Inline `<head>` script syncs to Tabler core's `tabler-theme` key — never write `tabler-theme` directly.
- Empty `header_buttons` / `footer_buttons` array hides the chrome; defaults ship Source code + Sponsor links.

### See also

- `tablar-installation-development`, `tablar-menu-development`.
- Slash: `/laravel-boost:install-tablar-kit`, `/laravel-boost:install-tablar-crud-generator`, `/laravel-boost:install-laravel-ajax-builder`.
