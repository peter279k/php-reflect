<?php
/**
 * Examples of Structure analyser's run with a listener attached.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.0.0-beta2
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
use Symfony\Component\EventDispatcher\GenericEvent;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(
    'reflect.progress',
    function (GenericEvent $e) {
        printf(
            'Parsing Data source "%s" in progress ... File "%s"' . PHP_EOL,
            $e['source'],
            $e['file']->getPathname()
        );
    }
);

$locator = new InMemoryLocator();
$locator->addHandler(
    new AnalyserRunHandler(
        $dispatcher,
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
