<?php namespace Esperance;

class Between
{
    /**
     * @inheritDoc
     */
    public static function getAliases()
    {
        return array('between');
    }

    /**
     * Expects two additional arguments - the low end and high end for a given value.
     */
    public function __invoke($asserter, $args)
    {
        if (count($args) != 2) {
            throw new \BadMethodCallError("Invalid number or arguments.");
        }

        list($low_open, $high_open) = $args;

        $asserter->assert(
            $subject >= $low_open && $value <= $high_open,
            "expected {$asserter->i($subject)} to be between $low_open and $high_open",
            "expected {$asserter->i($subject)} to not be between $low_open and $high_open"
        );
    }
}
