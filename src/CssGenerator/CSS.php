<?php

namespace Devly\CssGenerator;

use Devly\CssGenerator\Concerns\HasRules;
use InvalidArgumentException;
use RuntimeException;

class CSS
{
    use HasRules;

    protected ?string $charset;
    /**
     * @var Import[]
     */
    protected array $imports = [];

    public static string $indent;

    protected bool $minify;
    /**
     * @var FontFace[]
     */
    protected array $font_face;

    /**
     * @param Selector[]|CSS $rules
     * @param string[]|int[] $options
     */
    public function __construct($rules = [], array $options = [])
    {
        $this->indent($options['indent'] ?? 4);

        $this->minify($options['minify'] ?? false);

        $rules = $rules instanceof CSS ? $rules->getRules() : $rules;
        if (!is_array($rules)) {
            throw new InvalidArgumentException(sprintf(
                '%1$s constructor parameter must be an array or an instance of %1$s.',
                self::class
            ));
        }

        foreach ($rules as $rule) {
            $this->rules[] = $rule;
        }
    }

    /**
     * Create instance of CSS class.
     *
     * @param Selector[]|CSS $rules
     * @param array<string, int|string> $options
     *
     * @return CSS
     */
    public static function new($rules = [], array $options = []): CSS
    {
        return new self($rules, $options);
    }

    public function charset(string $charset): CSS
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @param string $url
     * @param string|string[] $media_queries
     *
     * @return Import
     */
    public function import(string $url, $media_queries = []): Import
    {
        return $this->imports[] = new Import($url, $media_queries, $this);
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

    /**
     * @param string $feature
     * @param CSS|Media|array|null $rules
     *
     * @return Supports
     */
    public function supports(string $feature, $rules = null): Supports
    {
        return $this->rules[] = new Supports($feature, $rules ?? [], $this);
    }

    public function compile(?bool $minify = null): string
    {
        $minify     = is_null($minify) ? $this->minify : $minify;
        $line_break = $minify ? '' : "\n";
        $output     = '';

        if (isset($this->charset)) {
            $output .= sprintf('@charset "%s";%s', $this->charset, $line_break);
        }

        if (!empty($this->imports)) {
            foreach ($this->imports as $import_statement) {
                $output .= $import_statement . $line_break;
            }

            $output .= $line_break;
        } else {
            if (!empty($output)) {
                $output .= $line_break;
            }
        }

        if (isset($this->font_face)) {
            foreach ($this->font_face as $font_face) {
                $output .= (empty($output) ? '' : $line_break) . $font_face->css($minify) . $line_break;
            }
        }

        $rules = '';
        if (isset($this->rules)) {
            foreach ($this->rules as $selector) {
                $rules .= (empty($rules) ? '' : $line_break) . $selector->css($minify) . $line_break;
            }
        }

        return $output . $rules;
    }

    /**
     * Save generated CSS into a file.
     *
     * @param string $path
     * @param bool $override
     * @param bool $mkdir
     *
     * @return bool
     */
    public function save(string $path, ?bool $minify = null, bool $override = false, bool $mkdir = false): bool
    {
        $file_exists = file_exists($path);
        if ($file_exists && !$override) {
            return false;
        }

        $path_info = pathinfo($path);

        if (!is_dir($path_info['dirname'])) {
            if (!$mkdir) {
                throw new RuntimeException(sprintf('Path "%s" must be present in order to save a css file.', $path_info['dirname']));
            }

            mkdir($path_info['dirname'], 0777, true);
        }

        $file    = fopen($path, 'w');
        $results = fwrite($file, $this->compile($minify)) !== false;

        fclose($file);

        return $results;
    }

    public function __toString()
    {
        return $this->compile();
    }

    /**
     * @return Selector[]|Media[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param int $indent
     *
     * @return void
     */
    protected function indent(int $indent): void
    {
        $_indent = '';
        for ($i = 0; $i < $indent; $i++) {
            $_indent .= ' ';
        }

        self::$indent = $_indent;
    }

    /**
     * @param bool $minify
     *
     * @return CSS
     */
    public function minify(bool $minify = true): CSS
    {
        $this->minify = $minify;

        return $this;
    }

    public function fontFace(): FontFace
    {
        return $this->font_face[] = new FontFace($this);
    }
}
