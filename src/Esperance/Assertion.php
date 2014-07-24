<?php
/**
 * Esperance
 *
 * BDD style assertion library for PHP
 *
 * @author Yuya Takeyama
 */
namespace Esperance;

use Esperance\Extension;
use Dumpling\Dumpling;

class Assertion
{
    /**
     * Subject for assertion.
     *
     * @var mixed
     */
    private $subject;

    /**
     * Optional descriptive prefix.
     *
     * @var string
     */
    private $explanation;

    private $flags;

    private $extension;

    private $dumper;

    private $aliases = array(
        'equal'       => 'be',
        'throw'       => 'throwException',
        'throwError'  => 'throwException',
        'callable'    => 'invokable',
        'an'          => 'a',
        'empty'       => '_empty',
        'greaterThan' => 'above',
        'lessThan'    => 'below',
    );

    public function __construct($subject, $extension = NULL, $dumper = NULL)
    {
        $this->subject = $subject;
        $this->flags = array();
        $this->dumper = $dumper ? $dumper : new Dumpling();
        $this->extension = $extension ? $extension : new Extension;
    }

    public function __get($key)
    {
        if ($key === 'and') {
            return new Assertion($this->subject, $this->extension);
        } else {
            $this->flags[$key] = true;
            return $this;
        }
    }

    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->aliases)) {
            return call_user_func_array(array($this, $this->aliases[$method]), $args);
        } else {
            throw new \BadMethodCallException("Undefined method {$this->i($method)} is called");
        }
    }

    public function assert($truth, $message, $error)
    {
        $this->extension->emitBeforeAssertion();
        $message = isset($this->flags['not']) && $this->flags['not'] ? $error : $message;
        $ok = isset($this->flags['not']) && $this->flags['not'] ? !$truth : $truth;

        if (!$ok) {
            $this->throwAssertionError($message);
        }
        $this->extension->emitAssertionSuccess();
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function beforeAssertion($callback)
    {
        $this->extension->beforeAssertion($callback);
    }

    public function onAssertionSuccess($callback)
    {
        $this->extension->onAssertionSuccess($callback);
    }

    public function onAssertionFailure($callback)
    {
        $this->extension->onAssertionFailure($callback);
    }

    public function explain($string)
    {
        $this->explanation = $string;

        return $this;
    }

    public function be($obj)
    {
        $this->assert(
            $obj === $this->subject,
            $this->msg("expected {$this->i($this->subject)} to equal {$this->i($obj)}"),
            $this->msg("expected {$this->i($this->subject)} to not equal {$this->i($obj)}")
        );
        return $this;
    }

    public function eql($obj)
    {
        return $this->assert(
            $this->subject == $obj,
            $this->msg("expected {$this->i($this->subject)} to sort of equal {$this->i($obj)}"),
            $this->msg("expected {$this->i($this->subject)} to sort of not equal {$this->i($obj)}")
        );
    }

    public function ok()
    {
        $this->assert(
            !!$this->subject,
            $this->msg("expected {$this->i($this->subject)} to be truthy"),
            $this->msg("expected {$this->i($this->subject)} to be falsy")
        );
    }

    public function throwException($klass = NULL, $expectedMessage = NULL)
    {
        $thrown = false;
        $thrownKlass = NULL;
        try {
            call_user_func($this->subject);
        } catch (\Exception $e) {
            if (is_null($klass)) {
                $thrown = true;
            } else if (is_string($klass) && \is_a($e, $klass)) {
                $thrown = true;
            }
            $thrownKlass = get_class($e);
            $message = $e->getMessage();
        }

        if (is_null($klass)) {
            $this->assert(
                $thrown,
                $this->msg('expected function to throw an exception'),
                $this->msg('expected function not to throw an exception')
            );
        } else {
            $this->assert(
                $thrown,
                $this->msg(
                  "expected function to throw {$klass}" .
                  ($thrownKlass ? " but got {$thrownKlass}" : '')
                ),
                $this->msg("expected function not to throw {$klass}")
            );
        }
        if ($thrown && $expectedMessage && $message !== $expectedMessage) {
            $this->throwAssertionError($this->msg(
                "expected exception message {$this->i($message)} to be {$this->i($expectedMessage)}"
            ));
        }
    }

    public function invokable()
    {
        $this->assert(
            \is_callable($this->subject),
            $this->msg("expected {$this->i($this->subject)} to be callable"),
            $this->msg("expected {$this->i($this->subject)} to not be callable")
        );
    }

    public function a($type)
    {
        if (\is_string($type)) {
            $article = preg_match('/^[aeiou]/i', $type) ? 'an' : 'a';

            $this->assert(
                \is_a($this->subject, $type),
                $this->msg("expected {$this->i($this->subject)} to be {$article} {$type}"),
                $this->msg("expected {$this->i($this->subject)} not to be {$article} {$type}")
            );
        } else {
            $type = get_class($type);
            $this->assert(
                \is_a($this->subject, $type),
                $this->msg("expected {$this->i($this->subject)} to be an instance of {$type}"),
                $this->msg("expected {$this->i($this->subject)} not to be an instance of {$type}")
            );
        }
        return $this;
    }

    public function _empty()
    {
        $this->assert(
            empty($this->subject),
            $this->msg("expected {$this->i($this->subject)} to be empty"),
            $this->msg("expected {$this->i($this->subject)} to not be empty")
        );
        return $this;
    }

    public function within($start, $finish)
    {
        $range = "{$start}..{$finish}";
        $this->assert(
            $this->subject >= $start && $this->subject <= $finish,
            $this->msg("expected {$this->i($this->subject)} to be within {$range}"),
            $this->msg("expected {$this->i($this->subject)} to not be within {$range}")
        );
    }

    public function above($n)
    {
        $this->assert(
            $this->subject > $n,
            $this->msg("expected {$this->i($this->subject)} to be above {$this->i($n)}"),
            $this->msg("expected {$this->i($this->subject)} to be below {$this->i($n)}")
        );
        return $this;
    }

    public function below($n)
    {
        $this->assert(
            $this->subject < $n,
            $this->msg("expected {$this->i($this->subject)} to be below {$this->i($n)}"),
            $this->msg("expected {$this->i($this->subject)} to be above {$this->i($n)}")
        );
        return $this;
    }

    public function match($regexp)
    {
        $this->assert(
            preg_match($regexp, $this->subject),
            $this->msg("expected {$this->i($this->subject)} to match {$regexp}"),
            $this->msg("expected {$this->i($this->subject)} not to match {$regexp}")
        );
        return $this;
    }

    public function length($n)
    {
        if (is_array($this->subject) || (is_object($this->subject) && $this->subject instanceof \Countable)) {
            $len = count($this->subject);
        } else if (is_string($this->subject)) {
            $len = strlen($this->subject);
        } else {
            throw new \InvalidArgumentException('Expected subject for length() is array, string or Countable.');
        }
        $this->assert(
            $len === $n,
            $this->msg("expected {$this->i($this->subject)} to have a length of {$this->i($n)} but got {$len}"),
            $this->msg("expected {$this->i($this->subject)} to not have a length of {$len}")
        );
        return $this;
    }

    private function i($obj)
    {
        return $this->dumper->dump($obj);
    }

    protected function throwAssertionError($message)
    {
        $error = new Error($message);
        $this->extension->emitAssertionFailure(array($error));
        throw $error;
    }

    /**
     * Formats an error message with additional information.
     */
    protected function msg($message)
    {
        if($this->explanation) {
            return $this->explanation . ' : ' . $message;
        } else {
            return $message;
        }
    }
}
