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

## Contributors

### Contributors on GitHub
* [Tim Groeneveld](http://timg.ws)

## License 
* MIT License (see [LICENSE](https://github.com/timgws/laravel-pjax/blob/master/LICENSE.md) file)

## TODO
* Support layout versions
* Build test suitelaravel-pjax
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

## Contributors

### Contributors on GitHub
* [Tim Groeneveld](http://timg.ws)

## License
* MIT License (see [LICENSE](https://github.com/timgws/laravel-pjax/blob/master/LICENSE.md) file)

## TODO
* Support layout versions
* Build test suite