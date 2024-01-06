<?php

namespace TakiElias\Tablar\Tests;

use Illuminate\Auth\Access\Gate;
use Illuminate\Auth\GenericUser;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use TakiElias\Tablar\Tablar;
use TakiElias\Tablar\Menu\ActiveChecker;
use TakiElias\Tablar\Menu\Builder;
use TakiElias\Tablar\Menu\Filters\ActiveFilter;
use TakiElias\Tablar\Menu\Filters\ClassesFilter;
use TakiElias\Tablar\Menu\Filters\DataFilter;
use TakiElias\Tablar\Menu\Filters\GateFilter;
use TakiElias\Tablar\Menu\Filters\HrefFilter;
use TakiElias\Tablar\Menu\Filters\LangFilter;
use TakiElias\Tablar\Menu\Filters\SearchFilter;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TestCase extends BaseTestCase
{
    private $dispatcher;

    private $routeCollection;

    private $translator;

    /**
     * Load the packages services providers.
     */
    protected function getPackageProviders($app)
    {
        // Register our package service provider into the Laravel application.

        return ['TakiElias\Tablar\TablarServiceProvider'];
    }

    protected function makeMenuBuilder($uri = 'http://example.com', GateContract $gate = null, $locale = 'en')
    {
        return new Builder([
            new GateFilter($gate ?: $this->makeGate()),
            new HrefFilter($this->makeUrlGenerator($uri)),
            new ActiveFilter($this->makeActiveChecker($uri)),
            new ClassesFilter(),
            new DataFilter(),
            new LangFilter($this->makeTranslator($locale)),
            new SearchFilter(),
        ]);
    }

    protected function makeTranslator($locale = 'en')
    {
        $translationLoader = new FileLoader(new  Filesystem, 'resources/lang/');

        $this->translator = new  Translator($translationLoader, $locale);
        $this->translator->addNamespace('tablar', 'resources/lang/');

        return $this->translator;
    }

    protected function makeActiveChecker($uri = 'http://example.com', $scheme = null)
    {
        return new ActiveChecker($this->makeUrlGenerator($uri, $scheme));
    }

    private function makeRequest($uri)
    {
        return Request::createFromBase(SymfonyRequest::create($uri));
    }

    protected function makeTablar()
    {
        return new Tablar($this->getFilters(), $this->getDispatcher(), $this->makeContainer());
    }

    protected function makeUrlGenerator($uri = 'http://example.com', $scheme = null)
    {
        $UrlGenerator = new UrlGenerator(
            $this->getRouteCollection(),
            $this->makeRequest($uri)
        );

        if ($scheme) {
            $UrlGenerator->forceScheme($scheme);
        }

        return $UrlGenerator;
    }

    protected function makeGate()
    {
        $userResolver = function () {
            return new GenericUser([]);
        };

        return new Gate($this->makeContainer(), $userResolver);
    }

    protected function makeContainer()
    {
        return new  Container();
    }

    protected function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->dispatcher = new Dispatcher;
        }

        return $this->dispatcher;
    }

    private function getFilters()
    {
        return [];
    }

    protected function getRouteCollection()
    {
        if (!$this->routeCollection) {
            $this->routeCollection = new RouteCollection();
        }

        return $this->routeCollection;
    }

    protected function getTranslator()
    {
        return $this->translator;
    }
}
