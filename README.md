<p align="center">
  <img src="assets/logo.png" alt="HookPress logo" width="500" />
</p>

# HookPress is a Laravel package that uses Composer hooks to automatically discover, filter, and cache classes, traits, and interfaces in your application

<p align="center">
  <a href="https://packagist.org/packages/teofanis/hook-press">
    <img alt="Latest Version on Packagist" src="https://img.shields.io/packagist/v/teofanis/hook-press.svg?style=flat-square">
  </a>
  <a href="https://github.com/teofanis/hook-press/actions/workflows/run-tests.yml">
    <img alt="GitHub Tests Action Status" src="https://img.shields.io/github/actions/workflow/status/teofanis/hook-press/run-tests.yml?branch=main&amp;label=tests&amp;style=flat-square">
  </a>
  <a href="https://github.com/teofanis/hook-press/actions/workflows/fix-php-code-style-issues.yml">
    <img alt="GitHub Code Style Action Status" src="https://img.shields.io/github/actions/workflow/status/teofanis/hook-press/fix-php-code-style-issues.yml?branch=main&amp;label=code%20style&amp;style=flat-square">
  </a>
  <a href="https://packagist.org/packages/teofanis/hook-press">
    <img alt="Total Downloads" src="https://img.shields.io/packagist/dt/teofanis/hook-press.svg?style=flat-square">
  </a>
</p>

HookPress builds a static class map during Composer install/update so your app can look up “discoverable” classes in O(1) time at runtime. No boot-time reflection, no recursive directory scans, no config arrays to maintain.

## Why?
I’ve used this in production for a while to auto-discover things like payment methods and populate lists/registries without hand-wiring every class. It’s been handy for pluggable components (drivers, actions, jobs) where “put the class in the right namespace” should be enough.

## Typical uses

- Collect all implementations of an interface (e.g. PayoutMethod) to build a registry or a UI dropdown.

- Group classes by trait (e.g. everything using Searchable) for indexing or batch operations.

