<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasParent;
use InvalidArgumentException;

class Selector
{
    use HasParent;

    /**
     * @var string[]
     */
    protected array $selector;
    /**
     * @var int[]|string[]
     */
    protected array $rules;

    /**
     * @param string|string[] $selector
     * @param int[]|string[] $rules
     * @param CSS|Media|null $parent
     */
    public function __construct($selector, array $rules = [], $parent = null)
    {
        $this->parent   = $parent;
        $this->selector = is_array($selector) ? $selector : [$selector];
        $this->rules    = $rules;
    }

    /**
     * Generates CSS string.
     *
     * @param bool $minify Whether to minify the returned CSS string. Defaults to false.
     *
     * @return string Generated CSS string.
     */
    public function css(bool $minify = false, bool $is_indent = false): string
    {
        $indent     = $minify ? '' : CSS::$indent;
        $line_break = $minify ? '' : "\n";
        $space      = $minify ? '' : ' ';

        $rules = implode($line_break . $indent . ($is_indent ? $indent : ''), $this->parseRules($space));

        $output  = $is_indent ? $indent : '';
        $output .= sprintf(
            '%1$s%2$s{%3$s%4$s%5$s%6$s}',
            $this->name($line_break, $is_indent ? $indent : ''),
            $space,
            $line_break,
            $is_indent ? $indent . $indent : $indent,
            $rules,
            $line_break . ($is_indent ? $indent : '')
        );

        return $output;
    }

    /**
     * @param string $space
     *
     * @return string[]
     */
    public function parseRules(string $space = ' '): array
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

    /**
     * @param string $line_break
     * @param string $indent
     *
     * @return string
     */
    protected function name(string $line_break, string $indent): string
    {
        return implode(',' . $line_break . $indent, $this->selector);
    }

    /**
     * @return CSS|Media|null
     */
    public function endSelector()
    {
        return $this->getParentContext();
    }

    public function display(string $value): Selector
    {
        return $this->addRule('display', $value);
    }

    /**
     * @param string|Selector $property
     * @param string|int $value
     *
     * @return Selector
     */
    public function addRule($property, $value): Selector
    {
        if ($property instanceof Selector) {
            $this->rules = array_merge($this->rules, $property->getRules());
        } else {
            $this->rules[$property] = $value;
        }

        return $this;
    }

    public function alignContent(string $value): Selector
    {
        return $this->addRule('align-content', $value);
    }

    public function alignItems(string $value): Selector
    {
        return $this->addRule('align-items', $value);
    }

    public function alignSelf(string $value): Selector
    {
        return $this->addRule('align-self', $value);
    }

    public function all(string $value): Selector
    {
        return $this->addRule('all', $value);
    }

    public function animation(string $value): Selector
    {
        return $this->addRule('animation', $value);
    }

    public function animationDelay(string $value): Selector
    {
        return $this->addRule('animation-delay', $value);
    }

    public function animationDuration(string $value): Selector
    {
        return $this->addRule('animation-duration', $value);
    }

    public function animationFillMode(string $value): Selector
    {
        return $this->addRule('animation-fill-mode', $value);
    }

    public function animationIterationCount(string $value): Selector
    {
        return $this->addRule('animation-iteration-count', $value);
    }

    public function animationName(string $value): Selector
    {
        return $this->addRule('animation-name', $value);
    }

    public function animationDirection(string $value): Selector
    {
        return $this->addRule('animation-direction', $value);
    }

    public function animationPlayState(string $value): Selector
    {
        return $this->addRule('animation-play-state', $value);
    }

    public function animationTimingFunction(string $value): Selector
    {
        return $this->addRule('animation-timing-function', $value);
    }

    public function backfaceVisibility(string $value): Selector
    {
        return $this
            ->addRule('-webkit-backface-visibility', $value)
            ->addRule('backface-visibility', $value);
    }

    public function color(string $color): Selector
    {
        return $this->addRule('color', $color);
    }

    public function backgroundColor(string $color): Selector
    {
        return $this->addRule('background-color', $color);
    }

    public function backgroundImage(string $url): Selector
    {
        return $this->addRule('background-image', sprintf('url("%s")', $url));
    }

    public function backgroundRepeatX(): Selector
    {
        return $this->backgroundRepeat('repeat-x');
    }

    public function backgroundRepeat(string $repeat): Selector
    {
        if (! in_array($repeat, ['repeat-y', 'repeat-x', 'no-repeat'])) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid value for background-repeat property', $repeat));
        }

