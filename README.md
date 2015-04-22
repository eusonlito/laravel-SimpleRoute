# Laravel 5 SimpleRoute

Package to generate simple multilanguage (or not) routes to Laravel

## Installation

Begin by installing this package through Composer.

```js
{
    "require": {
        "laravel/simple-route": "1.*"
    }
}
```

### SimpleRoute installation

#### config/app.php

```php
'locale' => 'en',
'locales' => [
    'en' => 'en_US',
    'es' => 'es_ES',
    'fr' => 'fr_FR',
    'it' => 'it_IT'
],

'aliases' => [
    '...',
    'SimpleRoute' => 'Laravel\SimpleRoute',
];
```

#### app/Controllers/Http/routes.php

```php
SimpleRoute::get('web.index'); // Controller `Web` method `index`

SimpleRoute::get('articles.home'); // Controller `Articles` method `home`
SimpleRoute::get('articles.detail'); // Controller `Articles` method `detail`

SimpleRoute::get('users.logout'); // Controller `Users` method `logout`

SimpleRoute::group(['middleware' => 'guest'], function () {
    SimpleRoute::any('users.login'); // Controller `Users` method `login`
    SimpleRoute::any('users.register'); // Controller `Users` method `register`
});

SimpleRoute::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
    SimpleRoute::get('users.home'); // Controller `Users` method `home`
    SimpleRoute::any('users.profile'); // Controller `Users` method `profile`
    SimpleRoute::any('users.edit-profile'); // Controller `Users` method `editProfile`

    SimpleRoute::group(['middleware' => 'admin'], function () {
        SimpleRoute::any('users.articles.add'); // Controller `UsersArticles` method `add`
        SimpleRoute::any('users.articles.edit'); // Controller `UsersArticles` method `edit`
        SimpleRoute::delete('users.articles.delete'); // Controller `UsersArticles` method `delete`
    });
});
```

#### resources/lang/en/routes.php

```php
return [
    'web.index' => '/',

    'articles.home' => '/articles',
    'articles.detail' => '/article/{slug}',

    'users' => '/users',

    'users.register' => '/register',
    'users.login' => '/login',
    'users.logout' => '/logout',
    'users.home' => '/home',
    'users.profile' => '/profile',

    'users.articles.add' => '/article/new',
    'users.articles.edit' => '/article/edit/{slug}',
    'users.articles.delete' => '/article/delete/{slug}'
];
```

#### resources/lang/es/routes.php

```php
return [
    'web.index' => '/',

    'articles.home' => '/articulos',
    'articles.detail' => '/articulo/{slug}',

    'users' => '/usuarios',

    'users.register' => '/registro',
    'users.login' => '/acceso',
    'users.logout' => '/salir',
    'users.home' => '/inicio',
    'users.profile' => '/perfil',

    'users.articles.add' => '/articulo/nuevo',
    'users.articles.edit' => '/articulo/editar/{slug}',
    'users.articles.delete' => '/articulo/borrar/{slug}'
];
```
