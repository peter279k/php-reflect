<?php
/**
 * Examples of Structure and Loc analyser's run.
 *
 * <code>
 * array (
 *  'files' =>
 *  array (
 *    // ...
 *  ),
 *
 *  'errors' =>
 *  array (
 *  ),
 *  'Bartlett\\Reflect\\Application\\Analyser\\StructureAnalyser' =>
 *  array (
 *    'namespaces' => 20,
 *    'interfaces' => 8,
 *    'traits' => 0,
 *    'classes' => 57,
 *    'abstractClasses' => 7,
 *    'concreteClasses' => 50,
 *    'functions' => 8,
 *    'namedFunctions' => 0,
 *    'anonymousFunctions' => 8,
 *    'methods' => 265,
 *    'publicMethods' => 211,
 *    'protectedMethods' => 53,
 *    'privateMethods' => 1,
 *    'nonStaticMethods' => 261,
 *    'staticMethods' => 4,
 *    'constants' => 0,
 *    'classConstants' => 12,
 *    'globalConstants' => 0,
 *    'magicConstants' => 3,
 *    'testClasses' => 0,
 *    'testMethods' => 0,
 *  ),
 *  'Bartlett\\Reflect\\Application\\Analyser\\LocAnalyser' =>
 *  array (
 *    'llocClasses' => 988,
 *    'llocByNoc' => 0,
 *    'llocByNom' => 0,
 *    'llocFunctions' => 34,
 *    'llocByNof' => 0,
 *    'llocGlobal' => 0,
 *    'classes' => 57,
 *    'functions' => 8,
 *    'methods' => 293,
 *    'cloc' => 79,
 *    'eloc' => 2568,
 *    'lloc' => 1022,
 *    'wloc' => 311,
 *    'loc' => 2958,
 *    'ccn' => 469,
 *    'ccnMethods' => 449,
 *  ),
 * )
 * </code>
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-alpha3
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

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
        __DIR__ . '/../bin/phpreflect.json.dist'
    ),
    AnalyserRunCommand::class
);

$handlerMiddleware = new CommandHandlerMiddleware(
    new ClassNameExtractor(),
    $locator,
    new InvokeInflector()
);

$commandBus = new CommandBus([$handlerMiddleware]);

// perform request, on a data source with two analysers (structure, loc)
$dataSource = dirname(__DIR__) . '/src';
$analysers  = ['structure', 'loc'];

$command = new AnalyserRunCommand($dataSource, $analysers, true);

// equivalent to CLI command `phpreflect bartlett:analyser:run ../src structure loc`
$metrics = $commandBus->handle($command);

var_export($metrics);
