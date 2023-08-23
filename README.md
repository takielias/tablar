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

Tabler Dashboard Preset built for Fast Development with Dark Mode. It also contains Dynamic menu generator that helps to maintain the navigation permission easily.

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

Laravel Table Admin Dashboard Package. It allows you to make beautiful feature righ admin dashboard Template using
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
- Node js >= 16.0

### Installation

```shell
composer require takielias/tablar
```

First Install the preset

```shell
php artisan ui tablar:install
```
Now

```shell
npm install
```
Finally, export the config, assets & auth scaffolding.

```shell
php artisan ui tablar:export-all
```
N.B: You may export individual component. For assets.
```shell
php artisan ui tablar:export-asset
```
For config.
```shell
php artisan ui tablar:export-config
```
For Js.
```shell
php artisan ui tablar:export-js
```
For auth scaffolding.
```shell
php artisan ui tablar:export-auth
```
For auth views.
```shell
php artisan ui tablar:export-views
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

To use the blade template provided by this package, just create a new blade file and extend the layout with @extends('tablar::page'). You may use the following code snippet:

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

### View : Use the below artisan command to export the views
```shell
php artisan ui tablar:export-views
```
All view files would be exported to **resources/views/vendor/tablar** directory. You can customize as your requirements.

### Menu: Use the below artisan command to export the config file.
For config.
```shell
php artisan ui tablar:export-config
```

A **tablar.php** file would be available into your config folder. You may customize your navigation bar in menu block

`'menu' => [
.......
]`

# That's It.

<!-- CONTRIBUTING -->

## Contributing

Contributions are what makes the open source community such an amazing place to learn, inspire, and create. Any
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

Hey dude! Help me out for a cup of ☕!

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