        return $this->addRule('background-repeat', $repeat);
    }

    public function backgroundRepeatY(): Selector
    {
        return $this->backgroundRepeat('repeat-y');
    }

    public function backgroundNoRepeat(): Selector
    {
        return $this->backgroundRepeat('no-repeat');
    }

    public function backgroundFixed(): Selector
    {
        return $this->backgroundAttachment('fixed');
    }

    public function backgroundAttachment(string $repeat): Selector
    {
        if (! in_array($repeat, ['fixed', 'scroll'])) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid value for background-attachment property', $repeat));
        }

        return $this->addRule('background-attachment', $repeat);
    }

    public function backgroundScroll(): Selector
    {
        return $this->backgroundAttachment('scroll');
    }

    public function backgroundPosition(string $position): Selector
    {
        return $this->addRule('background-position', $position);
    }

    /**
     * @param string|string[] $values
     *
     * @return Selector
     */
    public function background($values): Selector
    {
        $values = is_array($values) ? $values : func_get_args();

        return $this->addRule('background', implode(' ', $values));
    }

    public function backgroundBlendMode(string $value): Selector
    {
        return $this->addRule('background-blend-mode', $value);
    }

    public function backgroundClip(string $value): Selector
    {
        return $this->addRule('background-clip', $value);
    }

    public function backgroundOrigin(string $value): Selector
    {
        return $this->addRule('background-origin', $value);
    }

    public function backgroundSize(string $value): Selector
    {
        return $this->addRule('background-size', $value);
    }

    public function margin(string $margins): Selector
    {
        return $this->addRule('margin', $margins);
    }

    public function marginTop(string $margin): Selector
    {
        return $this->addRule('margin-top', $margin);
    }

    public function marginRight(string $margin): Selector
    {
        return $this->addRule('margin-right', $margin);
    }

    public function marginLeft(string $margin): Selector
    {
        return $this->addRule('margin-left', $margin);
    }

    public function marginBottom(string $margin): Selector
    {
        return $this->addRule('margin-bottom', $margin);
    }

    public function padding(string $padding): Selector
    {
        return $this->addRule('padding', $padding);
    }

    public function paddingTop(string $padding): Selector
    {
        return $this->addRule('padding-top', $padding);
    }

    public function paddingRight(string $padding): Selector
    {
        return $this->addRule('padding-right', $padding);
    }

    public function paddingLeft(string $padding): Selector
    {
        return $this->addRule('padding-left', $padding);
    }

    public function paddingBottom(string $padding): Selector
    {
        return $this->addRule('padding-bottom', $padding);
    }

    public function height(string $height): Selector
    {
        return $this->addRule('height', $height);
    }

    public function maxHeight(string $height): Selector
    {
        return $this->addRule('max-height', $height);
    }

    public function minHeight(string $height): Selector
    {
        return $this->addRule('min-height', $height);
    }

    public function width(string $width): Selector
    {
        return $this->addRule('width', $width);
    }

    public function maxWidth(string $width): Selector
    {
        return $this->addRule('max-width', $width);
    }

    public function minWidth(string $width): Selector
    {
        return $this->addRule('min-width', $width);
    }

    public function font(string $value): Selector
    {
        return $this->addRule('font', $value);
    }

    public function fontStyle(string $value): Selector
    {
        return $this->addRule('font', $value);
    }

    public function fontVariant(string $value): Selector
    {
        return $this->addRule('font-variant', $value);
    }

    public function fontStretch(string $value): Selector
    {
        return $this->addRule('font-stretch', $value);
    }

    public function fontSizeAdjust(string $value): Selector
    {
        return $this->addRule('font-size-adjust', $value);
    }

    public function fontKerning(string $value): Selector
    {
        return $this->addRule('font-kerning', $value);
    }

    public function textAlign(string $value): Selector
    {
        return $this->addRule('text-align', $value);
    }

    public function textAlignLast(string $value): Selector
    {
        return $this->addRule('text-align-last', $value);
    }

    public function verticalAlign(string $value): Selector
    {
        return $this->addRule('vertical-align', $value);
    }

    public function direction(string $value): Selector
    {
        return $this->addRule('direction', $value);
    }

    public function textDecoration(string $value): Selector
    {
        return $this->addRule('text-decoration', $value);
    }

    public function textUppercase(): Selector
    {
        return $this->textTransform('uppercase');
    }

    public function textTransform(string $value): Selector
    {
        return $this->addRule('text-transform', $value);
    }

    public function textLowercase(): Selector
    {
        return $this->textTransform('lowercase');
    }

    public function textCapitalize(): Selector
    {
        return $this->textTransform('capitalize');
    }

    public function textIndent(string $value): Selector
    {
        return $this->addRule('text-indent', $value);
    }

    public function letterSpacing(string $value): Selector
    {
        return $this->addRule('letter-spacing', $value);
    }

    /**
     * @param int|string $value
     *
     * @return Selector
     */
    public function lineHeight($value): Selector
    {
        return $this->addRule('line-height', $value);
    }

    public function wordSpacing(string $value): Selector
    {
        return $this->addRule('word-spacing', $value);
    }

    public function fontFamily(string $value): Selector
    {
        return $this->addRule('font-family', $value);
    }

    public function fontSize(string $value): Selector
    {
        return $this->addRule('font-size', $value);
    }

    public function fontWeight(string $value): Selector
    {
        return $this->addRule('font-weight', $value);
    }

    public function textShadow(string $value): Selector
    {
        return $this->addRule('text-shadow', $value);
    }

    public function listStyleType(string $value): Selector
    {
        return $this->addRule('list-style-type', $value);
    }

    public function listStyleImage(string $value): Selector
    {
        return $this->addRule('list-style-image', sprintf('url("%s")', $value));
    }

    public function listStylePosition(string $value): Selector
    {
        return $this->addRule('list-style-position', $value);
    }

    public function border(string $value): Selector
    {
        return $this->addRule('border', $value);
    }

    public function borderColor(string $value): Selector
    {
        return $this->addRule('border-color', $value);
    }

    public function borderImage(string $src, ?string $options = null): Selector
    {
        $statement = sprintf('url("%s")', $src);
        if (! is_null($options)) {
            $statement .= ' ' . $options;
        }

        return $this->addRule('border-image', $statement);
    }

    public function borderImageOutset(string $value): Selector
    {
        return $this->addRule('border-image-outset', $value);
    }

    public function borderImageRepeat(string $value): Selector
    {
        return $this->addRule('border-image-repeat', $value);
    }

    public function borderImageSlice(string $value): Selector
    {
        return $this->addRule('border-image-slice', $value);
    }

    public function borderImageSource(string $value): Selector
    {
        return $this->addRule('border-image-source', $value);
    }

    public function borderImageWidth(string $value): Selector
    {
        return $this->addRule('border-image-width', $value);
    }

    public function borderLeft(string $value): Selector
    {
        return $this->addRule('border-left', $value);
    }

    public function borderRight(string $value): Selector
    {
        return $this->addRule('border-right', $value);
    }

    public function borderTop(string $value): Selector
    {
        return $this->addRule('border-top', $value);
    }

    public function borderBottom(string $value): Selector
    {
        return $this->addRule('border-bottom', $value);
    }

    public function borderRadius(string $value): Selector
    {
        return $this->addRule('border-radius', $value);
    }

    public function borderStyle(string $value): Selector
    {
        return $this->addRule('border-style', $value);
    }

    public function borderWidth(string $value): Selector
    {
        return $this->addRule('border-width', $value);
    }

    public function borderCollapse(string $value): Selector
    {
        return $this->addRule('border-collapse', $value);
    }

    public function borderSpacing(string $value): Selector
    {
        return $this->addRule('border-spacing', $value);
    }

    public function bottom(string $value): Selector
    {
        return $this->addRule('bottom', $value);
    }

    public function top(string $value): Selector
    {
        return $this->addRule('top', $value);
    }

    public function boxShadow(string $value): Selector
    {
        return $this->addRule('box-shadow', $value);
    }

    public function boxSizing(string $value): Selector
    {
        return $this->addRule('box-sizing', $value);
    }

    public function boxDecorationBreak(string $value): Selector
    {
        return $this
            ->addRule('-webkit-box-decoration-break', $value)
            ->addRule('-o-box-decoration-break', $value)
            ->addRule('box-decoration-break', $value);
    }

    public function breakAfter(string $value): Selector
    {
        return $this->addRule('break-after', $value);
    }

    public function breakBefore(string $value): Selector
    {
        return $this->addRule('break-before', $value);
    }

    public function breakInside(string $value): Selector
    {
        return $this->addRule('break-inside', $value);
    }

    public function captionSide(string $value): Selector
    {
        return $this->addRule('caption-side', $value);
    }

    public function columnCount(string $value): Selector
    {
        return $this->addRule('column-count', $value);
    }

    public function columnFill(string $value): Selector
    {
        return $this->addRule('column-fill', $value);
    }

    public function columnGap(string $value): Selector
    {
        return $this->addRule('column-gap', $value);
    }

    public function columnRule(string $value): Selector
    {
        return $this->addRule('column-rule', $value);
    }

    public function columnRuleWidth(string $value): Selector
    {
        return $this->addRule('column-rule-width', $value);
    }

    public function columnRuleStyle(string $value): Selector
    {
        return $this->addRule('column-rule-style', $value);
    }

    public function columnRuleColor(string $value): Selector
    {
        return $this->addRule('column-rule-color', $value);
    }

    public function columnSpan(string $value): Selector
    {
        return $this->addRule('column-span', $value);
    }

    public function columnWidth(string $value): Selector
    {
        return $this->addRule('column-width', $value);
    }

    public function columns(string $value): Selector
    {
        return $this->addRule('columns', $value);
    }

    public function content(string $value): Selector
    {
        return $this->addRule('content', sprintf('"%s"', $value));
    }

    public function counterReset(string $value): Selector
    {
        return $this->addRule('counter-reset', $value);
    }

    public function counterIncrement(string $value): Selector
    {
        return $this->addRule('counter-increment', $value);
    }

    public function cursor(string $value): Selector
    {
        return $this->addRule('cursor', $value);
    }

    public function filter(string $value): Selector
    {
        return $this->addRule('filter', $value);
    }

    public function flex(string $value): Selector
    {
        return $this
            ->addRule('-ms-flex', $value)
            ->addRule('flex', $value);
    }

    public function flexBasis(string $value): Selector
    {
        return $this->addRule('flex-basis', $value);
    }

    public function flexDirection(string $value): Selector
    {
        return $this->addRule('flex-direction', $value);
    }

    public function flexFlow(string $value): Selector
    {
        return $this->addRule('flex-flow', $value);
    }

    public function gap(string $value): Selector
    {
        return $this->addRule('gap', $value);
    }

    public function grid(string $value): Selector
    {
        return $this->addRule('grid', $value);
    }

    public function gridArea(string $value): Selector
    {
        return $this->addRule('grid-area', $value);
    }

    public function gridAutoColumns(string $value): Selector
    {
        return $this->addRule('grid-auto-columns', $value);
    }
    public function gridAutoFlow(string $value): Selector
    {
        return $this->addRule('grid-auto-flow', $value);
    }

    public function gridAutoRows(string $value): Selector
    {
        return $this->addRule('grid-auto-rows', $value);
    }

    public function gridColumn(string $value): Selector
    {
        return $this->addRule('grid-column', $value);
    }

    public function gridColumnStart(string $value): Selector
    {
        return $this->addRule('grid-column-start', $value);
    }

    public function gridColumnGap(string $value): Selector
    {
        return $this->addRule('grid-column-gap', $value);
    }

    public function gridColumnEnd(string $value): Selector
    {
        return $this->addRule('grid-column-end', $value);
    }

    public function gridGap(string $value): Selector
    {
        return $this->addRule('grid-gap', $value);
    }

    public function gridRow(string $value): Selector
    {
        return $this->addRule('grid-row', $value);
    }

    public function gridRowEnd(string $value): Selector
    {
        return $this->addRule('grid-row-end', $value);
    }

    public function gridRowGap(string $value): Selector
    {
        return $this->addRule('grid-row-gap', $value);
    }

    public function gridRowStart(string $value): Selector
    {
        return $this->addRule('grid-row-start', $value);
    }

    public function gridTemplate(string $value): Selector
    {
        return $this->addRule('grid-template', $value);
    }

    public function gridTemplateArea(string $value): Selector
    {
        return $this->addRule('grid-template-area', $value);
    }

    public function gridTemplateColumns(string $value): Selector
    {
        return $this->addRule('grid-template-columns', $value);
    }

    public function gridTemplateRows(string $value): Selector
    {
        return $this->addRule('grid-template-rows', $value);
    }

    public function overflow(string $value): Selector
    {
        return $this->addRule('overflow', $value);
    }

    public function overflowX(string $value): Selector
    {
        return $this->addRule('overflow-x', $value);
    }

    public function overflowY(string $value): Selector
    {
        return $this->addRule('overflow-y', $value);
    }

    public function overflowWrap(string $value): Selector
    {
        return $this->addRule('overflow-wrap', $value);
    }

    public function order(string $value): Selector
    {
        return $this->addRule('order', $value);
    }

    public function opacity(string $value): Selector
    {
        return $this->addRule('opacity', $value);
    }

    public function rowGap(string $value): Selector
    {
        return $this->addRule('row-gap', $value);
    }

    public function right(string $value): Selector
    {
        return $this->addRule('right', $value);
    }

    public function left(string $value): Selector
    {
        return $this->addRule('left', $value);
    }

    public function position(string $value): Selector
    {
        return $this->addRule('position', $value);
    }

    public function zIndex(string $value): Selector
    {
        return $this->addRule('z-index', $value);
    }
}
