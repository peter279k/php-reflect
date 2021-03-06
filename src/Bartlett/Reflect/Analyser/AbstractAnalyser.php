<?php
/**
 * Base class to all analysers accessible through the AnalyserPlugin.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect;

use PhpParser\Node;
use PhpParser\NodeVisitor;

/**
 * Provides common metrics for all analysers.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
abstract class AbstractAnalyser implements AnalyserInterface, NodeVisitor
{
    protected $namespaces = array();
    protected $testClass;
    protected $tokens;
    protected $file;
    protected $metrics = array();

    protected $subject;

    public function getSubject()
    {
        return $this->subject;
    }

    public function getCurrentFile()
    {
        return $this->file;
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function setSubject(Reflect $reflect)
    {
        $this->subject = $reflect;
    }

    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function setCurrentFile($path)
    {
        $this->file = $path;
    }

    public function getMetrics()
    {
        return array(get_class($this) => $this->metrics);
    }

    public function getName()
    {
        $parts = explode('\\', get_class($this));
        return array_pop($parts);
    }

    public function getNamespace()
    {
        return implode('\\', array_slice(explode('\\', get_class($this)), 0, -1));
    }

    public function getShortName()
    {
        return strtolower(str_replace('Analyser', '', $this->getName()));
    }

    public function beforeTraverse(array $nodes)
    {
        $this->subject->dispatch(
            Reflect\Events::BUILD,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
            )
        );
    }

    public function enterNode(Node $node)
    {
        $this->subject->dispatch(
            Reflect\Events::BUILD,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => $node,
            )
        );
    }

    public function leaveNode(Node $node)
    {
        $this->subject->dispatch(
            Reflect\Events::BUILD,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => $node,
            )
        );
    }

    public function afterTraverse(array $nodes)
    {
        $this->subject->dispatch(
            Reflect\Events::BUILD,
            array(
                'method' => get_class($this) . '::' . __FUNCTION__,
                'node'   => null,
            )
        );
    }

    /**
     * Visits a namespace node.
     *
     * @param Node\Stmt\Namespace_ $namespace Represents a namespace in the data source
     *
     * @return void
     */
    protected function visitNamespace(Node\Stmt\Namespace_ $namespace)
    {
        $this->namespaces[] = $namespace->name;
    }

    /**
     * Visits a class node.
     *
     * @param Node\Stmt\Class_ $class Represents a class in the namespace
     *
     * @return void
     */
    protected function visitClass(Node\Stmt\Class_ $class)
    {
        $this->testClass = false;

        $parent = $class->extends;

        if (empty($parent)) {
            // No ancestry
            // Treat the class as a test case class if the name
            // of the parent class ends with "TestCase".

            if (substr($class->name, -8) == 'TestCase') {
                $this->testClass = true;
            }
        } else {
            // Ancestry
            // Treat the class as a test case class if the name
            // of the parent class equals to "PHPUnit_Framework_TestCase".

            if ((string) $parent === 'PHPUnit_Framework_TestCase') {
                $this->testClass = true;
            }
        }
    }

    /**
     * Checks if a property is implicitly public (PHP 4 syntax)
     *
     * @param array              $tokens
     * @param Node\Stmt\Property $prop
     *
     * @return boolean
     */
    protected function isImplicitlyPublicProperty(array $tokens, Node\Stmt\Property $prop)
    {
        $i = $prop->getAttribute('startTokenPos');
        return (isset($tokens[$i]) && $tokens[$i][0] == T_VAR);
    }

    /**
     * Checks if a method is implicitly public (PHP 4 syntax)
     *
     * @param array                 $tokens
     * @param Node\Stmt\ClassMethod $method
     *
     * @return boolean
     */
    protected function isImplicitlyPublicFunction(array $tokens, Node\Stmt\ClassMethod $method)
    {
        $i = $method->getAttribute('startTokenPos');
        for ($c = count($tokens); $i < $c; ++$i) {
            $t = $tokens[$i];
            if ($t[0] == T_PUBLIC || $t[0] == T_PROTECTED || $t[0] == T_PRIVATE) {
                return false;
            }
            if ($t[0] == T_FUNCTION) {
                break;
            }
        }
        return true;
    }

    /**
     * Checks if array syntax is normal or short (PHP 5.4+ feature)
     *
     * @param array            $tokens
     * @param Node\Expr\Array_ $array
     *
     * @return boolean
     */
    protected function isShortArraySyntax(array $tokens, Node\Expr\Array_ $array)
    {
        $i = $array->getAttribute('startTokenPos');
        return is_string($tokens[$i]);
    }
}
