<?php
/**
 * Examples of Structure and Loc analyser's run with filter applied on final results.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Example available since Release 3.1.0
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

// filter rules on final results
$dispatcher->addListener(
    'reflect.terminate',
    function (GenericEvent $e) {
        $data = $e['metrics'];

        $filterOnKeys = array(
            'classes', 'abstractClasses', 'concreteClasses',
            'classConstants', 'globalConstants', 'magicConstants',
        );

        foreach ($data as $title => &$keys) {
            if (strpos($title, 'StructureAnalyser') === false) {
                continue;
            }
            // looking into Structure Analyser metrics and keep classes and constants info
            foreach ($keys as $key => $val) {
                if (!in_array($key, $filterOnKeys)) {
                    unset($keys[$key]);  // "removed" unsolicited values
                    continue;
                }
            }
        }

        $e['metrics'] = $data;
    }
);

$locator = new InMemoryLocator();
$locator->addHandler(
    new AnalyserRunHandler(
        $dispatcher,
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

$metrics = $commandBus->handle($command);

var_export($metrics);
