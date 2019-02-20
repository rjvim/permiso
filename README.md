## Installation

You can install the package via composer:

``` bash
composer require rjvim/permiso
```

The package will automatically register itself.

You can publish the migration with:
```bash
php artisan vendor:publish --provider="Betalectic\Permiso\PermisoServiceProvider" --tag="migrations"
```

After the migration has been published you can create the `tags` and `taggables` tables by running the migrations:

```bash
php artisan migrate
```

You can optionally publish the config file with:
```bash
php artisan vendor:publish --provider="Betalectic\Permiso\PermisoServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [

    /*
     * The given function generates a URL friendly "slug" from the tag name property before saving it.
     */
    'slugger' => 'str_slug',
];
```


## Documentation
You'll find the documentation on [https://docs.spatie.be/laravel-tags/v2](https://docs.spatie.be/laravel-tags/v2).

Find yourself stuck using the package? Found a bug? Do you have general questions or suggestions for improving the `laravel-tags` package? Feel free to [create an issue on GitHub](https://github.com/spatie/laravel-tags/issues), we'll try to address it as soon as possible.

If you've found a bug regarding security please mail [freek@spatie.be](mailto:freek@spatie.be) instead of using the issue tracker.

## Testing

1. Copy `.env.example` to `.env` and fill in your database credentials.
2. Run `composer test`.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/open-source).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
