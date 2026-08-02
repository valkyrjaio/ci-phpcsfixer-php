<p align="center"><a href="https://valkyrja.io" target="_blank">
    <img src="https://raw.githubusercontent.com/valkyrjaio/art/refs/heads/master/long-banner/orange/php.png" width="100%">
</a></p>

# Valkyrja PHP CS Fixer

Shared PHP CS Fixer configuration for Valkyrja PHP projects — a reusable
rule set and workflow that enforce consistent code style across consuming
repositories.

<p>
    <a href="https://packagist.org/packages/valkyrja/phpcsfixer"><img src="https://poser.pugx.org/valkyrja/phpcsfixer/require/php" alt="PHP Version Require"></a>
    <a href="https://packagist.org/packages/valkyrja/phpcsfixer"><img src="https://poser.pugx.org/valkyrja/phpcsfixer/v" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/valkyrja/phpcsfixer"><img src="https://poser.pugx.org/valkyrja/phpcsfixer/license" alt="License"></a>
    <a href="https://github.com/valkyrjaio/ci-phpcsfixer-php/actions/workflows/ci.yml?query=branch%3A26.x"><img src="https://github.com/valkyrjaio/ci-phpcsfixer-php/actions/workflows/ci.yml/badge.svg?branch=26.x" alt="CI Status"></a>
    <a href="https://scrutinizer-ci.com/g/valkyrjaio/ci-phpcsfixer-php/?branch=26.x"><img src="https://scrutinizer-ci.com/g/valkyrjaio/ci-phpcsfixer-php/badges/quality-score.png?b=26.x" alt="Scrutinizer"></a>
    <a href="https://coveralls.io/github/valkyrjaio/ci-phpcsfixer-php?branch=26.x"><img src="https://coveralls.io/repos/github/valkyrjaio/ci-phpcsfixer-php/badge.svg?branch=26.x" alt="Coverage Status" /></a>
    <a href="https://shepherd.dev/github/valkyrjaio/ci-phpcsfixer-php"><img src="https://shepherd.dev/github/valkyrjaio/ci-phpcsfixer-php/coverage.svg" alt="Psalm Shepherd" /></a>
    <a href="https://sonarcloud.io/summary/new_code?id=valkyrjaio_phpcsfixer"><img src="https://sonarcloud.io/api/project_badges/measure?project=valkyrjaio_phpcsfixer&metric=sqale_rating" alt="Maintainability Rating" /></a>
</p>

Installation
------------

```
composer require valkyrja/phpcsfixer
```

Usage
-----

Call `Rules::getConfig()` from your `.php-cs-fixer.php` configuration file
and set the finder on the returned config:

```
// .php-cs-fixer.php
use Valkyrja\Fixer\Rules;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$header = <<<HEADER
This file is part of the Acme package.

(c) Acme Corp <acme@example.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

return Rules::getConfig($finder, $header);
```

`getConfig()` accepts two required arguments:

| Parameter | Type     | Description                                                                                                          |
| --------- | -------- | -------------------------------------------------------------------------------------------------------------------- |
| `$finder` | `Finder` | The PHP CS Fixer `Finder` instance specifying which files to lint                                                    |
| `$header` | `string` | The license/copyright text injected into every file's `header_comment` block, placed after `declare(strict_types=1)` |

Configuration Details
---------------------

### Parallel Execution

The config runs PHP CS Fixer in parallel:

| Setting           | Value       |
| ----------------- | ----------- |
| Max processes     | 5           |
| Files per process | 10          |
| Process timeout   | 240 seconds |

### Preset Rule Sets

The following preset rule sets are applied in order:

| Set                     | Notes                                          |
| ----------------------- | ---------------------------------------------- |
| `@PHP80Migration:risky` | PHP 8.0 migration including risky fixers       |
| `@PHP81Migration`       | PHP 8.1 migration                              |
| `@PER-CS`               | PHP Evolving Recommendation coding style       |
| `@PER-CS:risky`         | PER-CS including risky fixers                  |
| `@Symfony`              | Symfony coding standard                        |
| `@Symfony:risky`        | Symfony coding standard including risky fixers |

Risky fixers are enabled (`setRiskyAllowed(true)`).

### Additional Rules

#### Enabled

