<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasParent;
use LogicException;

class FontFace
{
    use HasParent;

    /**
     * @var array<string, int|string>
     */
    protected array $descriptors = [];

    public function __construct(?CSS $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Defines the name of the font.
     *
     * Specifies a name that will be used as the font face value for font properties.
     *
     * @param string $name
     *
     * @return FontFace
     */
    public function fontFamily(string $name): FontFace
    {
        return $this->addRule('font-family', $name);
    }

    /**
     * Defines the URL(s) where the font should be downloaded from
     *
     * This can be a URL to a remote font file location or the name
     * of a font on the user's computer.
     *
     * @param string $value
     *
     * @return FontFace
     */
    public function src(string $value): FontFace
    {
        return $this->addRule('src', $value);
    }

    /**
     * Defines how the font should be stretched
     *
     * Accepts two values to specify a range that is supported by a font-face,
     * for example font-stretch: 50% 200%;
     *
     * Default value is "normal"
     *
     * @param string $value
     *
     * @return FontFace
     */
    public function fontStretch(string $value): FontFace
    {
        return $this->addRule('font-stretch', $value);
    }

    /**
     * Defines the range of unicode characters the font supports
     *
     * Default value is "U+0-10FFFF".
     *
     * @param string $value
     *
     * @return FontFace
     */
    public function unicodeRange(string $value): FontFace
    {
        return $this->addRule('unicode-range', $value);
    }

    /**
     * Defines how the font should be styled
     *
     * Accepts two values to specify a range that is supported by a font-face,
     * for example font-style: oblique 20deg 50deg;
     *
     * Default value is "normal".
     *
     * @param string $style
     *
     * @return FontFace
     */
    public function fontStyle(string $style): FontFace
    {
        return $this->addRule('font-style', $style);
    }

    /**
     * Defines the boldness of the font
     *
     * Accepts two values to specify a range that is supported by a font-face,
     * for example font-weight: 100 400;
     *
     * Default value is "normal".
     *
     * @param string $value
     *
     * @return FontFace
     */
    public function fontWeight(string $value): FontFace
    {
        return $this->addRule('font-weight', $value);
    }

    /**
     * @param string $property
     * @param int|string $value
     *
     * @return FontFace
     */
    public function addRule(string $property, $value): FontFace
    {
        $this->descriptors[$property] = $value;

        return $this;
    }

    /**
     * Generates @font-face CSS string.
     *
     * @param bool $minify
     *
     * @return string
     */
    public function css(bool $minify = false): string
    {
        if (! isset($this->descriptors['font-family'])) {
            throw new LogicException('"font-family" is a mandatory font descriptor.');
        }

        if (! isset($this->descriptors['src'])) {
            throw new LogicException('"src" is a mandatory font descriptor.');
        }

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

    /**
     * End @font-face statement and get parent context.
     *
     * @return CSS|null
     */
    public function endFontFace(): ?CSS
    {
        return $this->getParentContext();
    }

    /**
     * Generates a CSS string of all the @font-face descriptors.
     *
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
        return $this->descriptors;
    }
}
