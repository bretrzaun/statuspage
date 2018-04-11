<?php
require __DIR__ . '/../vendor/autoload.php';

use BretRZaun\StatusPage\Check\PhpExtensionCheck;
use BretRZaun\StatusPage\Check\PhpMemoryLimitCheck;
use BretRZaun\StatusPage\Check\PhpVersionCheck;
use BretRZaun\StatusPage\StatusChecker;
use BretRZaun\StatusPage\StatusCheckerGroup;

$checker = new StatusChecker();

$group01 = new StatusCheckerGroup('General');
$group01->addCheck(new PhpVersionCheck('PHP Version', '7.0.0', '7.2.0'));
$group01->addCheck(new PhpMemoryLimitCheck('memory_limit', 128));
$checker->addGroup($group01);

$group02 = new StatusCheckerGroup('PHP Extensions');
$group02->addCheck(new PhpExtensionCheck('PHP Extension / cURL', 'curl'));
$group02->addCheck(new PhpExtensionCheck('PHP Extension / Zlib', 'zlib'));
$group02->addCheck(new PhpExtensionCheck('PHP Extension / xml', 'xml', '7.0.0'));
$group02->addCheck(new PhpExtensionCheck('PHP Extension / foo', 'foo'));
$group02->addCheck(new PhpExtensionCheck('PHP Extension / xml', 'xml', '99.0.0'));
$checker->addGroup($group02);

$checker->addCheck(new PhpExtensionCheck('PHP Extension / libxml', 'libxml'));
$checker->addCheck(new PhpExtensionCheck('PHP Extension / SimpleXML', 'SimpleXML'));

// run the checks
$checker->check();

// use the built-in Twig template
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../resources/views/');
$twig = new Twig_Environment($loader, ['autoescape' => false]);

$content = $twig->render(
    'status.twig',
    [
        'results' => $checker->getResults(),
        'title' => 'My status page'
    ]
);

http_response_code($checker->hasErrors() ? 500 : 200);

echo $content;
