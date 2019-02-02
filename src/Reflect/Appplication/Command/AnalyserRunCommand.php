<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class AnalyserRunCommand
{
    public $source;
    public $analysers;
    public $filter = false;

    public function __construct(string $source, array $analysers)
    {
        $this->source = $source;
        $this->analysers = $analysers;
    }
}