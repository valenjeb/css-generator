<?php

namespace Devly\CssGenerator\Concerns;

use BadMethodCallException;
use Devly\CssGenerator\CSS;
use Devly\CssGenerator\Media;

trait HasParent
{
    /**
     * @var CSS|Media|null
     */
    protected $parent;

    /**
     * @return CSS|Media|null
     */
    public function getParentContext()
    {
        return $this->parent;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call(string $name, $arguments)
    {
        if ($this->parent && method_exists($this->parent, $name)) {
            return call_user_func([$this->parent, $name], ...$arguments);
        }

        throw new BadMethodCallException($name . ' method does not exit.');
    }
}
