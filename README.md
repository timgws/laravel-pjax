laravel-pjax
======
**laravel-pjax** is a pjax middleware for Laravel >5.1.

By adding [pjax](https://github.com/defunkt/jquery-pjax) & jQuery to your web application, ajax and pushState can be used to deliver a fast browsing experience with real permalinks, page titles, and a working back button.

This middleware adds some functionality that other middleware (at the time of writing) did not provide, such as container whitelists and faster page filtering.

I wanted to have some additional functionality for loading "pjax" AJAX requests, so I quickly threw together this package.

## Setting up the middleware

Install the package:
```bash
# composer require timgws/laravel-pjax
```

Add the middleware to your app's kernel configuration. The higher the better!

```php
// app/Http/Kernel.php

// ...
protected $middleware = [
    // ...
    \timgws\pjax\Middleware::class,
];
```

Add the service provider:

```php
// config/app.php

// ...
    'providers' => [
        // ... Laravel providers

        timgws\pjax\ServiceProvider::class,

        // ... app providers ...
    ],
```

Publish the config file:

```bash
php artisan vendor:publish --provider="timgws\pjax\ServiceProvider" --tag="config"
```

and test!

## Limiting containers that can be requested
One major difference between this module and other Laravel pjax middleware is that you
can limit which containers can be requested via HTTP headers.

You may wish to edit the `pjax.php` config file to limit `valid_containers`

```php
return [
    'valid_containers' => [
        '#pjax-container', '.content'
    ]
];
```

See https://github.com/defunkt/jquery-pjax#usage for further information.

## Force reload of page when version changes

Inside config.php, set the layout version to force a hard reload of the requested page
when a client is on an old version of the layout.

Bumping the version will force clients to do a full reload the next
request getting the new layout and assets

Set the version to something like 'v1'. Also, you will need to add
a meta tag like this to the base layout:

```html
      <meta http-equiv="x-pjax-version" content="v1">
```

```php
      return [
          'layout_version' => 'v1'
      ];
```

See https://github.com/defunkt/jquery-pjax#usage for further information.

## Contributors

### Contributors on GitHub
* [Tim Groeneveld](http://timg.ws)

## License 
* MIT License (see [LICENSE](https://github.com/timgws/laravel-pjax/blob/master/LICENSE.md) file)

## TODO
* Build test suite
