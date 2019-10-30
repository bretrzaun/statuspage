<?php
require __DIR__ . '/../vendor/autoload.php';

use BretRZaun\StatusPage\Check\PhpExtensionCheck;
use BretRZaun\StatusPage\Check\PhpIniCheck;
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

$group03 = new StatusCheckerGroup('PHP.ini');
// php.ini default value: true
$group03->addCheck(new PhpIniCheck('BooleanCheck for allow_url_fopen', 'allow_url_fopen', PhpIniCheck::TypeBoolean, true, null));
// php.ini default value: 128M
$group03->addCheck(new PhpIniCheck('MemoryCheck for memory_limit', 'memory_limit', PhpIniCheck::TypeMemory, 256, null));
// php.ini default value: 1000
$group03->addCheck(new PhpIniCheck('NumberCheck for max_input_vars', 'max_input_vars', PhpIniCheck::TypeNumber, 1000, null));
// php.ini default value: UTF-8
$group03->addCheck(new PhpIniCheck('RegexCheck for default_charset', 'default_charset', PhpIniCheck::TypeRegex, 'UTF-[1-9]+', null));
// php.ini default value: UTF-8
$group03->addCheck(new PhpIniCheck('StringCheck for default_charset', 'default_charset', PhpIniCheck::TypeString, 'UTF-8', null));
$checker->addGroup($group03);

$checker->addCheck(new PhpExtensionCheck('PHP Extension / libxml', 'libxml'));
$checker->addCheck(new PhpExtensionCheck('PHP Extension / SimpleXML', 'SimpleXML'));

// run the checks
$checker->check();

// determine if the user is allowed to see the details (based on their IP)
$whitelistPattern = '.*'; // this intentionally matches everything
$showDetails = (bool)preg_match('|(?mi-Us)' . $whitelistPattern . '|', $_SERVER['REMOTE_ADDR']);

// use the built-in Twig template
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../resources/views/');
$twig = new Twig_Environment($loader, ['autoescape' => false]);

$content = $twig->render(
    'status.twig',
    [
        'results' => $checker->getResults(),
        'title' => 'My status page',
        'showDetails' => $showDetails,
    ]
);

http_response_code($checker->hasErrors() ? 500 : 200);

echo $content;
