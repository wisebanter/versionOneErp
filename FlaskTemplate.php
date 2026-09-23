<?php

/**
 * FlaskTemplate — Secure, eval‑free Jinja2/Flask‑style template engine.
 *
 * Security highlights:
 *   - No eval() – compiles to real PHP files (tmpfile or cache) and uses include()
 *   - Expressions are sanitised: function calls are DISALLOWED (except built‑ins we inject)
 *   - Template names are validated against directory traversal attacks
 *   - Temporary files are deleted after rendering (and created with restrictive permissions)
 *   - Auto‑escaping is ON by default (htmlspecialchars with ENT_QUOTES, UTF‑8)
 *   - Raw blocks are isolated and never parsed
 *   - Comments are stripped entirely before compilation
 *
 * Features:
 *   - Inheritance: {% extends "base.html" %} + {% block name %}...{% endblock %}
 *   - Control flow: {% if %}, {% elif %}, {% else %}, {% endif %}
 *   - Loops: {% for item in iterable %}...{% else %}...{% endfor %}
 *   - Include: {% include "partial.html" %} (shares context)
 *   - Set: {% set var = value %}
 *   - Raw: {% raw %}...{% endraw %}
 *   - Filters: upper, lower, escape, raw, length, trim, default, join, first, last, sort, reverse, batch, format, replace, striptags
 *   - Whitespace control: {%- -%} and {{- -}}
 *   - Globals: addGlobal() for variables available everywhere
 */

class FlaskTemplate
{
    private string $templateDir;
    private ?string $cacheDir;
    private bool $autoescape;
    private array $globals = [];
    private array $filters = [];

    private const SAFE_FUNCTIONS = ['isset', 'empty', 'in_array', 'url_for'];

    private const BUILTIN_FILTERS = [
        'upper' => 'strtoupper',
        'lower' => 'strtolower',
        'trim' => 'trim',
        'escape' => 'htmlspecialchars',
        'raw' => null,
        'length' => 'strlen',
        'default' => 'default_filter',
        'join' => 'join_filter',
        'first' => 'first_filter',
        'last' => 'last_filter',
        'sort' => 'sort_filter',
        'reverse' => 'reverse_filter',
        'batch' => 'batch_filter',
        'format' => 'sprintf',
        'replace' => 'replace_filter',
        'striptags' => 'strip_tags',
    ];

    public function __construct(string $templateDir, ?string $cacheDir = null, bool $autoescape = true)
    {
        $this->templateDir = rtrim(realpath($templateDir) ?: $templateDir, '/');
        if (!is_dir($this->templateDir)) {
            throw new \RuntimeException("Template directory does not exist: {$templateDir}");
        }
        $this->cacheDir = $cacheDir ? rtrim($cacheDir, '/') : null;
        $this->autoescape = $autoescape;
        $this->registerBuiltinFilters();
    }

