# Germania KG · Pagination




[![Packagist](https://img.shields.io/packagist/v/germania-kg/pagination.svg?style=flat)](https://packagist.org/packages/germania-kg/pagination)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/pagination.svg)](https://packagist.org/packages/germania-kg/pagination)
[![Build Status](https://img.shields.io/travis/GermaniaKG/Pagination.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/Pagination)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/Pagination/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Pagination/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/Pagination/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Pagination/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/Pagination/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Pagination/build-status/master)



## Installation with Composer

```bash
$ composer require germania-kg/pagination
```



## Overview

**Pagination:**
Page numbering made easy! Supports *current, next, previous, first* and *last* page numbers as well as *number of pages* and customizable *page sizes*.

**PaginationFactory:**
Callable class for creating a *Pagination* instance. Works great with `$_GET['page']`

**PaginationIterator:**
Limits your *Traversables* according to the *Pagination* status. Useful for paginated **JSON API** resources. 

**JsonApiPaginationDecorator:** 
Create useful `links` and `meta` information for your **JSON API** resource collection.



## Usage

### The Pagination class

Just pass the number of items to paginate. The *Pagination* class will calculate the page numbers, based on a default page size of 25. See below for customization examples.

```php
<?hpp
use Germania\Pagination\Pagination;

$items_count = 100; // count( $things ) or integer;
$pagination = new Pagination( $items_count );

$pagination->getPagesCount(); // 4, with 25 items each
$pagination->getFirst(); // 0
$pagination->getLast(); // 3
```



#### Pagination status

Right after instantiation, no page was picked by the user. Hence, the *Pagination* is then considered *inactive*. 

Please note that the current page also may be `int(0)` – the first page. This is why we have to check against `null`. The *isActive* method is a convenient alias for `$p->getCurrent() === null` . 

```php
$pagination->isActive();    // FALSE

$pagination->getCurrent();  // null
$pagination->getPrevious(); // null
$pagination->getNext();     // null
```

It first needs a *setCurrent* call to become *active*:

#### Setting the current page

```php
use Germania\Pagination\PaginationRangeException;

try {
  $pagination->setCurrent( 1 );
  
  $pagination->isActive();    // TRUE
  $pagination->getCurrent();  // 1  
  $pagination->getPrevious(); // null
  $pagination->getNext();     // 2  
}
catch ( PaginationRangeException $e )
{
  echo $e->getMessage(); // "Invalid Page number"
  echo $e->getCode();    // 400
}
```



#### Setting the page size

Whilst the default number of items on a page is 25, you may set another size—up to 100 per default:

```php
use Germania\Pagination\PaginationRangeException;

try {
	$pagination = new Pagination( $items_count );  
	$pagination->getPageSize(); // 25
	$pagination->setPageSize( 999 );  
}
catch ( PaginationRangeException $e ) 
{
  echo $e->getMessage(); // "Invalid Page size (max. 100)"
  echo $e->getCode();    // 400
}
```

**Tweak the page sizes** with constructor parameters:

```php
$custom_page_size =  50; // default: 25
$pagination = new Pagination( $items_count, $custom_page_size );

$max_page_size    = 200; // default: 100
$pagination = new Pagination( $items_count, $custom_page_size, $max_page_size );
```



### The PaginationFactory

The **PaginationFactory** constructor also accepts instances of `Countable`, `Traversable` or `arrays`, so you won't have to count the items yourself. The second parameter may be a page number *integer* or an *array* with `number` and/or `size` values. 

```php
<?php
use Germania\Pagination\PaginationFactory;

// Optinally set default page size
$factory = new PaginationFactory;
$factory = new PaginationFactory( 25 );

// Most simple: just integers
$items_count = 65;
$choose_page = 2;

// Create Pagination instance:
$pagination = $factory( $items_count, $choose_page );
```

**Creation from array** is useful when working with query parameters such as `$_GET['page']`

```php
// Both elements are optional.
$pagination = $factory( $items_count, [
	'number' => 2, // default: 0
  'size'   => 20
]);

// User Input
$pagination = $factory( $items_count, $_GET['page'] ?? [] );
```




### The PaginationIterator

Limits any  `\Traversable` iterator to the current page size, depending on the *pagination* status. The **PaginationIterator** constructor accepts your *iterator* and your *pagination* instance. It is also `\Countable` to count the numbers of items shown on the current page.

```php
<?php
use Germania\Pagination\PaginationIterator;

// Have your pagination at hand...
$pagination = ...
$pagination->setCurrent(2);

// Setup something really big
$collection = new MyHugeIterator( $thousand_items );

$paginated_collection = new PaginationIterator( $collection, $pagination );
echo count( $paginated_collection ); // 25

foreach( $paginated_collection as $item):
  // loop: 25 items on page 2
endforeach;
```

#### Which Iterator is used?

Depending on the *pagination* status, *PaginationIterator's* inner iterator used in the *foreach* loop is either `\LimitIterator` or the `MyHugeIterator` instance itself, when pagination is not active:

```php
// this time, we do not pick a page number!
$thousand_items = ...
$pagination = new Pagination( count($thousand_items) );
$pagination->isActive(); // null

$collection = new MyHugeIterator( $thousand_items );

$paginated_collection = new PaginationIterator( $collection, $pagination );
$iterator = $paginated_collection->getIterator();

get_class( $iterator ); // MyHugeIterator instance
```




### The JsonApiPaginationDecorator

This library provides a handy **JsonApiPaginationDecorator** which will generate useful information for your JSON API resource collection responses with the help of a given **\Psr\Http\Message\UriInterface** instance.

#### [Meta information](https://jsonapi.org/format/#document-meta) support

The `meta` member can be used to include non-standard meta-information, such as our pagination:

```php
$pagination->setCurrent( 16 );
$pagination->setPageSize( 10 );

$links = $ja_decorator->getMeta();
// array(
//     numberOfPages => 23
//     currentPage   => 16
//     pageSize      => 10
// )

```

**The result array will be empty,** when the Pagination is inactive.



#### [Links object](https://jsonapi.org/format/#document-links) support

The **JSON API specs** on [fetching pagination](https://jsonapi.org/format/#fetching-pagination) proposes to use `page[number]` and `page[size]` for customizing the paged output. And it states the `links` object in a collection must use these key names, when working with pagination links:

- `first`: the first page of data
- `last`: the last page of data
- `prev`: the previous page of data
- `next`: the next page of data

The **JsonApiPaginationDecorator** generates this elements for you. The class is `\JsonSerializable` as well.

```php
<?php
use Germania\Pagination\JsonApiPaginationDecorator;

// Prepare dependencies
$uri = \GuzzleHttp\Psr7\uri_for('http://example.com');
$pagination = ...
$pagination->setCurrent( 2 );

new $ja_decorator = new JsonApiPaginationDecorator( $pagination, $uri);

// These are equivalent:
$links = $ja_decorator->getLinks();
$links = $ja_decorator->jsonSerialize();

// array(
//     first    => http://example.com/?page[number]=0
//     last     => http://example.com/?page[number]=42
//     previous => http://example.com/?page[number]=1
//     next     => http://example.com/?page[number]=3
// )
```



##### Default Query Parameters

The *JsonApiPaginationDecorator* internally calls the *withQuery* method on the PSR-7 *$uri* instance. Unfortunately, this will replace any query parameters the URI contained. Just pass any needed query parameters as 3rd constructor parameter:

```php
$params = array(
	'foo'  => 'bar',
  'json' => 'cool'
);
new $ja_decorator = new JsonApiPaginationDecorator( $pagination, $uri, $params);

$links = $ja_decorator->getLinks();
// array(
//     first    => http://example.com/?page[number]=0&foo=bar&json=cool
//     last     => http://example.com/?page[number]=42&foo=bar&json=cool
//     previous => http://example.com/?page[number]=1&foo=bar&json=cool
//     next     => http://example.com/?page[number]=3&foo=bar&json=cool
// )
```



##### Custom page sizes 

In case you set a custom page size (which differs from the default size), the links will get an additional `size` field:

```php
$pagination->setPageSize( 10 );

$links = $ja_decorator->getLinks();
// array(
//     first    => http://example.com/?page[number]=0&page[size]=10
//     last     => http://example.com/?page[number]=42&page[size]=10
//     previous => http://example.com/?page[number]=1&page[size]=10
//     next     => http://example.com/?page[number]=3&page[size]=10
// )
```



##### Edge cases

On the first and last page, links like `previous` and `next` do not make sense. Their value will be *null*:

```php
$pagination = new Pagination (...);
new $ja_decorator = new JsonApiPaginationDecorator( $pagination, $uri);

$pagination->setCurrent( 0 );
$links = $ja_decorator->getLinks();
// array(
//     ...
//     previous => null
//     next     => http://example.com/?page[number]=1
// )

$pagination->setCurrent( 42 );
$links = $ja_decorator->getLinks();
// array(
//     ...
//     previous => http://example.com/?page[number]=41
//     next     => null
// )
```

When the pagination is not active, all values per default are *null*:

```php
$pagination = new Pagination (...);
$pagination->isActive(); // FALSE

$ja_decorator->getLinks();
// array(
//     first    => null
//     last     => null
//     previous => null
//     next     => null
// )
```



##### Filtering the results

To get a clean, uncluttered `links` array, you may pass a boolean *filter* flag as fourth constructor parameter: 

```php
$filter = true;
new $ja_decorator = new JsonApiPaginationDecorator( $pagination, $uri, [], $filter);

$ja_decorator->getLinks();
// array()
```





## Issues

See [full issues list.][i0]

[i0]: https://github.com/GermaniaKG/Pagination/issues



## Development

```bash
$ git clone https://github.com/GermaniaKG/Pagination.git
$ cd Pagination
$ composer install
```



## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```

