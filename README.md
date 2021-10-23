# Laravel Model Related Fields

Provide an ability to add related fields/columns to all model Eloquent queries without additional database queries.
It uses sub SELECT and joins so take into account extra database load when database has a lot of records.

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

## Table of contents

* [Installation](#installation)
* [Using](#using)

## Installation

To get the latest version of `Laravel Model Related Fields`, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require dedmytro/laravel-model-related-fields
```

Or manually update `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "dedmytro/laravel-model-related-fields": "^0.1"
    }
}
```

## Using

Add `HasRelatedFields` trait to your model and define method `addRelatedFields()` 
which returns array related fields you want to load on each Model query.
On example below you can see how to load related field through many relations and load SUM() of HasMany relation.

```php
use Illuminate\Database\Eloquent\Model;
use DeDmytro\LaravelModelRelatedFields\RelatedField;
use DeDmytro\LaravelModelRelatedFields\Traits\HasRelatedFields;
// ...

class Order extends Model
{
    use HasRelatedFields;
    
    // ...
    
    protected function addRelatedFields(): array
    {
        return [
            'country_currency' => 'event.company.country.currency',
            'total' => new RelatedField('items.total', function ($query) {
                $query->selectRaw('SUM(price)');
            }),
        ];
    }
```

where

`'event.company.country.currency'` is the same as `$order->event->company->country->currency`

`'items.total'` is the same as `$order->items()->sum('price')`

As an alternative you can use `$relatedFields` property for simple fields, without `RelatedField` class usage. Property
and method result array will be be merged.

```php
use Illuminate\Database\Eloquent\Model;
use DeDmytro\LaravelModelRelatedFields\RelatedField;
use DeDmytro\LaravelModelRelatedFields\Traits\HasRelatedFields;
// ...

class Order extends Model
{
    use HasRelatedFields;
    
    // ...
    
    protected $relatedFields = [
      'country_currency' => 'event.company.country.currency',
   ];
    
    protected function addRelatedFields(): array
    {
        return [
            'total' => new RelatedField('items.total', function ($query) {
                $query->selectRaw('SUM(price)');
            }),
        ];
    }
```

The result of query:

```php
$order = Order::first();
```

you will get Order model with all fields plus additional related fields:

```php
[
    "id" => 1,
    // ...
    "country_currency" => "USD",
    "total" => 25.00,
]
```

### How to disable/enable

#### Disable globally

To disable related fields for all models by default,

* publish config and change default value

```php
'enabled' => false
```

* add env variable

```env
ENABLE_MODEL_RELATED_FIELDS=false
```

#### Disable for particular model

Add `protected $enableRelatedFields = false`

```php
protected $enableRelatedFields = false;
```

#### Disable for current query

```php
$order = Order::withoutRelatedFields()->first();
```

#### Enable for current query if disabled globally

```php
$order = Order::withRelatedFields()->first();
```

[badge_downloads]:      https://img.shields.io/packagist/dt/dedmytro/laravel-model-related-fields.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dedmytro/laravel-model-related-fields.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/dedmytro/laravel-model-related-fields?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dedmytro/laravel-model-related-fields
