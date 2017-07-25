# StatusPage

[![Build Status](https://travis-ci.org/bretrzaun/statuspage.svg?branch=master)](https://travis-ci.org/bretrzaun/statuspage)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bretrzaun/statuspage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bretrzaun/statuspage/?branch=master)

Add a simple status page to Silex applications with custom checks.

The status page runs all registered checks and renders a page showing its results.

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

## Out-of-the-box checks

- **CallbackCheck**: generic check using a PHP callback function
- **DoctrineConnectionCheck**: checks for a valid [Doctrine DBAL](http://www.doctrine-project.org/projects/dbal.html) connection
- **LogFileContentCheck**: check a (log) file for certain content
- **UrlCheck**: checks a URL
- **PhpExtensionCheck**: check a given PHP extension is loaded
 
### Custom checks

Custom checks can be easily added by inheriting ```BretRZaun\StatusPage\Check\AbstractCheck```.

## Tests

To run the tests, just enter:

```
composer install
vendor/bin/phpunit
```
