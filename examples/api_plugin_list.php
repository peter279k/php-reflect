<?php
/**
 * Example of API Plugin list
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-alpha3+1
 */

$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->addClassMap(
    [
        'YourNamespace\LogPlugin'
        =>  __DIR__ . '/YourNamespace/LogPlugin.php',
        'YourNamespace\YourLogger'
        =>  __DIR__ . '/YourNamespace/YourLogger.php',
    ]
);

use Bartlett\Reflect\Application\Command\PluginListCommand;
use Bartlett\Reflect\Application\Command\PluginListHandler;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;

$locator = new InMemoryLocator();
$locator->addHandler(
    new PluginListHandler(),
    PluginListCommand::class
);

$handlerMiddleware = new CommandHandlerMiddleware(
    new ClassNameExtractor(),
    $locator,
    new InvokeInflector()
);

$commandBus = new CommandBus([$handlerMiddleware]);

$command = new PluginListCommand(
    __DIR__ . '/../vendor',
    __DIR__ . '/../',
    __DIR__ . '/YourNamespace/yournamespace.json',
    false
);

// equivalent to CLI command `phpreflect bartlett:plugin:list`
$plugins = $commandBus->handle($command);

print_r($plugins);
