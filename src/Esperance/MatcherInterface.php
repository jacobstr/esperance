<?php namespace Esperance;

interface MatcherInterface
{
    /**
     * Should return an array of one or more strings representing various
     * possible aliases for this matcher. The first element returned should be
     * considered the canonical name.
     *
     * @return Array
     */
    public function getAliases();

    /**
     * Invoke this matcher.
     *
     * @param mixed $subject The value being tested.
     * @return array(bool, string) Success / Failure status and an optional error message.
     */
    public function execute($subject, $args);
}
