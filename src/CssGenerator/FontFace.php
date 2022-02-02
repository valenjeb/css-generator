<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasParent;

class FontFace
{
    use HasParent;

    /**
     * @var array<string, int|string>
     */
    protected array $rules = [];

    public function __construct(?CSS $parent = null)
    {
        $this->parent = $parent;
    }

    public function fontFamily(string $name): FontFace
    {
        return $this->addRule('font-family', $name);
    }

    public function src(string $value): FontFace
    {
        return $this->addRule('src', $value);
    }

    public function fontStretch(string $value): FontFace
    {
        return $this->addRule('font-stretch', $value);
    }

    public function unicodeRange(string $value): FontFace
    {
        return $this->addRule('unicode-range', $value);
    }

    public function fontStyle(string $style): FontFace
    {
        return $this->addRule('font-style', $style);
    }

    public function fontWeight(string $name): FontFace
    {
        return $this->addRule('font-weight', $name);
    }

    /**
     * @param string $property
     * @param int|string $value
     *
     * @return FontFace
     */
    public function addRule(string $property, $value): FontFace
    {
        $this->rules[$property] = $value;

        return $this;
    }

    public function css(bool $minify = false): string
    {
        $indent     = $minify ? '' : CSS::$indent;
        $line_break = $minify ? '' : "\n";
        $space      = $minify ? '' : ' ';

        $rules = implode($line_break . $indent, $this->parseRules($space));

        return sprintf(
            '@font-face {%1$s%2$s%3$s%1$s}',
            $line_break,
            $indent,
            $rules
        );
    }

    public function __toString()
    {
        return $this->css();
    }

    public function endFontFace(): ?CSS
    {
        return $this->getParentContext();
    }

    /**
     * @param string $space
     *
     * @return string[]
     */
    protected function parseRules(string $space): array
    {
        $rules = [];
        foreach ($this->getRules() as $property => $value) {
            $rules[] = sprintf('%s:%s%s;', $property, $space, $value);
        }

        return $rules;
    }

    /**
     * @return int[]|string[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
