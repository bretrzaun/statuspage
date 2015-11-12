# StatusPage

[![Build Status](https://travis-ci.org/bretrzaun/statuspage.svg?branch=master)](https://travis-ci.org/bretrzaun/statuspage)

Add a simple status page to Silex application with custom checks.

The status page runs all registers checks and renders a page showing its results.

## Installation

```
composer require bretrzaun/statuspage
```

## Usage

Registering the service provider you add the checks via a callback method: 

```
$app->register(new \BretRZaun\StatusPage\StatusPageServiceProvider(), array(
    'statuspage.title' => 'MySilexApp - Status Page',
    'statuspage.checker' => $app->protect(function($app, $statusChecker) {
        $check = new \BretRZaun\StatusPage\Check\DoctrineConnectionCheck('Database', $app['db']);
        $statusChecker->addCheck($check);
        
        // ... add more checks here
    })
));
```

## Tests

To run the tests, just enter:

```
composer install
vendor/bin/phpunit
```
