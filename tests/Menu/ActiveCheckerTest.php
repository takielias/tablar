<?php

namespace TakiElias\Tablar\Tests\Menu;

use TakiElias\Tablar\Tests\TestCase;

class ActiveCheckerTest extends TestCase
{
    public function test_exact()
    {
        $checker = $this->makeActiveChecker('http://example.com/about');

        $this->assertTrue($checker->isActive(['url' => 'about']));
    }

    public function test_root()
    {
        $checker = $this->makeActiveChecker('http://example.com');

        $this->assertTrue($checker->isActive(['url' => '/']));
    }

    public function test_not_active()
    {
        $checker = $this->makeActiveChecker('http://example.com/about');

        $this->assertFalse($checker->isActive(['url' => 'home']));
    }

    public function test_string_not_active()
    {
        $checker = $this->makeActiveChecker();

        $this->assertFalse($checker->isActive('HEADER'));
    }

    public function test_sub()
    {
        $checker = $this->makeActiveChecker('http://example.com/about/sub');

        $this->assertTrue($checker->isActive(['url' => 'about/sub']));
    }

    public function test_submenu()
    {
        $checker = $this->makeActiveChecker('http://example.com/home');

        $isActive = $checker->isActive(
            [
                'submenu' => [
                    ['url' => 'foo'],
                    ['url' => 'home'],
                ],
            ]
        );

        $this->assertTrue($isActive);
    }

    public function test_multi_level_submenu()
    {
        $checker = $this->makeActiveChecker('http://example.com/home');

        $isActive = $checker->isActive(
            [
                'text' => 'Level 0',
                'submenu' => [
                    [
                        'text' => 'Level 1',
                        'submenu' => [
                            ['url' => 'foo'],
                            ['url' => 'home'],
                        ],
                    ],
                ],
            ]
        );

        $this->assertTrue($isActive);
    }

    public function test_explicit_active()
    {
        $checker = $this->makeActiveChecker('http://example.com/home');

        $isActive = $checker->isActive(['active' => ['home']]);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['active' => ['about']]);
        $this->assertFalse($isActive);
    }

    public function test_explicit_active_regex()
    {
        $checker = $this->makeActiveChecker('http://example.com/home/sub');

        $isActive = $checker->isActive(['active' => ['home/*']]);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['active' => ['home/su*']]);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['active' => ['hom*']]);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['active' => ['home/t*']]);
        $this->assertFalse($isActive);
    }

    public function test_explicit_overrides_default()
    {
        $checker = $this->makeActiveChecker('http://example.com/admin/users');

        $isActive = $checker->isActive(['active' => ['admin']]);
        $this->assertFalse($isActive);
    }

    public function test_full_url()
    {
        $checker = $this->makeActiveChecker('http://example.com/about');

        $isActive = $checker->isActive(['url' => 'http://example.com/about']);
        $this->assertTrue($isActive);
    }

    public function test_full_url_sub()
    {
        $checker = $this->makeActiveChecker('http://example.com/about/sub');

        $isActive = $checker->isActive(['url' => 'http://example.com/about/sub']);
        $this->assertTrue($isActive);
    }

    public function test_https()
    {
        $checker = $this->makeActiveChecker('https://example.com/about');

        $isActive = $checker->isActive(['url' => 'https://example.com/about']);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['url' => 'about']);
        $this->assertTrue($isActive);
    }

    public function test_params()
    {
        $checker = $this->makeActiveChecker('http://example.com/menu?param=option');

        $this->assertTrue($checker->isActive(['url' => 'menu']));
        $this->assertTrue($checker->isActive(['active' => ['menu']]));
        $this->assertTrue($checker->isActive(['active' => ['menu?param=option']]));
        $this->assertFalse($checker->isActive(['active' => ['menu?param=foo']]));
    }

    public function test_sub_params()
    {
        $checker = $this->makeActiveChecker('http://example.com/menu/item1?param=option');

        $this->assertTrue($checker->isActive(['url' => 'menu/item1']));
        $this->assertTrue($checker->isActive(['active' => ['menu/*']]));
    }

    public function test_explicit_active_regex_evaluation()
    {
        $checker = $this->makeActiveChecker('http://example.com/posts/1');

        $this->assertTrue($checker->isActive(['active' => ['regex:@^posts/[0-9]+$@']]));
        $this->assertFalse($checker->isActive(['active' => ['regex:@^post/[0-9]+$@']]));
    }

    public function test_activefallback_to_url()
    {
        $checker = $this->makeActiveChecker('http://example.com/home');

        $isActive = $checker->isActive(
            [
                'url' => 'home',
                'active' => ['about', 'no-home'],
                'submenu' => [],
            ]
        );

        $this->assertTrue($isActive);
    }

    public function test_with_forced_scheme()
    {
        $checker = $this->makeActiveChecker('http://example.com/about', 'https');

        $isActive = $checker->isActive(['url' => 'about']);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['url' => 'http://example.com/about']);
        $this->assertTrue($isActive);

        $isActive = $checker->isActive(['url' => 'https://example.com/about']);
        $this->assertTrue($isActive);
    }
}
