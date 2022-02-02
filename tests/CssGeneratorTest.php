<?php

namespace Devly\CssGenerator\Tests;

use Devly\CssGenerator\CSS;
use PHPUnit\Framework\TestCase;

class CssGeneratorTest extends TestCase
{
    public function testCreateInstance(): void
    {
        $css = CSS::new();
        $css->charset('UTF-8');

        $expected = '@charset "UTF-8";';
        $this->assertEquals($expected, trim($css->compile()));
    }

    public function testImport(): void
    {
        $css = CSS::new();
        $css->import('path/to/imported.css');
        $css->import('path/to/second-imported.css', 'screen and (max-width: 768px)');
        $css->import('path/to/third-imported.css')->supports('display: block');
        $css->import('path/to/fourth-imported.css')->supports('display: block')->media('print', 'screen');

        $expected = "@import \"path/to/imported.css\";\n@import \"path/to/second-imported.css\" screen and (max-width: 768px);\n@import \"path/to/third-imported.css\" supports(display: block);\n@import \"path/to/fourth-imported.css\" supports(display: block) print, screen;";

        $this->assertEquals($expected, trim($css->compile()));
    }

    public function testSelector(): void
    {
        $css = CSS::new()
            ->selector(['html', 'body'])
                ->display('block')
                ->backgroundColor('#000')
            ->endSelector()
            ->selector('h1')
                ->lineHeight(1)
            ->endSelector();

        $expected = "html,\nbody {\n    display: block;\n    background-color: #000;\n}\n\nh1 {\n    line-height: 1;\n}";
        $this->assertEquals($expected, trim($css->compile()));
    }

    public function testMediaQuery(): void
    {
        $css = CSS::new();
        $css->media('(max-width: 768px)')
            ->selector(['html', 'body'])
                ->display('block')
            ->selector('h1')
                ->lineHeight(1);

        $expected = "@media (max-width: 768px) {\n    html,\n    body {\n        display: block;\n    }\n\n    h1 {\n        line-height: 1;\n    }\n}";
        $this->assertEquals($expected, trim($css->compile()));
    }

    public function testSaveToFile(): void
    {
        $css = CSS::new()
            ->selector(['html', 'body'])
                ->display('block')
                ->backgroundColor('#000000')
                ->fontSize('1.6rem')
            ->endSelector()
            ->selector('h1')
                ->lineHeight(1)
            ->endSelector();

        $css = CSS::new($css, ['indent' => 2])
            ->charset('utf-8')
            ->import('./style.css')->media('screen', 'print')->endImport()
            ->media('(max-width: 768px)')
                ->selector(['html', 'body'])
                    ->fontSize('1rem')
            ->endMedia();


        $this->assertTrue($css->save(__DIR__ . '/generated/css/style.css', true, true));
    }

    public function testFontFace(): void
    {
        $css = CSS::new()
            ->fontFace()
                ->fontFamily('Arial')
                ->src("url('path/to/arial.ttf')")
                ->fontWeight('bold');

        $expected = "@font-face {\n    font-family: Arial;\n    src: url('path/to/arial.ttf');\n    font-weight: bold;\n}";
        $this->assertEquals($expected, trim($css->compile()));
    }
}
