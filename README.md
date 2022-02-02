# CSS Generator

Generate CSS on the fly using PHP.

## Usage

```php
use Devly\CssGenerator\CSS;
$options = [
    'indent' => 4, // Default indent: 4 spaces
    'minify' => false, // Default false
];
$css = CSS::new([], $options);
$css->charset('utf-8');
$css->import('path/to/imported.css');
$css->import('path/to/second-imported.css')
    ->supports('display: block')
    ->media('screen');
$css->selector('body')
    ->fontFamily('Arial, sans-serif')
    ->fontSize('16px')
    ->lineHeight(1.5);
$css->media('screen and (min-width: 768px)')
    ->selector('body')
    ->fontSize('18px')
    ->endMedia();

echo $css->compile();
```

### Output

```css
@charset "utf-8";
@import "path/to/imported.css";
@import "path/to/second-imported.css" supports(display: block) screen;

body {
    font-family: Arial, sans-serif;
    font-size: 16px;
    line-height: 1.5;
}

@media screen and (min-width: 768px) {
    body {
        font-size: 18px;
    }
}
```

### With minify enabled

```css
@charset "utf-8";@import "path/to/imported.css";@import "path/to/second-imported.css" supports(display: block) screen;body{font-family:Arial, sans-serif;font-size:16px;line-height:1.5;}@media screen and (min-width: 768px){body{font-size:18px;}}
```

### Save to file

Instead of outputting the compiled css, it can be saved to a file:

```php
$minify = true; // Will override minify option if already set
$override = true; // Override if file exists
$mkdir    = true; // Creates directory recursively if not already exists
$css->save('path/to/compiled.css', $minify, $override, $mkdir);
```

### Import rules from another instance of CSS class

```php
use Devly\CssGenerator\CSS;

$imported = CSS::new()->selector('body')->backgroundColor('#000000');

$css = CSS::new($imported)
```

In addition, it is possible to import CSS into media() statement:

```php
use Devly\CssGenerator\CSS;

$mobile_css = CSS::new()->selector('body')->backgroundColor('#000000');

$css = CSS::new()->media('screen and (max-width: 768px)', $mobile_css);
```

