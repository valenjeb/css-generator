<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasParent;

/**
 * Generate CSS import statement
 */
class Import
{
    use HasParent;

    protected string $url;
    protected ?string $supports;
    /**
     * @var string[]
     */
    protected array $media_queries;

    /**
     * @param string $url
     * @param string|string[] $media_queries
     * @param CSS|null $parent
     */
    public function __construct(string $url, $media_queries = [], ?CSS $parent = null)
    {
        $this->parent        = $parent;
        $this->url           = $url;
        $this->media_queries = is_array($media_queries) ? $media_queries : [$media_queries];
    }

    /**
     * Add conditional media query statement.
     *
     * @param string|string[] $query
     *
     * @return Import
     */
    public function media($query): Import
    {
        $query = is_array($query) ? $query : func_get_args();
        foreach ($query as $q) {
            $this->media_queries[] = $q;
        }

        return $this;
    }

    /**
     * Add conditional supports statement.
     *
     * @param string $supports
     *
     * @return Import
     */
    public function supports(string $supports): Import
    {
        $this->supports = $supports;

        return $this;
    }

    /**
     * Generates CSS string of import statement.
     *
     * @return string
     */
    public function css(): string
    {
        $output = sprintf('@import "%s"', $this->url);
        if (isset($this->supports)) {
            $output .= sprintf(' supports(%s)', $this->supports);
        }

        if (! empty($this->media_queries)) {
            $output .= sprintf(' %s', implode(', ', $this->media_queries));
        }

        $output .= ';';

        return $output;
    }

    /**
     * End import statement and get instance of parent class.
     *
     * @return CSS|null
     */
    public function endImport(): ?CSS
    {
        return $this->getParentContext();
    }

    public function __toString()
    {
        return $this->css();
    }
}