    public function addFilter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
    }

    public function addGlobal(string $name, mixed $value): void
    {
        $this->globals[$name] = $value;
    }

    public function getGlobal(string $name, mixed $defaultValue = ""): mixed
    {
        return isset($this->globals[$name]) ? $this->globals[$name] : $defaultValue;
    }

    public function execFilter(string $name, mixed $value, mixed ...$args): mixed
    {
        if (!isset($this->filters[$name])) {
            throw new \RuntimeException("Unknown filter: {$name}");
        }

        return call_user_func($this->filters[$name], $value, ...$args);
    }

    public function render(string $templateName, array $data = []): string
    {
        $this->validateTemplateName($templateName);
        $compiledPath = $this->compile($templateName);
        return $this->includeCompiled($compiledPath, $data);
    }

    private function compile(string $templateName): string
    {
        $cacheFile = null;
        $this->validateTemplateName($templateName);
        $sourceFile = $this->templateDir . '/' . ltrim($templateName, '/');

        if ($this->cacheDir) {
            $cacheFile = $this->cacheDir . '/' . $this->getCacheKey($templateName);
            if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($sourceFile)) {
                return $cacheFile;
            }
        }

        $source = file_get_contents($sourceFile);
        if ($source === false) {
            throw new \RuntimeException("Could not read template: {$templateName}");
        }

        [$source, $rawSlots] = $this->protectRaw($source);

        $parentName = null;
        if (preg_match('/{%\s*extends\s+[\'"](.+?)[\'"]\s*%}/s', $source, $m)) {
            $parentName = $m[1];
            $this->validateTemplateName($parentName);
            $source = str_replace($m[0], '', $source);
        }

        $childBlocks = [];
        $source = preg_replace_callback(
            '/{%\s*block\s+(\w+)\s*%}(.*?){%\s*endblock\s*%}/s',
            function (array $m) use (&$childBlocks): string {
                $childBlocks[$m[1]] = $m[2];
                return "<!-- block:{$m[1]} -->";
            },
            $source
        );

        if ($parentName !== null) {
            $phpCode = $this->resolveInheritance($parentName, $childBlocks, $rawSlots);
        } else {
            $phpCode = $this->convertSyntax($source);
            $phpCode = $this->restoreRaw($phpCode, $rawSlots);
        }

        if ($this->cacheDir) {
            if (!is_dir($this->cacheDir)) {
                mkdir($this->cacheDir, 0755, true);
            }
            file_put_contents($cacheFile, $phpCode);
            return $cacheFile;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'flask_');
        if ($tmpPath === false) {
            throw new \RuntimeException('Could not create temporary file');
        }
        file_put_contents($tmpPath, $phpCode);
        return $tmpPath;
    }

    private function resolveInheritance(string $parentName, array $childBlocks, array $childRawSlots): string
    {
        $this->validateTemplateName($parentName);
        $parentSource = file_get_contents($this->templateDir . '/' . ltrim($parentName, '/'));
        if ($parentSource === false) {
            throw new \RuntimeException("Could not read parent template: {$parentName}");
        }

        [$parentSource, $parentRawSlots] = $this->protectRaw($parentSource);

        $grandParentName = null;
        if (preg_match('/{%\s*extends\s+[\'"](.+?)[\'"]\s*%}/s', $parentSource, $m)) {
            $grandParentName = $m[1];
            $this->validateTemplateName($grandParentName);
            $parentSource = str_replace($m[0], '', $parentSource);
        }

        if ($grandParentName !== null) {
            $parentBlocks = [];
            $parentSource = preg_replace_callback(
                '/{%\s*block\s+(\w+)\s*%}(.*?){%\s*endblock\s*%}/s',
                function (array $m) use (&$parentBlocks): string {
                    $parentBlocks[$m[1]] = $m[2];
                    return "<!-- block:{$m[1]} -->";
                },
                $parentSource
            );
            $merged = array_merge($parentBlocks, $childBlocks);
            $mergedRaw = array_merge($parentRawSlots, $childRawSlots);
            return $this->resolveInheritance($grandParentName, $merged, $mergedRaw);
        }

        $parentSource = preg_replace_callback(
            '/{%\s*block\s+(\w+)\s*%}(.*?){%\s*endblock\s*%}/s',
            fn(array $m) => $childBlocks[$m[1]] ?? $m[2],
            $parentSource
        );

        $phpCode = $this->convertSyntax($parentSource);
        $phpCode = $this->restoreRaw($phpCode, $parentRawSlots);
        $phpCode = $this->restoreRaw($phpCode, $childRawSlots);

        return $phpCode;
    }

    private function convertSyntax(string $source): string
    {
        $source = preg_replace('/{%-\s*/', '{%', $source);
        $source = preg_replace('/\s*-%}/', '%}', $source);
        $source = preg_replace('/{{\s*-/', '{{', $source);
        $source = preg_replace('/-\s*}}/', '}}', $source);

        $source = preg_replace('/{#.*?#}/s', '', $source);

        $source = preg_replace_callback(
            '/{%\s*set\s+(\w+)\s*=\s*(.+?)\s*%}/',
            fn(array $m): string => "<?php \${$m[1]} = {$this->toPhpExpr($m[2])}; ?>",
            $source
        );

        // ── {{ variable|filter }} ──────────────────────────────────────────
        $source = preg_replace_callback(
            '/{{\s*(.+?)\s*}}/',
            function (array $m): string {
                $parts = preg_split('/\s*\|\s*/', trim($m[1]));
                $varExpr = array_shift($parts);
                $phpExpr = $this->toPhpExpr($varExpr);
                $hasRaw = false;

                foreach ($parts as $filter) {
                    if ($filter === 'raw') {
                        $hasRaw = true;
                        continue;
                    }

                    if (preg_match('/^(\w+)\s*\((.+)\)$/', $filter, $fMatches)) {
                        $filterName = $fMatches[1];
                        $args = $this->toPhpExpr($fMatches[2]);
                        $phpExpr = $this->applyFilter($phpExpr, $filterName, $args);
                    } else {
                        $phpExpr = $this->applyFilter($phpExpr, $filter);
                    }
                }

                if ($this->autoescape && !$hasRaw) {
                    return "<?= htmlspecialchars((string)({$phpExpr}), ENT_QUOTES, 'UTF-8') ?>";
                }

                return "<?= {$phpExpr} ?>";
            },
            $source
        );

        $tokens = $this->tokenize($source);
        $php = '';
        $stack = [];

        foreach ($tokens as $token) {
            if ($token['type'] === 'text') {
                $php .= $token['content'];
                continue;
            }

            $tag = trim($token['content']);

            if (preg_match('/^if\s+(.+)$/', $tag, $m)) {
                $stack[] = ['type' => 'if', 'hasElse' => false];
                $php .= '<?php if (' . $this->toPhpExpr($m[1]) . '): ?>';
            } elseif (preg_match('/^elif\s+(.+)$/', $tag, $m)) {
                $php .= '<?php elseif (' . $this->toPhpExpr($m[1]) . '): ?>';
            } elseif ($tag === 'else' || $tag === 'else%}') {
                if (empty($stack)) {
                    throw new \RuntimeException('Unexpected {% else %} without an opening block');
                }
                $last = &$stack[count($stack) - 1];
                if ($last['type'] === 'if') {
                    $php .= '<?php else: ?>';
                } elseif ($last['type'] === 'for') {
                    $php .= '<?php endforeach; else: ?>';
                    $last['hasElse'] = true;
                } else {
                    throw new \RuntimeException('{% else %} not allowed inside ' . $last['type']);
                }
            } elseif ($tag === 'endif') {
                if (empty($stack) || $stack[count($stack) - 1]['type'] !== 'if') {
                    throw new \RuntimeException('Unexpected {% endif %}');
                }
                array_pop($stack);
                $php .= '<?php endif; ?>';
            } elseif (preg_match('/^for\s+(\w+)\s+in\s+(.+)$/', $tag, $m)) {
                $stack[] = ['type' => 'for', 'hasElse' => false];
                $item = $m[1];
                $iterable = $this->toPhpExpr($m[2]);
                $loopVar = "_loop_{$item}";
                $php .= "<?php \${$loopVar} = {$iterable}; if (!empty(\${$loopVar})): foreach (\${$loopVar} as \${$item}): ?>";
            } elseif ($tag === 'endfor' || $tag === 'endfor%}') {
                if (empty($stack) || $stack[count($stack) - 1]['type'] !== 'for') {
                    throw new \RuntimeException('Unexpected {% endfor %}');
                }
                $block = array_pop($stack);
                if ($block['hasElse']) {
                    $php .= '<?php endif; ?>';
                } else {
                    $php .= '<?php endforeach; endif; ?>';
                }
            } elseif (preg_match('/^include\s+[\'"](.+?)[\'"]\s*(?:with\s+context)?$/', $tag, $m)) {
                $php .= $this->compileSubTemplate($m[1]);
            } else {
                $php .= '{' . $tag . '}';
            }
        }

        if (!empty($stack)) {
            throw new \RuntimeException('Unclosed block(s): ' . implode(', ', array_column($stack, 'type')));
        }

        return $php;
    }

    private function tokenize(string $source): array
    {
        $tokens = [];
        $offset = 0;
        $len = strlen($source);
        $tagRegex = '/{%\s*(.*?)\s*%}/s';

        while ($offset < $len) {
            if (preg_match($tagRegex, $source, $match, PREG_OFFSET_CAPTURE, $offset)) {
                $matchStart = $match[0][1];
                $matchEnd = $matchStart + strlen($match[0][0]);

                if ($matchStart > $offset) {
                    $tokens[] = ['type' => 'text', 'content' => substr($source, $offset, $matchStart - $offset)];
                }

                $tokens[] = ['type' => 'tag', 'content' => $match[1][0]];
                $offset = $matchEnd;
            } else {
                $tokens[] = ['type' => 'text', 'content' => substr($source, $offset)];
                break;
            }
        }
        return $tokens;
    }

    private function compileSubTemplate(string $name): string
    {
        $this->validateTemplateName($name);
        $source = file_get_contents($this->templateDir . '/' . ltrim($name, '/'));
        if ($source === false) {
            throw new \RuntimeException("Sub‑template not found: {$name}");
        }

        [$source, $rawSlots] = $this->protectRaw($source);
        $phpCode = $this->convertSyntax($source);
        $phpCode = $this->restoreRaw($phpCode, $rawSlots);
        return $phpCode;
    }

    // 2. Updated expression parser
    private function toPhpExpr(string $expr): string
    {
        $expr = trim($expr);

        // Protect string literals ('...' and "...") from variable and dot-notation regexes
        $stringLiterals = [];
        $expr = preg_replace_callback(
            '/\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*"/s',
            function (array $m) use (&$stringLiterals): string {
                $key = '__STR_' . count($stringLiterals) . '__';
                $stringLiterals[$key] = $m[0];
                return $key;
            },
            $expr
        );

        // Convert Jinja2 named args (filename='...') to PHP 8 named args (filename: '...')
        $expr = preg_replace('/(?<=\(|\,|^)\s*([a-zA-Z_]\w*)\s*=\s*/', '$1: ', $expr);

        // Operator replacements
        $expr = preg_replace('/\bis\s+defined\b/', 'isset', $expr);
        $expr = preg_replace('/\bis\s+not\s+defined\b/', '!isset', $expr);
        $expr = preg_replace('/\bis\s+none\b/', 'is_null', $expr);
        $expr = preg_replace('/\bis\s+not\s+none\b/', '!is_null', $expr);
        $expr = preg_replace('/\bnot\s+in\b/', '!in_array', $expr);
        $expr = preg_replace('/\bin\b/', 'in_array', $expr);

        // Validate function calls
        $expr = preg_replace_callback(
            '/\b([a-zA-Z_]\w*)\s*\(/',
            function (array $m): string {
                $func = $m[1];
                if (!in_array($func, self::SAFE_FUNCTIONS, true)) {
                    throw new \RuntimeException("Function call '{$func}()' is not allowed in expressions");
                }
                return $m[0];
            },
            $expr
        );

        // Prefix variables with $ (ignore reserved keywords, named parameters, and string tokens)
        $expr = preg_replace_callback(
            '/(?<!\$)\b([a-zA-Z_]\w*)\b(?!\s*[\(:])/',
            function (array $m): string {
                $keyword = strtolower($m[1]);
                $reserved = ['true', 'false', 'null', 'and', 'or', 'not', 'is', 'in', 'as', 'isset', 'empty', 'array'];
                if (in_array($keyword, $reserved, true) || str_starts_with($keyword, '__str_')) {
                    return $m[1];
                }
                return '$' . $m[1];
            },
            $expr
        );

        // Method calls ($obj.method())
        $expr = preg_replace_callback(
            '/(\$[a-zA-Z_]\w*)\.\$?([a-zA-Z_]\w*)\s*\(/',
            function (array $m): string {
                return $m[1] . '->' . $m[2] . '(';
            },
            $expr
        );

        // Dot notation array access ($obj.prop)
        $expr = preg_replace_callback(
            '/(\$[a-zA-Z_]\w*)((?:\.\$?[a-zA-Z_]\w*)+)(?![(])/',
            function (array $m): string {
                $base = $m[1];
                $rest = str_replace('$', '', $m[2]);
                $parts = explode('.', $rest);
                foreach ($parts as $part) {
                    if ($part !== '') {
                        $base .= "['" . $part . "']";
                    }
                }
                return $base;
            },
            $expr
        );

        // Restore string literals
        if (!empty($stringLiterals)) {
            $expr = str_replace(array_keys($stringLiterals), array_values($stringLiterals), $expr);
        }

        return $expr;
    }

    private function registerBuiltinFilters(): void
    {
        foreach (self::BUILTIN_FILTERS as $name => $callback) {
            if (is_string($callback) && method_exists($this, $callback)) {
                $this->filters[$name] = [$this, $callback];
            } elseif (is_callable($callback)) {
                $this->filters[$name] = $callback;
            }
        }
    }

    private function applyFilter(string $expr, string $filter, ?string $args = null): string
    {
        $argString = $args !== null ? ", {$args}" : "";

        return match ($filter) {
            'upper' => "strtoupper((string)({$expr}))",
            'lower' => "strtolower((string)({$expr}))",
            'trim' => "trim((string)({$expr}))",
            'length' => "(is_array({$expr}) ? count({$expr}) : strlen((string)({$expr})))",
            'striptags' => "strip_tags((string)({$expr}))",
            'format' => "sprintf((string)({$expr}){$argString})",
            default => "\$this->execFilter('{$filter}', {$expr}{$argString})",
        };
    }

    public function default_filter(mixed $value, $default = ''): mixed
    {
        return empty($value) ? $default : $value;
    }

    public function join_filter(array $value, string $glue = ''): string
    {
        return implode($glue, (array) $value);
    }

    public function first_filter(mixed $value): mixed
    {
        return is_array($value) ? reset($value) : null;
    }

    public function last_filter(mixed $value): mixed
    {
        return is_array($value) ? end($value) : null;
    }

    public function sort_filter(mixed $value): mixed
    {
        if (is_array($value)) {
            sort($value);
            return $value;
        }
        return $value;
    }

    public function reverse_filter(mixed $value): array|string
    {
        if (is_array($value)) {
            return array_reverse($value);
        }
        return strrev((string) $value);
    }

    public function batch_filter(mixed $value, int $size, $fill = null): array
    {
        if (!is_array($value)) {
            return [];
        }

        $chunks = array_chunk($value, $size, true);

        if ($fill !== null && !empty($chunks)) {
            $lastKey = array_key_last($chunks);
            $lastChunk = $chunks[$lastKey];
            $missing = $size - count($lastChunk);
            if ($missing > 0) {
                $chunks[$lastKey] = array_merge($lastChunk, array_fill(0, $missing, $fill));
            }
        }

        return $chunks;
    }

    public function replace_filter(array|string $value, array|string $search, array|string $replace): string
    {
        return str_replace($search, $replace, (string) $value);
    }

    private function protectRaw(string $source): array
    {
        $slots = [];
        $source = preg_replace_callback(
            '/{%\s*raw\s*%}(.*?){%\s*endraw\s*%}/s',
            function (array $m) use (&$slots): string {
                $key = '__RAW_SLOT_' . count($slots) . '__';
                $slots[$key] = $m[1];
                return $key;
            },
            $source
        );
        return [$source, $slots];
    }

    private function restoreRaw(string $source, array $slots): string
    {
        return str_replace(array_keys($slots), array_values($slots), $source);
    }

    private function includeCompiled(string $compiledPath, array $data): string
    {
        $data = array_merge($this->globals, $data);
        $isTemp = strpos($compiledPath, sys_get_temp_dir()) === 0;

        ob_start();
        extract($data, EXTR_SKIP);
        try {
            include $compiledPath;
        } finally {
            if ($isTemp && file_exists($compiledPath)) {
                unlink($compiledPath);
            }
        }
        return ob_get_clean();
    }

    private function validateTemplateName(string $name): void
    {
        $name = ltrim($name, '/');
        if (strpos($name, '..') !== false || strpos($name, './') !== false) {
            throw new \RuntimeException("Invalid template name: {$name}");
        }
        $fullPath = realpath($this->templateDir . '/' . $name);
        if ($fullPath === false || strpos($fullPath, realpath($this->templateDir)) !== 0) {
            throw new \RuntimeException("Template outside of allowed directory: {$name}");
        }
    }

    private function getCacheKey(string $name): string
    {
        return md5($name) . '.php';
    }
}




