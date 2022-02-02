<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasParent;
use Devly\CssGenerator\Concerns\HasRules;

class Media
{
    use HasParent;
    use HasRules;

    /**
     * @var string[]
     */
    protected array $queries;

    /**
     * @param string|string[] $queries
     * @param CSS|Selector[]|null $rules
     * @param CSS|Supports|null $parent
     */
    public function __construct($queries, $rules = null, $parent = null)
    {
        $this->queries = is_array($queries) ? $queries : [$queries];

        if (! empty($rules)) {
            $this->rules = $rules instanceof CSS ? $rules->getRules() : $rules;
        }

        $this->parent = $parent;
    }

    public function css(bool $minify = false): string
    {
        $line_break = $minify ? '' : "\n";
        $space      = $minify ? '' : ' ';

        $output = sprintf('@media %s%s{', implode(', ', $this->queries), $space);

        /** @var Selector $selector */
        foreach ($this->rules as $selector) {
            $output .= $line_break . $selector->css($minify, true) . $line_break;
        }

        return $output . '}';
    }

    /**
     * @return CSS|Media|null
     */
    public function endMedia()
    {
        return $this->getParentContext();
    }
}
