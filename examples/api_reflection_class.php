<?php
/**
 * Example of API Class Reflection.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-alpha3+1
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bartlett\Reflect\Application\Command\ReflectionClassCommand;
use Bartlett\Reflect\Application\Command\ReflectionClassHandler;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;

$locator = new InMemoryLocator();
$locator->addHandler(
    new ReflectionClassHandler(),
    ReflectionClassCommand::class
);

$handlerMiddleware = new CommandHandlerMiddleware(
    new ClassNameExtractor(),
    $locator,
    new InvokeInflector()
);

$commandBus = new CommandBus([$handlerMiddleware]);

$dataSource = dirname(__DIR__) . '/src';

$command = new ReflectionClassCommand(\Bartlett\Reflect::class, $dataSource, 'txt');

// equivalent to CLI command `phpreflect bartlett:reflection:class Bartlett\Reflect ../src`
$model = $commandBus->handle($command);

echo $model;