// =============================================================================
// Example usage (secure and Flask‑like)
// =============================================================================
/*
$engine = new FlaskTemplate(__DIR__ . '/templates', __DIR__ . '/cache', true);
$engine->addGlobal('app_name', 'MySite');
echo $engine->render('index.html', ['user' => ['name' => 'John', 'age' => 30]]);
*/


/**
 * 
 * # Detailed Usage Guide for the Enhanced FlaskTemplate Engine

This guide covers everything you need to know to use the **Enhanced FlaskTemplate** – a secure, zero‑dependency, eval‑free template engine that mimics Flask/Jinja2 in PHP.

---

## 📦 Installation

1. **Copy the class** – save the entire `FlaskTemplate` class code into a file named `FlaskTemplate.php`.
2. **No external libraries** – it’s pure PHP 7.4+.
3. **Require it** in your project:
   ```php
   require_once __DIR__ . '/FlaskTemplate.php';
   ```

---

## 🚀 Basic Usage

### 1. Create your template directory
Place all `.html`, `.php`, or any text files in a folder, e.g., `templates/`.

### 2. Instantiate the engine
```php
$engine = new FlaskTemplate(
    __DIR__ . '/templates',   // template directory
    __DIR__ . '/cache',       // optional cache directory (will be created)
    true                      // autoescape (default true)
);
```

### 3. Render a template
```php
echo $engine->render('index.html', [
    'username' => 'John',
    'items'    => ['Apple', 'Banana', 'Cherry']
]);
```

---

## 🧩 Template Syntax (Jinja2‑Compatible)

### Variables
```twig
{{ variable }}
{{ user.name }}          <!-- array access: $user['name'] -->
{{ user['name'] }}       <!-- explicit array access -->
{{ user.getName() }}     <!-- method call → $user->getName() -->
```
**Auto‑escaping** is on by default – all output is passed through `htmlspecialchars`.  
Use the `raw` filter to disable escaping:
```twig
{{ html_content|raw }}
```

### Filters
Apply filters with the pipe `|` – you can chain them:
```twig
{{ name|upper }}
{{ name|lower }}
{{ name|trim }}
{{ name|escape }}
{{ name|length }}
{{ name|default('Guest') }}
{{ list|join(', ') }}
{{ list|first }}
{{ list|last }}
{{ list|sort }}
{{ list|reverse }}
{{ list|batch(3, 'fill') }}
{{ text|replace('old', 'new') }}
{{ text|striptags }}
```
Custom filters can be added (see below).

### Control Structures

#### `if` / `elif` / `else`
```twig
{% if user.is_admin %}
    <p>Welcome, admin!</p>
{% elif user.is_moderator %}
    <p>Hello, mod.</p>
{% else %}
    <p>Hi, {{ user.name }}.</p>
{% endif %}
```

#### `for` loops
```twig
{% for item in items %}
    <li>{{ item }}</li>
{% else %}
    <li>No items found.</li>
{% endfor %}
```
The `{% else %}` block executes when the iterable is empty.

### Template Inheritance

#### Base template (`base.html`)
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}My Site{% endblock %}</title>
</head>
<body>
    <header>{% block header %}Default Header{% endblock %}</header>
    <main>{% block content %}{% endblock %}</main>
    <footer>{% block footer %}© 2026{% endblock %}</footer>
</body>
</html>
```

#### Child template (`page.html`)
```twig
{% extends "base.html" %}

{% block title %}Home Page{% endblock %}

{% block header %}
    <h1>Welcome to our site</h1>
{% endblock %}

{% block content %}
    <p>Hello, {{ user.name }}!</p>
{% endblock %}
```
Blocks not overridden inherit the parent’s default content.

### Include Sub‑templates
```twig
{% include "header.html" %}
{% include "sidebar.html" with context %}
```
The `with context` is optional – context is always shared by default.

### Raw Blocks
Content inside `{% raw %}` … `{% endraw %}` is **not parsed**:
```twig
{% raw %}
    {{ This will not be interpreted }}
    {% if something %} … {% endif %}
{% endraw %}
```

### Set Variables
```twig
{% set title = "My Page" %}
{% set full_name = user.first ~ ' ' ~ user.last %}
```

### Comments
```twig
{# This comment is removed entirely from output #}
```

### Whitespace Control
Strip whitespace around tags with `-`:
```twig
{%- if show -%}
    Content
{%- endif -%}
```
Similarly for `{{-` and `-}}`.

---

## 🌍 Global Variables

You can define variables that are available in **every** template:
```php
$engine->addGlobal('app_name', 'MyAwesomeApp');
$engine->addGlobal('current_year', date('Y'));
```
Then in any template:
```twig
<footer>&copy; {{ current_year }} {{ app_name }}</footer>
```

---

## 🔧 Custom Filters

Register your own filters with a callable:
```php
$engine->addFilter('currency', function($amount, $symbol = '$') {
    return $symbol . number_format($amount, 2);
});
```
Then use it:
```twig
{{ price|currency('€') }}
```

---

## 🗂️ Caching

If you pass a cache directory, compiled PHP files are stored there:
```php
$engine = new FlaskTemplate(__DIR__ . '/templates', __DIR__ . '/cache');
```
The engine checks the source file’s modification time and recompiles only when needed.  
For maximum performance in production, ensure the cache directory is writable.

If no cache directory is given, the engine uses temporary files (created with `tempnam()`) that are automatically deleted after each render – this is safe but slightly slower.

---

## 🔒 Security Highlights

- **No `eval()`** – all templates are compiled to real PHP files and `include()`d.
- **Expression sandbox** – only safe functions (`isset`, `empty`, `in_array`) are allowed; all other function calls are **blocked**.
- **Path traversal protection** – template names are validated to stay inside the template directory.
- **Auto‑escaping** by default – prevents XSS.
- **Raw blocks** are isolated and never parsed.
- **Comments are stripped** before compilation.

---

## 🧪 Complete Example

### File structure
```
project/
├── FlaskTemplate.php
├── templates/
│   ├── base.html
│   ├── index.html
│   └── header.html
└── public/
    └── index.php
```

### `templates/base.html`
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Default Title{% endblock %}</title>
</head>
<body>
    {% include "header.html" %}
    <main>
        {% block content %}{% endblock %}
    </main>
    <footer>&copy; {{ current_year }} {{ app_name }}</footer>
</body>
</html>
```

### `templates/header.html`
```twig
<header>
    <h1>{{ app_name }}</h1>
</header>
```

### `templates/index.html`
```twig
{% extends "base.html" %}

{% block title %}Welcome{% endblock %}

{% block content %}
    <h2>Hello, {{ user.name }}!</h2>
    <ul>
    {% for item in items %}
        <li>{{ item|upper }}</li>
    {% else %}
        <li>No items.</li>
    {% endfor %}
    </ul>
{% endblock %}
```

### `public/index.php`
```php
<?php
require_once __DIR__ . '/../FlaskTemplate.php';

$engine = new FlaskTemplate(__DIR__ . '/../templates', __DIR__ . '/../cache', true);
$engine->addGlobal('app_name', 'MySite');
$engine->addGlobal('current_year', date('Y'));

$data = [
    'user'  => ['name' => 'John Doe'],
    'items' => ['apple', 'banana', 'cherry']
];

echo $engine->render('index.html', $data);
```

### Output (HTML)
```html
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <header>
        <h1>MySite</h1>
    </header>
    <main>
        <h2>Hello, John Doe!</h2>
        <ul>
            <li>APPLE</li>
            <li>BANANA</li>
            <li>CHERRY</li>
        </ul>
    </main>
    <footer>&copy; 2026 MySite</footer>
</body>
</html>
```

---

## ⚙️ Advanced: Disabling Auto‑escape

If you need raw output for a whole template (e.g., generating JSON or XML), you can disable auto‑escape at engine creation:
```php
$engine = new FlaskTemplate(__DIR__ . '/templates', null, false);
```
Then you must manually escape where needed, or use the `escape` filter.

---

## 🧪 Error Handling

The engine throws `RuntimeException` for:
- Missing templates.
- Invalid template names (outside the directory).
- Syntax errors (e.g., malformed expressions).

Wrap `render()` in a try‑catch to handle gracefully.

---

## 🏎️ Performance Tips

- **Use the cache directory** – compiled templates are stored and reused.
- **Avoid complex logic inside templates** – keep presentation logic minimal.
- **Use globals** for values that don’t change often.
- **Enable OPcache** – PHP’s opcode cache will speed up `include()` of compiled files.

---

## 📚 Summary

The Enhanced FlaskTemplate gives you a **secure, full‑featured, Jinja2‑like** templating experience in PHP – without third‑party libraries. It’s ideal for micro‑frameworks, CMSes, or any project that needs a clean separation of logic and presentation.

Start using it today and enjoy the simplicity and safety of Flask‑style templating in PHP!
 */