| Rule                                       | Description                                                            |
| ------------------------------------------ | ---------------------------------------------------------------------- |
| `no_unused_imports`                        | Remove unused `use` statements                                         |
| `align_multiline_comment`                  | Align multiline doc comments                                           |
| `array_indentation`                        | Each array element on its own line is indented once                    |
| `assign_null_coalescing_to_coalesce_equal` | Use `??=` instead of `= ... ?? ...`                                    |
| `combine_consecutive_issets`               | Merge multiple consecutive `isset()` calls                             |
| `combine_consecutive_unsets`               | Merge multiple consecutive `unset()` calls                             |
| `comment_to_phpdoc`                        | Convert `/* */` comments to `/** */` where appropriate                 |
| `method_chaining_indentation`              | Method chaining must be indented consistently                          |
| `modernize_types_casting`                  | Replace `intval()`, `strval()`, etc. with casts                        |
| `no_unreachable_default_argument_value`    | Remove default argument values that are unreachable                    |
| `no_superfluous_elseif`                    | Replace `elseif` that can be `else if` with `else if`                  |
| `no_useless_else`                          | Remove useless `else` branches                                         |
| `no_useless_return`                        | Remove useless `return` statements                                     |
| `phpdoc_var_annotation_correct_order`      | `@var` type must come before description                               |
| `php_unit_strict`                          | Use strict PHPUnit assertions (`assertSame` over `assertEquals`, etc.) |
| `simplified_null_return`                   | Use `return;` instead of `return null;` for nullable returns           |
| `simple_to_complex_string_variable`        | Convert `${var}` to `{$var}` in strings                                |
| `static_lambda`                            | Use `static` on closures that don't use `$this`                        |
| `strict_comparison`                        | Use `===` and `!==` instead of `==` and `!=`                           |
| `strict_param`                             | Add strict flags to `in_array()`, `base64_decode()`, etc.              |
| `trailing_comma_in_multiline`              | Add trailing comma in multiline arrays, arguments, etc.                |
| `void_return`                              | Add `: void` return type to functions that return nothing              |

#### Disabled

These rules are explicitly turned off, overriding preset defaults:

| Rule                         | Reason                                                          |
| ---------------------------- | --------------------------------------------------------------- |
| `no_superfluous_phpdoc_tags` | PHPDoc tags are kept even when types are declared in signatures |
| `single_line_throw`          | `throw` expressions may span multiple lines                     |
| `unary_operator_spaces`      | No space required around unary operators                        |

#### Configured Rules

**`array_syntax`**

Short array syntax is enforced:

```
// bad
$a = array(1, 2, 3);
// good
$a = [1, 2, 3];
```

**`binary_operator_spaces`**

| Operator | Alignment                                                                           |
| -------- | ----------------------------------------------------------------------------------- |
| `=`      | `align_single_space` — assignment operators aligned within a block                  |
| `=>`     | `align_single_space_minimal_by_scope` — array arrows aligned minimally within scope |

**`blank_line_before_statement`**

A blank line is required before the following statements:
`break`, `continue`, `declare`, `default`, `do`, `exit`, `for`, `foreach`,
`goto`, `if`, `include`, `include_once`, `require`, `require_once`, `return`,
`switch`, `throw`, `try`, `while`, `yield`, `yield_from`.

**`concat_space`**

One space around the concatenation operator:

```
$s = 'Hello' . ' ' . 'World';
```

**`global_namespace_import`**

Classes, constants, and functions from the global namespace are imported
with `use` statements rather than referenced with a leading backslash.

**`header_comment`**

The string passed to `getConfig($header)` is injected as a `/* */` block
comment immediately after `declare(strict_types=1);` in every file.

**`increment_style`**

Post-increment/decrement (`$i++`, `$i--`) is enforced over pre-increment.

**`method_argument_space`**

- No multiple spaces after a comma
- Multiline argument lists are fully multiline (each argument on its own
  line)
- Arguments after heredocs are formatted correctly

**`multiline_whitespace_before_semicolons`**

Semicolons must not be on their own line after a multiline expression
(`no_multi_line` strategy).

**`no_alias_functions`**

All aliased functions are replaced with their canonical names (`@all` set).

**`nullable_type_declaration`**

Nullable types use union syntax:

```
// bad
?string $value
// good
string|null $value
```

**`operator_linebreak`**

Binary operators (including non-boolean) are placed at the **beginning** of
the continued line:

```
$result = $a
    && $b
    && $c;
```

**`ordered_class_elements`**

Class members must appear in this order (sort algorithm: none):

