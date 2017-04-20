<?php
/**
 * Created by PhpStorm.
 * Date: 4/19/17
 * Time: 12:53 PM
 */

namespace Clim\Cli;


class Parameters
{
    const KIND_NONE = 0;
    const KIND_ARGUMENT = 1;
    const KIND_OPTION_SHORT = 2;
    const KIND_OPTION_LONG = 3;
    const KIND_SEPARATER = 4;

    /** @var array */
    protected $_argv;

    /** @var string hold tentative value */
    protected $_tentative;

    /** @var bool */
    protected $no_more_option = false;

    public function __construct(array $argv)
    {
        $this->_argv = $argv;
    }

    public function dumpOut()
    {
        return array_splice($this->_argv, 0, count($this->_argv));
    }

    public function unshift($value)
    {
        array_unshift($this->_argv, $value);
    }

    /**
     * @param string $value
     * @return int
     */
    protected function kind($value)
    {
        if (is_null($value)) return self::KIND_NONE;

        if ($this->no_more_option) return self::KIND_ARGUMENT;

        if ($value === '--') return self::KIND_SEPARATER;
        if ($value[0] !== '-' || strlen($value) <= 1) return self::KIND_ARGUMENT;
        if ($value[1] !== '-') return self::KIND_OPTION_SHORT;
        return self::KIND_OPTION_LONG;
    }

    public function nextKind()
    {
        $kind = $this->kind($this->peek());
        if ($this->no_more_option) return $kind;

        if ($kind === self::KIND_SEPARATER) {
            $this->no_more_option = true;
            $this->next();

            $kind = $this->kind($this->peek());
        }

        return $kind;
    }

    public function peek()
    {
        return $this->hasMore() ? $this->_argv[0] : null;
    }

    public function next()
    {
        return array_shift($this->_argv);
    }

    public function hasMore()
    {
        return count($this->_argv) > 0;
    }

    public function tentative($value = null)
    {
        if (is_null($value)) {
            if (!is_null($this->_tentative)) {
                $value = $this->_tentative;
                $this->_tentative = null;
            }
            return $value;
        } else {
            $this->_tentative = $value;
            return null;
        }
    }
}