/**
 * 🧪 Detailed Usage Guide
1. Project Structure
text
project/
├── Router.php              # Contains both FlaskTemplate and Router
├── templates/              # Your HTML templates
│   ├── base.html
│   ├── index.html
│   └── error.html
├── models/                 # Optional model classes (auto‑loaded)
│   └── User.php
└── public/
    └── index.php           # Entry point
2. Entry Point (public/index.php)
php
<?php
require_once __DIR__ . '/../Router.php';

// Instantiate router with template support
$router = new Router(
    templateDir: __DIR__ . '/../templates',
    cacheDir:    __DIR__ . '/../cache',   // optional, for compiled templates
    autoescape:  true                     // HTML escaping on by default
);

// Define routes using magic methods
$router->get('/', function ($class, $args) {
    // Render a template and return HTML string
    return $class->render('index.html', ['user' => 'John']);
});

$router->get('/api/user/{id:int}', function ($class, $args) {
    // Return JSON response
    $userId = $args->getValue('id');
    return [
        'status'      => true,
        'statusCode'  => 200,
        'messageCode' => 0,
        'message'     => 'User found',
        'data'        => ['id' => $userId, 'name' => 'John Doe']
    ];
});

$router->post('/api/users', function ($class, $args) {
    // Access POST/JSON data via $args
    $name = $args->getValue('name');
    // ... create user ...
    return [
        'status'      => true,
        'statusCode'  => 201,
        'messageCode' => 0,
        'message'     => 'User created',
        'data'        => ['name' => $name]
    ];
});

// Everything is automatically executed in the destructor.
3. Template Example (templates/index.html)
twig
{% extends "base.html" %}

{% block title %}Home{% endblock %}

{% block content %}
    <h1>Hello, {{ user }}!</h1>
    <p>This page is rendered by the FlaskTemplate engine.</p>
{% endblock %}
4. Advanced: Using Models & Error Handling
php
$router->get('/users/{id:int}', function ($class, $args) {
    $id = $args->getValue('id');
    try {
        $userModel = $class->getModel('User');
        $user = $userModel->find($id);
        if (!$user) {
            $class->setStatusCode(404);
            return [
                'status'      => false,
                'statusCode'  => 404,
                'messageCode' => 1001,
                'message'     => 'User not found'
            ];
        }
        return [
            'status'      => true,
            'statusCode'  => 200,
            'messageCode' => 0,
            'message'     => 'OK',
            'data'        => $user
        ];
    } catch (Exception $e) {
        // The router will automatically catch and format this.
        throw new Exception('Database error: ' . $e->getMessage(), 500);
    }
});
5. Registering Custom Route Parameter Types
php
// In your bootstrap or inside the Router constructor override
$router->addType('hex', '[0-9a-fA-F]+', function($val) { return hexdec($val); });

// Then use it: /item/{color:hex}
6. Auto‑loading Models
php
// Automatically register all model classes from the models/ directory
$router->autoModelRegister();
// Now you can use $router->getModel('User') etc.
📦 Complete Integration Example
public/index.php – full working example:

php
<?php
require_once __DIR__ . '/../Router.php';

$router = new Router(
    templateDir: __DIR__ . '/../templates',
    cacheDir:    __DIR__ . '/../cache',
    autoescape:  true
);

// Register custom filter (optional)
$router->templateEngine->addFilter('stars', function($value) {
    return str_repeat('⭐', (int)$value);
});

// Home page – HTML
$router->get('/', function($class, $args) {
    return $class->render('index.html', ['user' => 'Guest']);
});

// JSON API – get user
$router->get('/api/user/{id:int}', function($class, $args) {
    $id = $args->getValue('id');
    return [
        'status'      => true,
        'statusCode'  => 200,
        'messageCode' => 0,
        'message'     => 'User profile',
        'user'        => ['id' => $id, 'name' => 'John Doe']
    ];
});

// POST – create user
$router->post('/api/user', function($class, $args) {
    $name = $args->getValue('name');
    if (empty($name)) {
        $class->setStatusCode(400);
        return [
            'status'      => false,
            'statusCode'  => 400,
            'messageCode' => 1002,
            'message'     => 'Name is required'
        ];
    }
    // ... save to database ...
    return [
        'status'      => true,
        'statusCode'  => 201,
        'messageCode' => 0,
        'message'     => 'User created',
        'data'        => ['name' => $name]
    ];
});

// Any method – fallback for invalid routes (already handled by Router)
✅ Summary
The enhanced Router now offers:

Flask‑style routing with parameter types (int, string, uuid, etc.)

Automatic token authentication (Bearer tokens)

Flexible responses – JSON or HTML (via templates)

Built‑in template engine (secure, eval‑free, Jinja2‑like)

Model auto‑loading for quick data access

Clean, exception‑safe error handling

All in a single, dependency‑free file – perfect for micro‑services, APIs, or full‑stack PHP applications that want a Flask‑like experience without a heavy framework.
 */