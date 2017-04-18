<?php

namespace Clim\Helper;

use Clim\Exception\DefinitionException;

const PARSER_PATTERN = '/ \\s* (.+) \\s* \\{ \\s* (.*) \\s* \\} \\s* (.*) \\s* /x';

/**
 * trait DeferredDefinitionTrait
 *
 * The trait which postpone the evaluation of pattern
 * parsing.
 */
trait DeferredDefinitionTrait
{
    /**
     * The definition to be parsed later
     * @var string
     */
    protected $definition;

    /**
     * The flag whether definition is compiled or not
     *  @var bool
     */
    protected $defined = false;

    /**
     * Abstract method which will be called when definition
     * is parsed.
     * BODY {NAME|PATTERN} NOTE
     * @param string $body   body part
     * @param string $name    name part
     * @param string $pattern pattern part
     * @param string $note    note part
     */
    abstract protected function define($body, $name, $pattern, $note);

    /**
     * Ensure definition has been parsed before actual use.
     * It is safe to call it multiple times.
     */
    protected function needDefined()
    {
        if ($this->defined) return;
        $this->defined = true;

        $name = $pattern = $note = null;

        if (preg_match(PARSER_PATTERN, $this->definition, $matches)) {
            $body = $matches[1];
            $name = $matches[2];
            $note = $matches[3];

            $pos = strpos($name, '|');
            if ($pos !== false) {
                $pattern = $this->assertPattern(trim(substr($name, $pos + 1)));

                $name = trim(substr($name, 0, $pos));
            }
        } else {
            $body = $this->definition;
        }

        $this->define($body, $name, $pattern, $note);
    }

    /**
     * Validate the regular expression pattern string
     * @return string|null
     */
    protected function assertPattern($pattern)
    {
        if (strlen($pattern) == 0) return null;

        $pattern = '/^'. str_replace('/', '\\/', $pattern). '$/';
        if (@preg_match($pattern, null) === false) {
            throw new DefinitionException("invalid regular expression pattern");
        }

        return $pattern;
    }
}