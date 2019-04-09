<?php

declare(strict_types=1);

/**
 * Unit tests for PHP_Reflect package, issues reported
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 * @since      Class available since Release 3.0.0-alpha2
 */

namespace Bartlett\Tests\Reflect\Model;

use Bartlett\Reflect\Application\Model\ClassModel;

/**
 * Tests for PHP_CompatInfo, retrieving reference elements,
 * and versioning information.
 *
 * @category   PHP
 * @package    PHP_Reflect
 * @subpackage Tests
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @license    https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link       http://php5.laurent-laville.org/reflect/
 */
class IssueTest extends GenericModelTest
{
    private const GH4 = 'packages.php';

    /**
     * Sets up the shared fixture.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$fixture = self::GH4;
        parent::setUpBeforeClass();
    }

    /**
     * Regression test for bug GH#4
     *
     * @link https://github.com/llaville/php-reflect/issues/
     *       "Handle namespaces without name"
     * @link https://github.com/llaville/php-reflect/pull/4 by Eric Colinet
     * @group regression
     * @return void
     */
    public function testBugGH4()
    {
        $c = 0;    // empty namespace, class MyGlobalClass

        $this->assertInstanceOf(
            ClassModel::class,
            self::$models[$c]
        );
        $this->assertEquals('MyGlobalClass', self::$models[$c]->getName());
    }
}
