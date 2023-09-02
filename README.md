![Laravel Tabler Admin Dashboard](https://banners.beyondco.de/Laravel%20Tabler%20Admin%20Dashboard.png?theme=light&packageManager=composer+require&packageName=takielias%2Ftablar&pattern=topography&style=style_1&description=Laravel+%2B+Tabler+%3D+Tablar+%23+Admin+Dashboard+with+Dark+Mode.&md=1&showWatermark=0&fontSize=125px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

## Tabler + Laravel = TabLar

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<a href="https://www.buymeacoffee.com/takielias" target="_blank"> <img align="left" src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" height="50" width="210" alt="takielias" /></a>

<br/>
<br/>

<!-- PROJECT LOGO -->

<p align="center">

<br/> 

#### Tabler: A Laravel Dashboard Preset Featuring Dark Mode and Dynamic Menu Generation for Effortless Navigation and Permission Management.

Inspired By Laravel Adminlte `jeroennoten/Laravel-AdminLTE`

### Demo

https://tablar.ebuz.xyz

```shell
tablar@ebuz.xyz
12345678
```

### Laravel Tablar CRUD Generator https://github.com/takielias/tablar-crud-generator

![Tablar Light](https://user-images.githubusercontent.com/38932580/194739188-0034be57-d738-4d0d-bc49-8c96c8b6e89c.png)

![Tablar Dark](https://user-images.githubusercontent.com/38932580/194739128-fa092837-10d7-47eb-8f66-ac22b3c26a7c.png)

<br>
<a href="https://www.buymeacoffee.com/takielias" target="_blank"> <img align="left" src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" height="50" width="210" alt="takielias" /></a>
</p>
<br><br>

Laravel Table Admin Dashboard Package. It allows you to make beautiful feature rich admin dashboard Template using
Laravel & Super Fast https://vitejs.dev.

<!-- TABLE OF CONTENTS -->

## Table of Contents

* [Getting Started](#getting-started)
    * [Prerequisites](#prerequisites)
    * [Installation](#installation)
* [Usage](#usage)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

<!-- GETTING STARTED -->

## Getting Started

This is an example of how you may give instructions on setting up your project locally. To get a local copy up and
running, follow these simple example steps.

### Prerequisites

- PHP >= 8.1
- Fresh Laravel Framework (10.* recommended)
- Composer
- Node js >= 18.0

### Installation

```shell
composer require takielias/tablar
```

First Install the preset

```shell
php artisan tablar:install
```

Now

```shell
npm install
```

Finally, export the config, assets & auth scaffolding.

```shell
php artisan tablar:export-all
```

N.B: You may export individual component. For assets.

```shell
php artisan tablar:export-assets
```

For config.

```shell
php artisan tablar:export-config
```

For Js.

```shell
php artisan tablar:export-js
```

For auth scaffolding.

```shell
php artisan tablar:export-auth
```

For auth views.

```shell
php artisan tablar:export-views
```

Before staring the server don't forget to run migrate

```
php artisan migrate
```

Now run

```
npm run dev
```

Now visit your login route

![Login](https://user-images.githubusercontent.com/38932580/194739218-67066af4-ea1c-4a93-bc9e-14a332c686d5.png)

N.B : If you use virtual host for your laravel application, don't forget to change the **APP_URL**

```shell
APP_URL=http://your virtual host
```

<!-- USAGE EXAMPLES -->

## Usage

To use the blade template provided by this package, just create a new blade file and extend the layout with @extends('
tablar::page'). You may use the following code snippet:

```shell
@extends('tablar::page')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Empty page
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <!-- Page Content goes here -->

        </div>
    </div>
@endsection
```

## External style Tag

If you need to use custom script in different pages, please follow the instruction below to achieve it.

```shell
@section('css')
    <style>
     ......
    </style>
@stop
```

## External script Tag

If you need to use custom script in different pages, please follow the instruction below to achieve it.

```shell
@section('js')
    <script type="module">
        $(".btn-test").click(function () {
            alert("The Button was clicked.");
        });
    </script>
@stop
```

**Enable Display Alert**

Make `display_alert` to `true` from **tablar.php** config file

**Use Tabler Pagination**

`
{!! $modelName->links('tablar::pagination') !!}
`

## Customization

## Views
Use the below artisan command to export the views

```shell
php artisan tablar:export-views
```

All view files would be exported to **resources/views/vendor/tablar** directory. You can customize as your requirements.

## Menu
Use the below artisan command to export the config file.

For config.

```shell
php artisan tablar:export-config
```

You can specify the set of menu items to display in the top navbar. A menu item representing a link should have a text
attribute and an url (or route) attribute. Also, and optionally, you can use the icon attribute to specify an icon from
Tablar Icon for every menu item. There is a can attribute that can be used as a filter with the Laravel's built-in Gate
functionality. Even more, you can create a nested menu using the submenu attribute.

Here is a basic example that will give you a quick overview of the menu configuration:

```php
    'menu' => [
        // Navbar items:
        [
            'text' => 'Home',
            'icon' => 'ti ti-home',
            'label' => 4,
            'label_color' => 'success',
        ],
        [
            'text' => 'Support',
            'url' => '#',
            'icon' => 'ti ti-help',
            'submenu' => [
                [
                    'text' => 'Ticket',
                    'url' => '#',
                    'icon' => 'ti ti-article'
                ]
            ],
        ],

    ],
```

We are going to give a summary of the available attributes for the menu items.
Take in consideration that most of
these attributes are optional and will be explained in future with more details.

 Attribute                                      | Description                                                  
------------------------------------------------|--------------------------------------------------------------
 [active](#the-active-attribute)                | To define when the item should have the active style.        
 [can](#the-can-attribute)                      | Permissions of the item for use with Laravel's Gate.         
 [classes](#the-classes-attribute)              | To add custom classes to a menu item.                        
 [icon](#the-icon-and-icon_color-attributes)    | A font awesome icon for the item.                            
 [id](#the-id-attribute)                        | To define an `id` for the menu item.                         
 [label](#the-label-and-label_color-attributes) | Text for a badge associated with the item.                   
 [route](#the-route-attribute)                  | A route name, usually used on link items.                    
 [submenu](#the-submenu-attribute)              | Array with child items that enables nested menus definition. 
 [target](#the-target-attribute)                | The underlying target attribute for link items.              
 [text](#the-text-attribute)                    | Text representing the name of the item.                      
 [url](#the-url-attribute)                      | An URL path, normally used on link items.                    

###
Now, we are going to review all of these attributes with more detail:

#### The __`active`__ Attribute:

By default, a menu item is considered active if any of the following conditions holds:

- The current path exactly matches the `url` attribute.
- The current path without the query parameters matches the `url` attribute.
- If it has a submenu containing an active menu item.

```php
[
    'text'   => 'Pages',
    'url'    => 'pages',
    'active' => ['pages', 'content', 'content*', 'regex:@^content/[0-9]+$@']
]
```

In the previous case, the menu item will be considered active for all the next URLs:

- `http://my.domain.com/pages`
- `http://my.domain.com/content`
- `http://my.domain.com/content-user` (because `content*`)
- `http://my.domain.com/content/1234` (because `regex:@^content/[0-9]+$@`)

#### The __`can`__ Attribute:

You may use the `can` attribute if you want to conditionally show a menu item. This integrates with
the [Laravel's Gate](https://laravel.com/docs/authorization#gates) functionality. If you need to conditionally show a
header item, you need to wrap it in an array using the `header` attribute. You can also use multiple conditions entries
with an array, check the next example for details:

```php
[
    [
        'header' => 'Posts',
        'can'    => 'read-post',
    ],
    [
        'text' => 'Add new post',
        'url'  => 'admin/blog/new',
        'can'  => ['add-post', 'other-right'],
    ],
]
```

So, for the previous example the header will show only if the user has the `read-post` permission, and the link will
show if the user has the `add-post` or `other-right` permissions.

#### The __`classes`__ Attribute:

This attribute provides a way to add custom classes to a particular menu item. The value should be a string with one or
multiple class names, similar to the HTML `class` attribute. For example, you can make a colorful `HEADER` item centered
on the left sidebar with the next definition:

```php
[
    'header'   => 'account_settings',
    'classes'  => 'text-yellow text-bold text-center',
]
```

Or you can highlight an important link item with something like this:

```php
[
    'text'     => 'Important Link',
    'url'      => 'important/link',
    'icon'     => 'ti ti-alert-triangle',
    'classes'  => 'text-danger text-uppercase',
]
```

#### The __`icon`__ Attributes:

This attribute is optional, and you will get
an [open circle](https://tabler-icons.io/) if you leave it out. The available icons
that you can use are those from [Tabler Icons](https://tabler-icons.io/). Just specify the name of the icon, and it
will appear in front of your menu item. Example:

```php
[
    'text'       => 'profile',
    'url'        => 'user/profile',
    'icon'       => 'ti ti-user',
]
```

#### The __`id`__ Attribute:

This attribute is optional and just provide a way to add an `id` to the element that wraps the menu item, generally
a `<li>` tag. This can be useful when you need to target the menu item from `Javascript` or `jQuery` in order to perform
updates on it.

```php
[
    'text'       => 'profile',
    'url'        => 'user/profile',
    'id'         => 'profile-id',
]
```

#### The __`route`__ Attribute:

You can use this attribute to assign a Laravel route name to a link item, if you choose to use this attribute, then
don't combine it with the `url` attribute, for example:

```php
[
    'text'  => 'Profile',
    'route' => 'admin.profile',
    'icon'  => 'ti ti-user',
]
```

Even more, you can define a route with parameters using an array where the first value is the route name and the second
value an array with the parameters, as shown next:

```php
[
    'text'  => 'Profile',
    'route' => ['admin.profile', ['userID' => '673']],
    'icon'  => 'ti ti-user',
]
```

#### The __`submenu`__ Attribute:

This attribute provides a way to create a menu item containing child items. With this feature you can create nested
menus. You can create a menu with items in the sidebar and/or the top navbar. Example:

```php
[
    'text'    => 'menu',
    'icon'    => 'ti ti-share',
    'submenu' => [
        [
            'text' => 'child 1',
            'url'  => 'menu/child1',
        ],
        [
            'text' => 'child 2',
            'url'  => 'menu/child2',
        ],
    ],
]
```
#### The __`target`__ Attribute:

This attribute is optional and intended to be used only with link items. It represents the underlying `HTML` target attribute for a link item. As an example, you can setup this attribute to the `'_blank'` value in order to open the link in a new tab.

#### The __`text`__ Attribute:

The value of this attribute is just the descriptive text for a menu item (except for headers).

#### The __`url`__ Attribute:

The value of this attribute should be the URL for a link item. You can use a full URL with the domain part or without
it. Don't combine this attribute with the `route` attribute. Examples:

```php
[
    'text' => 'profile',
    'url'  => 'http://my.domain.com/user/profile',
    'icon' => 'ti ti-user',
],
[
    'text' => 'change_password',
    'url'  => 'admin/settings',
    'icon' => 'ti ti-settings-automation',
],
```

## Custom Menu Filters

You can set the filters you want to include for rendering the menu using the `filters` configuration of the config file.
You can add your own filters to this array after you've created them. You can comment out the `GateFilter` if you don't
want to use Laravel's built in Gate functionality. The current default set of menu filters is:

```php
'filters' => [
        TakiElias\Tablar\Menu\Filters\GateFilter::class,
        TakiElias\Tablar\Menu\Filters\HrefFilter::class,
        TakiElias\Tablar\Menu\Filters\SearchFilter::class,
        TakiElias\Tablar\Menu\Filters\ActiveFilter::class,
        TakiElias\Tablar\Menu\Filters\ClassesFilter::class,
        TakiElias\Tablar\Menu\Filters\LangFilter::class,
        TakiElias\Tablar\Menu\Filters\DataFilter::class,
],
```

If you need to use a custom menu filter, you can add your own menu filters to the previous array. This can be useful,
for example, when you are using a third-party package for authorization (instead of the Laravel's Gate functionality).

In order to provide more details, we are going to show an example of how you can configure
the [Laratrust Package](https://laratrust.santigarcor.me/). Start by creating your custom filter implementation in `App\Filter`:

```php
<?php

namespace App\Filter;

use TakiElias\Tablar\Menu\Filters\FilterInterface;
use Laratrust\Laratrust;

class RolePermissionMenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if (isset($item['permission']) && ! Laratrust::isAbleTo($item['permission'])) {
            $item['restricted'] = true;
        }

        return $item;
    }
}
```

In order to use [Laravel Permission](https://github.com/spatie/laravel-permission), we are going to show an example of how you can configure. Start by creating your custom filter
implementation:

```php
<?php

namespace App\Filter;

use Illuminate\Support\Facades\Auth;
use TakiElias\Tablar\Menu\Builder;
use TakiElias\Tablar\Menu\Filters\FilterInterface;

class RolePermissionMenuFilter implements FilterInterface
{
    public function transform($item, Builder $builder)
    {
        if (!$this->isVisible($item)) {
            return false;
        }

        if (isset($item['header'])) {
            $item = $item['header'];
        }

        return $item;
    }

    protected function isVisible($item)
    {
        // check if a user is a member of specified role(s)
        if (isset($item['hasAnyRole'])) {
            if (!(Auth::user())->hasAnyRole($item['hasAnyRole'])) {
                // not a member of any valid hasAnyRole; check if user has been granted explicit permission
                if (isset($item['hasAnyPermission']) && (Auth::user())->hasAnyPermission($item['hasAnyPermission'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } elseif (isset($item['hasRole'])) {
            if (!(Auth::user())->hasRole($item['hasRole'])) {
                if (isset($item['hasAnyPermission']) && (Auth::user())->hasAnyPermission($item['hasAnyPermission'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            // valid hasAnyRole not defined; check if user has been granted explicit permission
            if (isset($item['hasAnyPermission'])) {
                // permissions are defined
                if ((Auth::user())->hasAnyPermission($item['hasAnyPermission'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // no valid hasAnyRole or permissions defined; allow for all users
                return true;
            }
        }
    }
}

```

And then add the following configuration to the `config/tablar.php` file:

```php
'filters' => [
    ...
       //TakiElias\Tablar\Menu\Filters\GateFilter::class,
        TakiElias\Tablar\Menu\Filters\HrefFilter::class,
        TakiElias\Tablar\Menu\Filters\SearchFilter::class,
        TakiElias\Tablar\Menu\Filters\ActiveFilter::class,
        TakiElias\Tablar\Menu\Filters\ClassesFilter::class,
        TakiElias\Tablar\Menu\Filters\LangFilter::class,
        TakiElias\Tablar\Menu\Filters\DataFilter::class,
        
        MyApp\RolePermissionMenuFilter::class,
]
```

A **tablar.php** file would be available into your config folder.

# That's It.

<!-- CONTRIBUTING -->

## Contributing

Contributions are what makes the open source community such an amazing place to learn, inspire and create. Any
contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE` for more information.

<!-- CONTACT -->

## Contact

Taki Elias - [@takiele](https://twitter.com/takiele) - [https://ebuz.xyz](https://ebuz.xyz) - taki.elias@gmail.com

## Support on Buy Me A Coffee

Hello!!! Help me out for a cup of ☕!

<a href="https://www.buymeacoffee.com/takielias" target="_blank">
<img align="left" src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" height="50" width="210" alt="takielias" /></a>

<br><br>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/takielias/tablar.svg?style=flat-square

[contributors-url]: https://github.com/takielias/tablar/graphs/contributors

[forks-shield]: https://img.shields.io/github/forks/takielias/tablar.svg?style=flat-square

[forks-url]: https://github.com/takielias/tablar/network/members

[stars-shield]: https://img.shields.io/github/stars/takielias/tablar.svg?style=flat-square

[stars-url]: https://github.com/takielias/tablar/stargazers

[issues-shield]: https://img.shields.io/github/issues/takielias/tablar.svg?style=flat-square

[issues-url]: https://github.com/takielias/tablar/issues

[license-shield]: https://img.shields.io/github/license/takielias/tablar.svg?style=flat-square

[license-url]: https://github.com/takielias/tablar/blob/master/LICENSE.txt

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=flat-square&logo=linkedin&colorB=555

[linkedin-url]: https://linkedin.com/in/takielias

[product-screenshot]: images/screenshot.png

[ico-version]: https://img.shields.io/packagist/v/takielias/tablar.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/takielias/tablar.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/takielias/tablar

[link-downloads]: https://packagist.org/packages/takielias/tablar

[link-author]: https://github.com/takielias
