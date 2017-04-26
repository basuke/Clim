<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/19/17
 * Time: 2:58 PM
 */

namespace Clim\Cli;

use Clim\Option;
use Clim\Task;

class Spec
{
    /** @var array */
    private $options = [];

    /** @var array */
    private $arguments = [];

    /** @var array */
    private $tasks = [];

    /** @var MiddlewareStack */
    private $task_middleware;

    /**
     * @param Option[] $options
     * @param ArgumentInterface[] $arguments
     */
    public function __construct(array $options = [], array $arguments = [])
    {
        $this->task_middleware = new MiddlewareStack();

        $this->options = $options;
        $this->arguments = $arguments;
    }

    public function addOption(Option $option)
    {
        $this->options[] = $option;
    }

    public function addArgument(ArgumentInterface $argument)
    {
        $this->arguments[] = $argument;
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function pushMiddleware(callable $middleware)
    {
        $this->task_middleware->push($middleware);
    }

    public function options()
    {
        return $this->options;
    }

    public function arguments()
    {
        return $this->arguments;
    }

    public function tasks()
    {
        return $this->tasks;
    }

    public function taskMiddleware()
    {
        return $this->task_middleware;
    }
}