<?php

declare(strict_types=1);

/**
 * Unit Test Case that covers the Environment component.
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 2.6.0
 */

namespace Bartlett\Tests\Reflect;

use Bartlett\Reflect\Presentation\Console\Application;

/**
 * Unit Test Case that covers Bartlett\Reflect\Environment
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class EnvironmentTest extends \PHPUnit\Framework\TestCase
{
    const DIST_RC = 'phpreflect.json.dist';

    /**
     * Clean-up single test environment
     *
     * @return void
     */
    public function tearDown()
    {
        putenv("BARTLETT_SCAN_DIR=");
        putenv("BARTLETTRC=");
    }

    /**
     * @covers \Bartlett\Reflect\Presentation\Console\Application::getJsonConfigFilename
     *
     * @return void
     */
    public function testDefaultEnvironmentVariables()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No configuration file found in BARTLETT_SCAN_DIR paths');

        $app = new Application("phpReflect");
        $app->getJsonConfigFilename();
    }

    /**
     * @covers \Bartlett\Reflect\Presentation\Console\Application::getJsonConfigFilename
     *
     * @return void
     */
    public function testUndefinedConfigFilename()
    {
        putenv("BARTLETTRC=" . self::DIST_RC);
        putenv("BARTLETT_SCAN_DIR=" . getcwd());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No configuration file found in BARTLETT_SCAN_DIR paths');

        $app = new Application("phpReflect");
        $app->getJsonConfigFilename();
    }

    /**
     * @covers \Bartlett\Reflect\Presentation\Console\Application::getJsonConfigFilename
     *
     * @return void
     */
    public function testGetConfigFilenameInSingleScanDirEnvironment()
    {
        $singleScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment' . DIRECTORY_SEPARATOR . 'dir1';

        putenv("BARTLETT_SCAN_DIR=$singleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $app = new Application("phpReflect");

        $this->assertEquals(
            $singleScanDir . DIRECTORY_SEPARATOR . self::DIST_RC,
            $app->getJsonConfigFilename()
        );
    }

    /**
     * @covers \Bartlett\Reflect\Presentation\Console\Application::getJsonConfigFilename
     *
     * @return void
     */
    public function testGetConfigFilenameInMultipleScanDirEnvironment()
    {
        $baseScanDir = __DIR__ . DIRECTORY_SEPARATOR . 'Environment';

        $multipleScanDir = $baseScanDir . DIRECTORY_SEPARATOR . 'dir1'
            . PATH_SEPARATOR .
            $baseScanDir . DIRECTORY_SEPARATOR . 'dir2'
        ;

        putenv("BARTLETT_SCAN_DIR=$multipleScanDir");
        putenv("BARTLETTRC=" . self::DIST_RC);

        $app = new Application("phpReflect");

        $this->assertEquals(
            $baseScanDir . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . self::DIST_RC,
            $app->getJsonConfigFilename()
        );
    }
}
