<?php
/**
 * Examples of Structure analyser's run with a custom log plugin.
 *
 * <code>
 *   array (
 *   'files' =>
 *   array (
 *     // ...
 *   ),
 *   'errors' =>
 *   array (
 *   ),
 *   'Bartlett\\Reflect\\Application\\Analyser\\StructureAnalyser' =>
 *   array (
 *     'namespaces' => 20,
 *     'interfaces' => 8,
 *     'traits' => 0,
 *     'classes' => 57,
 *     'abstractClasses' => 7,
 *     'concreteClasses' => 50,
 *     'functions' => 8,
 *     'namedFunctions' => 0,
 *     'anonymousFunctions' => 8,
 *     'methods' => 265,
 *     'publicMethods' => 211,
 *     'protectedMethods' => 53,
 *     'privateMethods' => 1,
 *     'nonStaticMethods' => 261,
 *     'staticMethods' => 4,
 *     'constants' => 0,
 *     'classConstants' => 12,
 *     'globalConstants' => 0,
 *     'magicConstants' => 3,
 *     'testClasses' => 0,
 *     'testMethods' => 0,
 *   ),
 * </code>
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-beta2
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

use Bartlett\Reflect\Application\Command\AnalyserRunCommand;
use Bartlett\Reflect\Application\Command\AnalyserRunHandler;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use Symfony\Component\EventDispatcher\EventDispatcher;

$locator = new InMemoryLocator();
$locator->addHandler(
    new AnalyserRunHandler(
        new EventDispatcher(),
        __DIR__ . '/YourNamespace/yournamespace.json'
    ),
    AnalyserRunCommand::class
);

$handlerMiddleware = new CommandHandlerMiddleware(
    new ClassNameExtractor(),
    $locator,
    new InvokeInflector()
);

$commandBus = new CommandBus([$handlerMiddleware]);

// perform request, on a data source with default analyser (structure)
$dataSource = dirname(__DIR__) . '/src';
$analysers  = ['structure'];

$command = new AnalyserRunCommand($dataSource, $analysers, false);

// equivalent to CLI command `phpreflect bartlett:analyser:run ../src`
$metrics = $commandBus->handle($command);

var_export($metrics);
