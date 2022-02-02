<?php

namespace Devly\CssGenerator\Concerns;

use Devly\CssGenerator\Media;
use Devly\CssGenerator\Selector;

trait HasRules
{
    /**
     * @var Selector[]|Media[]
     */
    protected array $rules = [];

    /**
     * @param string|string[] $selector
     * @param string[]|int[] $rules
     *
     * @return Selector
     */
    public function selector($selector, array $rules = []): Selector
    {
        return $this->rules[] = new Selector($selector, $rules, $this);
    }
}
