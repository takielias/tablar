<?php

namespace TakiElias\Tablar\Tests\Menu;

use Illuminate\Routing\Route;
use TakiElias\Tablar\Menu\Builder;
use TakiElias\Tablar\Tests\TestCase;

class BuilderTest extends TestCase
{
    public function test_add_one_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/']);

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
    }

    public function test_add_multiple_items()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add('MENU');
        $builder->add(['text' => 'Home', 'url' => '/']);
        $builder->add(['text' => 'About', 'url' => '/about']);

        $this->assertCount(3, $builder->menu);
        $this->assertEquals('MENU', $builder->menu[0]);
        $this->assertEquals('Home', $builder->menu[1]['text']);
        $this->assertEquals('/', $builder->menu[1]['url']);
        $this->assertEquals('About', $builder->menu[2]['text']);
        $this->assertEquals('/about', $builder->menu[2]['url']);
    }

    public function test_add_multiple_items_at_once()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(
            ['text' => 'Home', 'url' => '/'],
            ['text' => 'About', 'url' => '/about']
        );

        $this->assertCount(2, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
        $this->assertEquals('About', $builder->menu[1]['text']);
        $this->assertEquals('/about', $builder->menu[1]['url']);
    }

    public function test_add_after_one_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addAfter('home', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(2, $builder->menu);
        $this->assertEquals('Profile', $builder->menu[1]['text']);
        $this->assertEquals('/profile', $builder->menu[1]['url']);
    }

    public function test_add_after_one_not_found_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addAfter('foo', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
    }

    public function test_add_after_multiple_items()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addAfter('home', ['text' => 'About', 'url' => '/about']);
        $builder->addAfter('home', ['text' => 'Profile', 'url' => '/profile']);

        $this->multipleItemTests($builder);
    }

    public function test_add_after_multiple_items_at_once()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);

        $builder->addAfter('home',
            ['text' => 'Profile', 'url' => '/profile'],
            ['text' => 'About', 'url' => '/about']
        );

        $this->multipleItemTests($builder);
    }

    public function test_add_after_one_sub_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(
            [
                'text' => 'Home',
                'url' => '/',
                'key' => 'home',
                'submenu' => [
                    [
                        'text' => 'Test',
                        'url' => '/test',
                        'key' => 'test',
                    ],
                ],
            ]
        );
        $builder->addAfter('test', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(1, $builder->menu);
        $this->assertCount(2, $builder->menu[0]['submenu']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][1]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][1]['url']);
    }

    public function test_add_before_one_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Profile', 'url' => '/profile', 'key' => 'profile']);
        $builder->addBefore('profile', ['text' => 'Home', 'url' => '/']);

        $this->assertCount(2, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
    }

    public function test_add_before_one_not_found_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addBefore('foo', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
    }

    public function test_add_before_one_sub_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(
            [
                'text' => 'Home',
                'url' => '/',
                'key' => 'home',
                'submenu' => [
                    [
                        'text' => 'Test',
                        'url' => '/test',
                        'key' => 'test',
                    ],
                ],
            ]
        );
        $builder->addBefore('test', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(1, $builder->menu);
        $this->assertCount(2, $builder->menu[0]['submenu']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][0]['url']);
    }

    public function test_add_before_multiple_items()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Profile', 'url' => '/profile', 'key' => 'profile']);
        $builder->addBefore('profile', ['text' => 'Home', 'url' => '/']);
        $builder->addBefore('profile', ['text' => 'About', 'url' => '/about']);

        $this->assertCount(3, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
        $this->assertEquals('About', $builder->menu[1]['text']);
        $this->assertEquals('/about', $builder->menu[1]['url']);
        $this->assertEquals('Profile', $builder->menu[2]['text']);
        $this->assertEquals('/profile', $builder->menu[2]['url']);
    }

    public function test_add_before_multiple_items_at_once()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Profile', 'url' => '/profile', 'key' => 'profile']);

        $builder->addBefore('profile',
            ['text' => 'Home', 'url' => '/'],
            ['text' => 'About', 'url' => '/about']
        );

        $this->assertCount(3, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
        $this->assertEquals('About', $builder->menu[1]['text']);
        $this->assertEquals('/about', $builder->menu[1]['url']);
        $this->assertEquals('Profile', $builder->menu[2]['text']);
        $this->assertEquals('/profile', $builder->menu[2]['url']);
    }

    public function test_add_in_one_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addIn('home', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(1, $builder->menu);
        $this->assertCount(1, $builder->menu[0]['submenu']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][0]['url']);
    }

    public function test_add_in_one_not_found_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addIn('foo', ['text' => 'Profile', 'url' => '/profile']);

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
    }

    public function test_add_in_multiple_items()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->addIn('home', ['text' => 'Profile', 'url' => '/profile']);
        $builder->addIn('home', ['text' => 'About', 'url' => '/about']);

        $this->assertCount(1, $builder->menu);
        $this->assertCount(2, $builder->menu[0]['submenu']);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][0]['url']);
        $this->assertEquals('About', $builder->menu[0]['submenu'][1]['text']);
        $this->assertEquals('/about', $builder->menu[0]['submenu'][1]['url']);
    }

    public function test_add_in_multiple_items_at_once()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);

        $builder->addIn('home',
            ['text' => 'Profile', 'url' => '/profile'],
            ['text' => 'About', 'url' => '/about']
        );

        $this->assertCount(1, $builder->menu);
        $this->assertCount(2, $builder->menu[0]['submenu']);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][0]['url']);
        $this->assertEquals('About', $builder->menu[0]['submenu'][1]['text']);
        $this->assertEquals('/about', $builder->menu[0]['submenu'][1]['url']);
    }

    public function test_remove_one_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->add(['text' => 'Profile', 'url' => '/profile', 'key' => 'profile']);

        $builder->remove('home');

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Profile', $builder->menu[0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['url']);
    }

    public function test_remove_one_not_found_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->remove('foo');

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
    }

    public function test_remove_multiple_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);
        $builder->add(['text' => 'About', 'url' => '/about', 'key' => 'about']);
        $builder->add(['text' => 'Profile', 'url' => '/profile', 'key' => 'profile']);

        $builder->remove('home');
        $builder->remove('about');

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Profile', $builder->menu[0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['url']);
    }

    public function test_remove_one_sub_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add([
            'text' => 'Home',
            'url' => '/',
            'key' => 'home',
            'submenu' => [
                ['text' => 'About', 'url' => '/about', 'key' => 'about'],
                ['text' => 'Profile', 'url' => '/profile', 'key' => 'profile'],
            ],
        ]);

        $builder->remove('about');

        $this->assertCount(1, $builder->menu);
        $this->assertCount(1, $builder->menu[0]['submenu']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][0]['url']);
    }

    public function test_remove_multiple_sub_item()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add([
            'text' => 'Home',
            'url' => '/',
            'key' => 'home',
            'submenu' => [
                ['text' => 'About', 'url' => '/about', 'key' => 'about'],
                ['text' => 'Profile', 'url' => '/profile', 'key' => 'profile'],
                ['text' => 'Demos', 'url' => '/demos', 'key' => 'demos'],
            ],
        ]);

        $builder->remove('about');
        $builder->remove('demos');

        $this->assertCount(1, $builder->menu);
        $this->assertCount(1, $builder->menu[0]['submenu']);
        $this->assertEquals('Profile', $builder->menu[0]['submenu'][0]['text']);
        $this->assertEquals('/profile', $builder->menu[0]['submenu'][0]['url']);
    }

    public function test_item_key_exists()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);

        $this->assertTrue($builder->itemKeyExists('home'));
    }

    public function test_item_sub_key_exists()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add([
            'text' => 'Home',
            'url' => '/',
            'key' => 'home',
            'submenu' => [
                ['text' => 'About', 'url' => '/about', 'key' => 'about'],
                ['text' => 'Profile', 'url' => '/profile', 'key' => 'profile'],
            ],
        ]);

        $this->assertTrue($builder->itemKeyExists('home'));
        $this->assertTrue($builder->itemKeyExists('about'));
        $this->assertTrue($builder->itemKeyExists('profile'));
        $this->assertFalse($builder->itemKeyExists('demos'));
    }

    public function test_item_sub_sub_key_exists()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add([
            'text' => 'Home',
            'url' => '/',
            'key' => 'home',
            'submenu' => [
                [
                    'text' => 'About',
                    'url' => '/about',
                    'key' => 'about',
                    'submenu' => [
                        ['text' => 'Profile', 'url' => '/profile', 'key' => 'profile'],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($builder->itemKeyExists('home'));
        $this->assertTrue($builder->itemKeyExists('about'));
        $this->assertTrue($builder->itemKeyExists('profile'));
        $this->assertFalse($builder->itemKeyExists('demos'));
    }

    public function test_href_will_be_added()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home', 'url' => '/']);
        $builder->add(['text' => 'About', 'url' => '/about']);

        $this->assertEquals('http://example.com', $builder->menu[0]['href']);
        $this->assertEquals(
            'http://example.com/about',
            $builder->menu[1]['href']
        );
    }

    public function test_default_href()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'Home']);

        $this->assertEquals('#', $builder->menu[0]['href']);
    }

    public function test_submenu_href()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(
            [
                'text' => 'Home',
                'submenu' => [
                    ['text' => 'About', 'url' => '/about'],
                ],
            ]
        );

        $this->assertEquals(
            'http://example.com/about',
            $builder->menu[0]['submenu'][0]['href']
        );
    }

    public function test_multi_level_submenu_href()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(
            [
                'text' => 'Home',
                'submenu' => [
                    [
                        'text' => 'About',
                        'url' => '/about',
                        'submenu' => [
                            ['text' => 'Test', 'url' => '/test'],
                        ],
                    ],
                ],
            ]
        );

        $this->assertEquals(
            'http://example.com/test',
            $builder->menu[0]['submenu'][0]['submenu'][0]['href']
        );
    }

    public function test_route_href()
    {
        $builder = $this->makeMenuBuilder();
        $this->getRouteCollection()->add(new Route('GET', 'about', ['as' => 'pages.about']));
        $this->getRouteCollection()->add(new Route('GET', 'profile', ['as' => 'pages.profile']));

        $builder->add(['text' => 'About', 'route' => 'pages.about']);
        $builder->add(
            [
                'text' => 'Profile',
                'route' => ['pages.profile', ['user' => 'data']],
            ]
        );

        $this->assertEquals('http://example.com/about', $builder->menu[0]['href']);
        $this->assertEquals('http://example.com/profile?user=data', $builder->menu[1]['href']);
    }

    public function test_active_class()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');

        $builder->add(['text' => 'About', 'url' => 'about']);
        $builder->add(['text' => 'Profile', 'url' => 'profile']);

        $this->assertStringContainsString('active', $builder->menu[0]['class']);
        $this->assertStringNotContainsString('active', $builder->menu[1]['class']);
    }

    public function test_active_class_with_route()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');
        $this->getRouteCollection()->add(new Route('GET', 'about', ['as' => 'pages.about']));

        $builder->add(['text' => 'About', 'route' => 'pages.about']);
        $builder->add(['text' => 'Profile', 'url' => 'profile']);

        $this->assertStringContainsString('active', $builder->menu[0]['class']);
        $this->assertStringNotContainsString('active', $builder->menu[1]['class']);
    }

    public function test_active_class_with_submenu_and_url()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');

        $builder->add(
            [
                'text' => 'Menu',
                'submenu' => [
                    [
                        'text' => 'About',
                        'url' => '/about',
                    ],
                ],
            ]
        );

        $this->assertStringContainsString('active', $builder->menu[0]['class']);
        $this->assertStringContainsString('active', $builder->menu[0]['submenu'][0]['class']);
    }

    public function test_active_class_with_submenu_and_route()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');
        $this->getRouteCollection()->add(new Route('GET', 'about', ['as' => 'pages.about']));

        $builder->add(
            [
                'text' => 'Menu',
                'submenu' => [
                    [
                        'text' => 'About',
                        'route' => 'pages.about',
                    ],
                ],
            ]
        );

        $this->assertStringContainsString('active', $builder->menu[0]['class']);
        $this->assertStringContainsString('active', $builder->menu[0]['submenu'][0]['class']);
    }

    public function test_submenu_active_with_hash()
    {
        $builder = $this->makeMenuBuilder('http://example.com/home');

        $builder->add(
            [
                'text' => 'Menu',
                'url' => '#',
                'submenu' => [
                    ['url' => 'home'],
                ],
            ]
        );

        $this->assertTrue($builder->menu[0]['active']);
        $this->assertEquals('active', $builder->menu[0]['class']);
        $this->assertEquals('show', $builder->menu[0]['submenu_class']);
    }

    public function test_top_nav_active_class()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');

        $builder->add(['text' => 'About', 'url' => 'about', 'topnav' => true]);

        $this->assertEquals('active', $builder->menu[0]['class']);
    }

    public function test_top_nav_right_active_class()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');

        $builder->add(['text' => 'About', 'url' => 'about', 'topnav_right' => true]);

        $this->assertEquals('active', $builder->menu[0]['class']);
    }

    public function test_submenu_class_when_add_in_multiple_items()
    {
        $builder = $this->makeMenuBuilder();

        // Add a new link item.

        $builder->add(['text' => 'Home', 'url' => '/', 'key' => 'home']);

        // Add elements inside the previous one, now it will be a submenu item.

        $builder->addIn('home', ['text' => 'Profile', 'url' => '/profile']);
        $builder->addIn('home', ['text' => 'About', 'url' => '/about']);

        // Check the "submenu_class" attribute is added.

        $this->assertTrue(isset($builder->menu[0]['submenu_class']));
    }

    public function test_can()
    {
        $gate = $this->makeGate();
        $gate->define(
            'show-about',
            function () {
                return true;
            }
        );
        $gate->define(
            'show-home',
            function () {
                return false;
            }
        );

        $builder = $this->makeMenuBuilder('http://example.com', $gate);

        $builder->add(
            [
                'text' => 'About',
                'url' => 'about',
                'can' => 'show-about',
            ],
            [
                'text' => 'Home',
                'url' => '/',
                'can' => 'show-home',
            ]
        );

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('About', $builder->menu[0]['text']);
    }

    public function test_can_add_one_restricted_item()
    {
        $gate = $this->makeGate();
        $gate->define(
            'show-home',
            function () {
                return false;
            }
        );

        $builder = $this->makeMenuBuilder('http://example.com', $gate);

        $builder->add(
            [
                'text' => 'Home',
                'url' => '/',
                'can' => 'show-home',
            ]
        );

        $this->assertCount(0, $builder->menu);
    }

    public function test_can_with_invalid_values()
    {
        $gate = $this->makeGate();
        $gate->define(
            'show-about',
            function () {
                return true;
            }
        );
        $gate->define(
            'show-home',
            function () {
                return false;
            }
        );

        $builder = $this->makeMenuBuilder('http://example.com', $gate);

        $builder->add(
            ['text' => 'LinkA', 'url' => 'link_a', 'can' => false],
            ['text' => 'LinkB', 'url' => 'link_b', 'can' => 1024],
            ['text' => 'LinkC', 'url' => 'link_c', 'can' => ''],
            ['text' => 'LinkD', 'url' => 'link_d', 'can' => []],
            ['text' => 'LinkE', 'url' => 'link_e']
        );

        $this->assertCount(5, $builder->menu);
        $this->assertEquals('LinkA', $builder->menu[0]['text']);
        $this->assertEquals('LinkB', $builder->menu[1]['text']);
        $this->assertEquals('LinkC', $builder->menu[2]['text']);
        $this->assertEquals('LinkD', $builder->menu[3]['text']);
        $this->assertEquals('LinkE', $builder->menu[4]['text']);
    }

    public function test_multiple_can()
    {
        $gate = $this->makeGate();
        $gate->define(
            'show-users',
            function () {
                return true;
            }
        );
        $gate->define(
            'edit-user',
            function () {
                return false;
            }
        );
        $gate->define(
            'show-settings',
            function () {
                return false;
            }
        );

        $builder = $this->makeMenuBuilder('http://example.com', $gate);

        $builder->add(
            [
                'text' => 'Users',
                'url' => 'users',
                'can' => ['show-users', 'edit-user'],
            ],
            [
                'text' => 'Settings',
                'url' => 'settings',
                'can' => ['show-settings'],
            ]
        );

        $this->assertCount(1, $builder->menu);
        $this->assertEquals('Users', $builder->menu[0]['text']);
    }

    public function test_can_headers()
    {
        $gate = $this->makeGate();
        $gate->define(
            'show-header',
            function () {
                return true;
            }
        );
        $gate->define(
            'show-settings',
            function () {
                return false;
            }
        );

        $builder = $this->makeMenuBuilder('http://example.com', $gate);

        $builder->add(
            [
                'header' => 'HEADER',
                'can' => 'show-header',
            ],
            [
                'header' => 'SETTINGS',
                'can' => 'show-settings',
            ]
        );

        $this->assertCount(1, $builder->menu);
        $this->assertStringContainsString('HEADER', $builder->menu[0]['header']);
    }

    public function test_lang_translate()
    {

        $builder = $this->makeMenuBuilder('http://example.com');
        $builder->add(['header' => 'Profile']);
        $builder->add(['text' => 'Profile', 'url' => '/profile', 'label' => 'LABELS']);
        $this->menuTest($builder);
        $this->assertEquals('LABELS', $builder->menu[1]['label']);
        $this->assertEquals('Blog', $builder->menu[2]['text']);
        $this->assertEquals('TEST', $builder->menu[3]['header']);

        $builder = $this->makeMenuBuilder('http://example.com', null, 'de');
        $builder->add(['header' => 'Profile']);
        $builder->add(['text' => 'Profile', 'url' => '/profile', 'label' => 'Labels']);
        $this->menuTest($builder);
        $this->assertEquals('Labels', $builder->menu[1]['label']);
        $this->assertEquals('Blog', $builder->menu[2]['text']);
        $this->assertEquals('TEST', $builder->menu[3]['header']);
    }

    public function test_lang_translate_with_extra_params()
    {
        $builder = $this->makeMenuBuilder('http://example.com', null, 'es');

        $lines = [
            'menu.header_with_params' => 'MENU :cat / :subcat',
            'menu.profile_with_params' => 'Perfil de :name',
            'menu.label_with_params' => 'Etiqueta :type',
        ];

        $translator = $this->getTranslator();
        $translator->addLines($lines, 'es', 'tablar');

        $builder->add(
            [
                'header' => [
                    'header_with_params',
                    ['cat' => 'CAT', 'subcat' => 'SUBCAT'],
                ],
            ],
            [
                'text' => ['profile_with_params', ['name' => 'Diego']],
                'url' => '/profile',
                'label' => ['label_with_params', ['type' => 'Tipo']],
            ],
            [
                // Test case with partial parameters.
                'header' => ['header_with_params', ['subcat' => 'SUBCAT']],
            ],
            [
                // Test case with empty parameters.
                'header' => ['header_with_params'],
            ],
            [
                // Test case with non-array parameters.
                'header' => ['header_with_params', 'non-array-value'],
            ]
        );

        $this->assertCount(5, $builder->menu);
        $this->assertEquals('MENU CAT / SUBCAT', $builder->menu[0]['header']);
        $this->assertEquals('Perfil de Diego', $builder->menu[1]['text']);
        $this->assertEquals('Etiqueta Tipo', $builder->menu[1]['label']);
        $this->assertEquals('MENU :cat / SUBCAT', $builder->menu[2]['header']);
        $this->assertEquals('MENU :cat / :subcat', $builder->menu[3]['header']);
        $this->assertEquals('MENU :cat / :subcat', $builder->menu[4]['header']);
    }

    public function test_data_attributes()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'About', 'data' => [
            'test-one' => 'content-one',
            'test-two' => 'content-two',
        ]]);

        $this->assertEquals(
            'data-test-one="content-one" data-test-two="content-two"',
            $builder->menu[0]['data-compiled']
        );
    }

    public function test_search_bar_default_method()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'search', 'search' => true]);
        $builder->add(['text' => 'Search', 'search' => true, 'method' => 'foo']);
        $builder->add(['text' => 'Search', 'search' => true, 'method' => 'post']);

        $this->assertEquals('get', $builder->menu[0]['method']);
        $this->assertEquals('get', $builder->menu[1]['method']);
        $this->assertEquals('post', $builder->menu[2]['method']);
    }

    public function test_search_bar_default_name()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add(['text' => 'search', 'search' => true]);
        $builder->add(['text' => 'Search', 'search' => true, 'input_name' => 'foo']);

        $this->assertEquals('tablarSearch', $builder->menu[0]['input_name']);
        $this->assertEquals('foo', $builder->menu[1]['input_name']);
    }

    public function test_classes_attribute()
    {
        $builder = $this->makeMenuBuilder();

        $builder->add([
            'text' => 'About',
            'classes' => 'foo-class',
        ]);

        $this->assertStringContainsString('foo-class', $builder->menu[0]['class']);
    }

    public function test_classes_attribute_with_active_class()
    {
        $builder = $this->makeMenuBuilder('http://example.com/about');

        $builder->add([
            'text' => 'About',
            'url' => 'about',
            'classes' => 'foo-class bar-class',
        ]);

        $this->assertStringContainsString('active', $builder->menu[0]['class']);
        $this->assertStringContainsString('foo-class', $builder->menu[0]['class']);
        $this->assertStringContainsString('bar-class', $builder->menu[0]['class']);
    }

    public function multipleItemTests(Builder $builder): void
    {
        $this->assertCount(3, $builder->menu);
        $this->assertEquals('Home', $builder->menu[0]['text']);
        $this->assertEquals('/', $builder->menu[0]['url']);
        $this->assertEquals('Profile', $builder->menu[1]['text']);
        $this->assertEquals('/profile', $builder->menu[1]['url']);
        $this->assertEquals('About', $builder->menu[2]['text']);
        $this->assertEquals('/about', $builder->menu[2]['url']);
    }

    public function menuTest(Builder $builder): void
    {
        $builder->add(['text' => 'Blog', 'url' => '/blog']);
        $builder->add(['header' => 'TEST']);

        //        dd($builder->menu);

        $this->assertCount(4, $builder->menu);
        $this->assertEquals('Profile', $builder->menu[0]['header']);
        $this->assertEquals('Profile', $builder->menu[1]['text']);
    }
}