1. `use_trait`
2. `case` (enum cases)
3. Constants: `public` → `protected` → `private`
4. Static properties: `public` → `protected` → `private`
5. Readonly properties: `public` → `protected` → `private`
6. Instance properties: `public` → `protected` → `private`
7. `construct`, `destruct`
8. Static methods: `public` → `protected` → `private` (with abstract
   variants)
9. PHPUnit methods
10. Instance methods: `public` → `protected` → `private` (with abstract
    variants)

**`ordered_imports`**

`use` statements are sorted alphabetically and ordered by type:
classes first, then functions, then constants.

**`phpdoc_add_missing_param_annotation`**

Adds missing `@param` annotations only for untyped parameters.

**`phpdoc_order`**

PHPDoc tags must appear in the order: `@param`, `@throws`, `@return`.

**`phpdoc_tag_type`**

`@inheritDoc` is written as an annotation (`{@inheritDoc}`).

**`phpdoc_to_comment`**

Inline `/* */` comments are converted to `//` except for `@todo`, `@var`,
and `@psalm-suppress` tags.

**`phpdoc_types_order`**

PHPDoc union types are not sorted, but `null` is always placed last:

```
/** @param string|int|null $value */
```

**`php_unit_test_case_static_method_calls`**

PHPUnit assertion methods are called on `self::` by default. The
expectation methods (`any`, `atLeastOnce`, `exactly`, `once`, `never`) use
`$this->` since they return mock builder objects:

```
// assertions
self::assertSame($expected, $actual);

// mock expectations
$mock->expects($this->once())->method('run');
```

**`yoda_style`**

Yoda conditions are disabled — the variable goes on the left:

```
// bad (yoda)
if (null === $value) { ... }
// good
if ($value === null) { ... }
```

Workflows
---------

The [`_workflow-call.yml`](.github/workflows/_workflow-call.yml) reusable
workflow runs PHP CS Fixer against the calling repository's source. It is
designed to be called from other repositories via `workflow_call`.

### Inputs

| Input              | Type    | Default                   | Description                                                                                                                                           |
| ------------------ | ------- | ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| `paths`            | string  | —                         | **Required.** YAML filter spec with two keys: `ci` (CI config files that trigger a base-branch fetch) and `files` (all files that trigger the check). |
| `post-pr-comment`  | boolean | `true`                    | Post a PR comment on failure and remove it on success. Disable when the calling workflow handles its own reporting.                                   |
| `composer-options` | string  | `''`                      | Extra flags passed to every `composer install` step (e.g. `--ignore-platform-req=ext-openswoole`).                                                    |
| `php-version`      | string  | `'8.4'`                   | PHP version to use.                                                                                                                                   |
| `ci-directory`     | string  | `'.github/ci/phpcsfixer'` | Path to the CI directory containing `composer.json` and the tool config.                                                                              |
| `extensions`       | string  | `'mbstring, intl'`        | PHP extensions to install via `shivammathur/setup-php`.                                                                                               |

### Usage

```yaml
jobs:
  phpcsfixer:
    uses: valkyrjaio/ci-phpcsfixer-php/.github/workflows/_workflow-call.yml@26.x
    permissions:
      pull-requests: write
      contents: read
    with:
      php-version: '8.4'
      paths: |
        ci:
          - '.github/ci/phpcsfixer/**'
          - '.github/workflows/phpcsfixer.yml'
        files:
          - '.github/ci/phpcsfixer/**'
          - '.github/workflows/phpcsfixer.yml'
          - 'src/**/*.php'
          - 'composer.json'
    secrets: inherit
```

`secrets: inherit` is required to pass the `VALKYRJA_GHA_APP_ID` and
`VALKYRJA_GHA_PRIVATE_KEY` org secrets used for PR comments.

Contributing
------------

See [`CONTRIBUTING.md`][contributing url] for the submission process and
[`VOCABULARY.md`][vocabulary url] for the terminology used across Valkyrja.

Security Issues
---------------

If you discover a security vulnerability, please follow our
[disclosure procedure][security vulnerabilities url].

License
-------

Licensed under the [MIT license][MIT license url]. See
[`LICENSE.md`](./LICENSE.md).

[contributing url]: https://github.com/valkyrjaio/.github/blob/master/CONTRIBUTING.md
[vocabulary url]: https://github.com/valkyrjaio/.github/blob/master/VOCABULARY.md
[security vulnerabilities url]: https://github.com/valkyrjaio/.github/blob/master/SECURITY.md
[MIT license url]: https://opensource.org/licenses/MIT