- Discover classes marked by attributes (e.g. #[Discoverable]).

- Find invokables or classes with particular methods/properties for handler pipelines.


## Requirements

- PHP 8.0+

- Laravel 9+

## Installation

```bash
composer require teofanis/hook-press
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="hook-press-config"
```

Wire HookPress to run when composer installs/updates
```json
{
  "scripts": {
    "post-install-cmd": [
      "HookPress\\Hooks\\HookRunner::postInstall"
    ],
    "post-update-cmd": [
      "HookPress\\Hooks\\HookRunner::postUpdate"
    ]
  }
}
```
> **_NOTE:_** You can use artisan directly if you prefer
```json
"post-install-cmd": ["@php artisan hookpress:build --no-ansi --no-interaction"],
"post-update-cmd":  ["@php artisan hookpress:build --no-ansi --no-interaction"]

```
 
This is the contents of the published config file:

```php
return [
     // Namespaces to consider as your application roots
    'roots' => [
        'App\\',
    ],

    // Optional: trait grouping
    'traits' => [
        'enabled'    => true,
        'namespaces' => ['App\\Traits\\'],
        'group_key'  => 'traits', // where the trait map will be stored
    ],

    // Define your discovery “maps”
    'maps' => [
        'payout_methods' => [
            'namespaces' => ['App\\Classes\\PayoutMethods\\'],
            'conditions' => [
                'isInstantiable',
                'implementsInterface' => 'App\\Interfaces\\PayoutMethod',
            ],
        ],
    ],

    // Exclusions (exact class, namespace prefix, or regex)
    'exclusions' => [
        'classes'    => [],
        'namespaces' => [],
        'regex'      => [],
    ],

    // Where the computed map is stored
    'store' => [
        'driver' => 'file', // 'file' or 'cache'
        'file'   => ['path'  => 'bootstrap/cache/hookpress.php'],
        'cache'  => ['store' => null, 'key' => 'hookpress:map', 'ttl' => null],
    ],

    // Where HookPress reads the Composer classmap from (leave as default in apps)
    'composer' => [
        'classmap_path'   => 'vendor/composer/autoload_classmap.php',
        'artisan_command' => 'hookpress:build',
    ],
];
```
### How it works 
On composer install/update (or php artisan hookpress:build), HookPress scans your Composer classmap, applies your conditions, and writes a single PHP file (or cache entry) with the results.

At runtime you read from that file/cache—no reflection or directory walking.

## Usage

```php
use HookPress\Facades\HookPress;
// Entire map (all keys)
$all = HookPress::map();
// Specific map
$methods = HookPress::map('payout_methods');

// Classes that use a trait
$searchables = HookPress::classesUsing(\App\Traits\Searchable::class);

// Rebuild on demand (usually done via Composer hook)
HookPress::refresh();

// Clear the stored map
HookPress::clear();
```

## Artisan Commands 
```bash
php artisan hookpress:build        # compute and store the map
php artisan hookpress:show         # print the map
php artisan hookpress:show payout_methods
php artisan hookpress:clear
php artisan hookpress:build --no-traits  # skip trait grouping

```

## Conditions & Examples

### Available built-in conditions

| Condition            | Purpose                                                                 | Arg Type                                      | Example Arg(s)                                                                                   | Notes / Behavior                                                                                                                                                 |
|----------------------|-------------------------------------------------------------------------|-----------------------------------------------|---------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `isInstantiable`     | Include only classes that can be instantiated (not abstract/interfaces). | _none_                                         | _n/a_                                                                                            | Default when `conditions` is omitted. Effectively filters out abstract classes and interfaces.                                                                   |
| `implementsInterface`| Require the class to implement a specific interface.                    | `string` (FQCN)                                | `App\\Interfaces\\PayoutMethod::class`                                                           | Uses `ReflectionClass::implementsInterface()`. Pass a fully-qualified interface name.                                                                            |
| `usesTrait`          | Require the class to use a given trait.                                 | `string` (FQCN)                                | `App\\Traits\\Searchable::class`                                                                  | Checks `ReflectionClass::getTraitNames()`. Matches exact trait FQCN.                                                                                             |
| `hasAttribute`       | Require the class to be annotated with a PHP 8 attribute.               | `string` (FQCN)                                | `App\\Attributes\\Discoverable::class`                                                            | Uses `ReflectionClass::getAttributes()`. Attribute FQCN must match.                                                                                              |
| `extends`            | Require the class to extend a given parent/base class.                   | `string` (FQCN)                                | `Illuminate\\Database\\Eloquent\\Model::class`                                                    | Uses `ReflectionClass::isSubclassOf()`.                                                                                                                           |
| `isAbstract`         | Include only abstract classes.                                          | _none_                                         | _n/a_                                                                                            | Opposite of `isInstantiable` for abstracts. Useful if you deliberately want base classes.                                                                         |
| `isFinal`            | Include only classes declared `final`.                                   | _none_                                         | _n/a_                                                                                            | Uses `ReflectionClass::isFinal()`.                                                                                                                                |
| `hasMethod`          | Require a method to exist (optionally with constraints).                 | `string` **or** `array`                        | `'process'` **or** `['name' => 'process', 'public' => true, 'static' => false, 'returns' => 'bool']` | Array keys supported: `name` (required), `public`/`protected`/`private` (bool), `static` (bool), `returns` (`'void'` or FQCN/string of return type).             |
| `hasProperty`        | Require a property to exist (optionally with constraints).               | `string` **or** `array`                        | `'driver'` **or** `['name' => 'driver', 'public' => true, 'static' => false, 'type' => 'string']`   | Array keys supported: `name` (required), `public`/`protected`/`private` (bool), `static` (bool), `type` (`'string'`, `'int'`, FQCN, etc.).                        |
| `nameMatches`        | Match FQCN or short class name using a regex.                           | `string` **or** `array`                        | `'/Controller$/'` **or** `['pattern' => '/^Bank/', 'short' => true]`                               | If you pass an array: `pattern` is the regex; `short: true` applies it to the short class name; otherwise it applies to the full-qualified class name (FQCN).    |

**Notes**
- If `conditions` is omitted for a map, HookPress defaults to `['isInstantiable']`.
- Combine multiple conditions in the array to apply **AND** logic.
- All FQCNs must be fully-qualified (escape backslashes in JSON/Markdown as needed).


### Examples

#### Models (non-abstract Eloquent):
```php

'models' => [
    'namespaces' => ['App\\Models\\'],
    'conditions' => [
        'extends' => \Illuminate\Database\Eloquent\Model::class,
        'isInstantiable'// removes abstracts if-any
    ],
],
```
#### Invokables (public `__invoke`):
```php
'invokables' => [
    'namespaces' => ['App\\Actions\\', 'App\\Jobs\\'],
    'conditions' => [
        'isInstantiable',
        'hasMethod' => ['name' => '__invoke', 'public' => true],
    ],
],

```
#### Controllers by name:
```php
'controllers' => [
    'namespaces' => ['App\\Http\\Controllers\\'],
    'conditions' => [
        'isInstantiable',
        'nameMatches' => '/Controller$/',
    ],
],
```

### Extending with your own custom checks

1. Implement the contract 

```php
namespace App\HookPress;

use HookPress\Contracts\Condition;
use ReflectionClass;

class ConstructorTakesLogger implements Condition
{
    public function passes(ReflectionClass $ref, mixed $arg = null): bool
    {
        $ctor = $ref->getConstructor();
        if (! $ctor) return false;

        foreach ($ctor->getParameters() as $p) {
            $t = $p->getType();
            if ($t && ltrim((string) $t, '\\') === 'Psr\\Log\\LoggerInterface') {
                return true;
            }
        }
        return false;
    }
}

```

2. Reference it by the FQCN (Fully Qualified Class Name) in your config

```php
'services_requiring_logger' => [
    'namespaces' => ['App\\Services\\'],
    'conditions' => [
        \App\HookPress\ConstructorTakesLogger::class => null,
    ],
],
```
HookPress resolves unknown condition keys as classes via the container, so no extra registration is needed.


### Notes
Build time scales with your classmap size, but it’s a one-off step during Composer or CI.

Runtime lookups are reading from a single array in a PHP file or a cache item.

You can switch to the cache driver if you prefer not to ship a file under bootstrap/cache.
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Teofanis Papadopulos](https://github.com/teofanis)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
