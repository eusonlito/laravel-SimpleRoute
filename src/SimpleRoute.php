<?php

namespace Laravel\SimpleRoute;

use Exception;
use Lang;
use Route;

class SimpleRoute
{
    private static $locale;
    private static $routes = [];
    private static $path = 'resources/lang';
    private static $file = 'routes.php';

    public static function setLangPath($path)
    {
        $full = base_path($path);

        if (!is_dir($full)) {
            throw new Exception(sprintf('Path %s not exists', $path));
        }

        self::$path = $path;
    }

    private static function getLangPath()
    {
        return base_path(self::$path);
    }

    public static function setRoutesFile($file)
    {
        if (!preg_match('/\.php$/', $file)) {
            $file .= '.php';
        }

        self::$file = $file;
    }

    private static function getRoutes()
    {
        if (self::$routes) {
            return self::$routes;
        }

        return self::loadRoutes();
    }

    private static function getLocales()
    {
        if (!$config = config('gettext')) {
            $config = config('app');
        }

        if (array_key_exists('locales', $config)) {
            $locales = array_unique(array_map(function($locale) {
                return strtolower(preg_replace('/_.*$/', '', $locale));
            }, $config['locales']));
        } else {
            $locales = [$config['locale']];
        }

        return $locales;
    }

    private static function loadRoutes()
    {
        $path = self::getLangPath();

        self::$routes = [];

        foreach (self::getLocales() as $locale) {
            $file = $path.'/'.$locale.'/'.self::$file;

            if (is_file($file)) {
                self::$routes[$locale] = require $file;
            }
        }

        return self::$routes;
    }

    private static function getCurrentLocale()
    {
        if (self::$locale) {
            return self::$locale;
        }

        return self::$locale = Lang::getLocale();
    }

    public static function get($name, array $settings = [])
    {
        self::setRoutes(__FUNCTION__, $name, $settings);
    }

    public static function post($name, array $settings = [])
    {
        self::setRoutes(__FUNCTION__, $name, $settings);
    }

    public static function put($name, array $settings = [])
    {
        self::setRoutes(__FUNCTION__, $name, $settings);
    }

    public static function delete($name, array $settings = [])
    {
        self::setRoutes(__FUNCTION__, $name, $settings);
    }

    public static function any($name, array $settings = [])
    {
        self::setRoutes(__FUNCTION__, $name, $settings);
    }

    public static function match(array $methods, $name, array $settings = [])
    {
        foreach ($methods as $method) {
            self::setRoutes(strtolower($method), $name, $settings);
        }
    }

    private static function getRoute($locale, $name)
    {
        return self::$routes[$locale][$name];
    }

    private static function setRoutes($method, $name, array $settings = [])
    {
        foreach (array_keys(self::getRoutes()) as $locale) {
            self::setRoute($method, $name, $settings, $locale);
        }

        self::setRoute($method, $name, $settings);
    }

    private static function setRoute($method, $name, array $settings = [], $locale = '')
    {
        $current = self::getCurrentLocale();

        $controller = '';
        $function = $name;

        while (strstr($function, '.')) {
            list($first, $function) = explode('.', $function, 2);
            $controller .= ucfirst($first);
        }

        $function = preg_replace_callback('/\-([a-z])/', function ($matches) {
            return ucfirst($matches[1]);
        }, $function);

        $route = self::getRoute($locale ?: $current, $name);

        return Route::$method($route, array_merge([
            'as' => $name.($locale ? ('-'.$locale) : ''),
            'uses' => $controller.'@'.$function,
        ], $settings));
    }

    public static function group($settings, $callback)
    {
        $prefix = isset($settings['prefix']) ? $settings['prefix'] : null;

        foreach (self::getRoutes() as $locale => $routes) {
            if ($prefix) {
                $settings['prefix'] = $routes[$prefix];
            }

            Route::group($settings, $callback);
        }

        if ($prefix) {
            $settings['prefix'] = self::getRoute(self::getCurrentLocale(), $prefix);
        }

        Route::group($settings, $callback);
    }
}
