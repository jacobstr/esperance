<?php namespace Esperance;

class Resemble
{
    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return array('resemble');
    }

    /**
     * Expects two arguments - the low end and high end for a given value.
     */
    public function __invoke($asserter, $args)
    {
        if (count($args) != 1) {
            throw new \BadMethodCallError("Invalid number or arguments.");
        }

        list ($target) = $args;
        $subject = $asserter->getSubject();
        $differences = array();
        $path = array();

        // Keep track of our differences.
        if (!is_array($target) || !is_array($subject)) {
            throw new Error("Comparison is only valid between arrays");
        }

        $this->deepCompare($subject, $target, $differences, array());

        $asserter->assert(
            empty($differences),
            implode("", array(
                "expected ",
                $asserter->i($asserter->getSubject()),
                " to resemble ",
                $asserter->i($target),
                " found differences at ",
                $asserter->i($differences)
            )),
            implode("", array(
                "expected ",
                $asserter->i($asserter->getSubject()),
                " to not resemble ",
                $asserter->i($target)
            ))
        );
    }

    protected function deepCompare($source, $target, &$differences, $path)
    {
        foreach ($target as $key => $value) {
            $current_path = array_merge($path, array($key));

            // Simple existence check.
            if (!isset($source[$key])) {
                $this->setDifference($differences, $current_path, $value);
            // Dealing with arrays.
            } else {
                $source_value = $source[$key];
                if (is_array($value) && !is_array($source_value)) {
                    $this->setDifference($differences, $current_path, $value);
                } else if(is_array($value) && is_array($source_value)) {
                    $this->deepCompare($source_value, $value, $differences, $current_path);
                } else if ($value != $source_value) {
                    $this->setDifference($differences, $current_path, $value);
                }
            }
        }
    }

    protected function setDifference(&$differences, $path, $value)
    {
        $last = array_pop($path);

        $current = &$differences;
        foreach($path as $key) {
            if (!isset($current[$key])) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }

        $current[$last] = $value;
    }
}
