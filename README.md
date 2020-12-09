# Laravel Auto Migrations For Laravel 8
[![Latest Stable Version](https://poser.pugx.org/mwakalingajohn/laravel-auto-migrations/v)](//packagist.org/packages/mwakalingajohn/laravel-auto-migrations) 
[![Total Downloads](https://poser.pugx.org/mwakalingajohn/laravel-auto-migrations/downloads)](//packagist.org/packages/mwakalingajohn/laravel-auto-migrations) 
[![Latest Unstable Version](https://poser.pugx.org/mwakalingajohn/laravel-auto-migrations/v/unstable)](//packagist.org/packages/mwakalingajohn/laravel-auto-migrations) 
[![License](https://poser.pugx.org/mwakalingajohn/laravel-auto-migrations/license)](//packagist.org/packages/mwakalingajohn/laravel-auto-migrations)


This package is created in attempt to emulate the django migration system

## Quick installation
````bash
$ composer require mwakalingajohn/laravel-auto-migrations
````

#### Service Provider & Facade (Optional)
Register provider and facade on your `config/app.php` file.
````php
'providers' => [
    ...,
    Mwakalingajohn\LaravelAutoMigrations\LaravelAutoMigrationsServiceProvider::class,
]
````

## Credits
[Mwakalinga John](https://github.com/**mwakalingajohn**)

## License

The MIT License (MIT). Please see [License File](https://github.com/yajra/laravel-datatables/blob/master/LICENSE.md) for more information.
