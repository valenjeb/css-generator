<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasParent;
use Devly\CssGenerator\Concerns\HasRules;

class Supports
{
    use HasParent;
    use HasRules;

    /**
     * @var string[]
     */
    protected array $feature;

    /**
     * @param string $feature
     * @param CSS|Media|array $rules
     * @param CSS|null $parent
     */
    public function __construct(string $feature, $rules = [], ?CSS $parent = null)
    {
        $this->parent    = $parent;
        $this->rules     = $rules instanceof CSS || $rules instanceof Media ? $rules->getRules() : $rules;
        $this->feature[] = sprintf('(%s)', $feature);
    }

    public function andSupports(string $property): Supports
    {
        $this->feature[] = sprintf('and (%s)', $property);

        return $this;
    }

    public function orSupports(string $property): Supports
    {
        $this->feature[] = sprintf('or (%s)', $property);

        return $this;
    }

    public function notSupports(string $property): Supports
    {
        $this->feature[] = sprintf('not (%s)', $property);

        return $this;
    }

    /**
     * @param string|string[] $queries
     * @param null $rules
     *
     * @return Media
     */
    public function media($queries, $rules = null): Media
    {
        return $this->rules[] = new Media($queries, $rules, $this);
    }

    public function css(bool $minify = false): string
    {
        $line_break = $minify ? '' : "\n";
        $space      = $minify ? '' : ' ';

        $rules = '';

        /** @var Selector $selector */
        foreach ($this->rules as $selector) {
            $rules .= $line_break . $selector->css($minify, true) . $line_break;
        }

        return sprintf(
            '@supports %1$s%2$s{%3$s}',
            implode(' ', $this->feature),
            $space,
            $rules
        );
    }

    public function endSupports(): ?CSS
    {
        return $this->getParentContext();
    }
}
