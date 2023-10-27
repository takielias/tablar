<?php

namespace TakiElias\Tablar\Helpers;

use Illuminate\Support\Facades\View;


class LayoutHelper
{

    /**
     * Get Layout Class.
     *
     * @return bool
     */
    public static function getLayoutClass()
    {
        return config('tablar.layout') || View::getSection('layout');
    }

    /**
     * Make and return the set of classes related to the body tag.
     *
     * @return string
     */
    public static function makeBodyClasses()
    {
        $classes = [];

        $classes = array_merge($classes, self::makeLayoutClasses());
        $classes = array_merge($classes, self::makeCustomBodyClasses());

        return trim(implode(' ', $classes));
    }

    /**
     * Make and return the set of classes related to the layout configuration.
     *
     * @return array
     */
    private static function makeLayoutClasses()
    {
        $classes = [];

        // Get default Layout Class

        if (self::getLayoutClass()) {
            $classes[] = config('tablar.layout');
        }

        return $classes;
    }

    /**
     * Make the set of classes related to custom body classes configuration.
     *
     * @return array
     */
    private static function makeCustomBodyClasses()
    {
        $classes = [];
        $cfg = config('tablar.classes_body', '');

        if (is_string($cfg) && $cfg) {
            $classes[] = $cfg;
        }

        return $classes;
    }
}
