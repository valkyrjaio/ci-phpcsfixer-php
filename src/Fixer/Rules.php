<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja PHP CS Fixer package.
 *
 * Copyright (c) 2016-present Melech Mizrachi
 *
 * Released under the MIT License. See LICENSE.md for details.
 */

namespace Valkyrja\Fixer;

use InvalidArgumentException;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Runner\Parallel\ParallelConfig;

use function array_merge;
use function str_contains;

class Rules
{
    /**
     * Builds the copyright header for a package.
     *
     * The header states the package name, and every other line is the same in each repository. This
     * package therefore holds the text, and a repository states only its own name. A repository that
     * keeps a copy of the whole header can drift from this text, and no tool reports the drift.
     * COPYRIGHT_HEADER.md in the `.github` repository specifies the text, and it maps each
     * repository to its package name.
     *
     * @param string $package The package name, for example `Valkyrja Framework`
     *
     * @throws InvalidArgumentException When the package name spans more than one line
     */
    public static function getHeader(string $package): string
    {
        // Warning: a package name that spans lines corrupts every file, and no check reports it.
        // This method puts the argument into the first line of the header, so an assembled header
        // builds "This file is part of the <whole header> package". PHP CS Fixer writes that text
        // into every file, and the check afterwards passes, because the files and the configuration
        // then agree with each other. A loud failure is better than a silent rewrite, so a name
        // that spans lines stops here.
        if (str_contains($package, "\n")) {
            throw new InvalidArgumentException(
                'Rules::getHeader() takes a package name, such as "Valkyrja Framework", and it was'
                . ' given text that spans lines. A caller that passes the assembled header must'
                . ' pass the name instead.'
            );
        }

        return <<<HEADER
            This file is part of the $package package.

            Copyright (c) 2016-present Melech Mizrachi

            Released under the MIT License. See LICENSE.md for details.
            HEADER;
    }

    /**
     * Builds the PHP CS Fixer configuration.
     *
     * The rules come from the methods below, and this method merges them in order. A later slice
     * overwrites an earlier one that holds the same rule name, so each rule name must appear in one
     * slice only.
     *
     * @param string $package The package name the header states, for example `Valkyrja Framework`.
     *                        Pass the name, never an assembled header.
     *
     * @throws InvalidArgumentException When the package name spans more than one line
     */
    public static function getConfig(Finder $finder, string $package): Config
    {
        $header = self::getHeader($package);

        return new Config()
            ->setParallelConfig(static::getParallelConfig())
            ->setRiskyAllowed(true)
            ->registerCustomFixers(static::getCustomFixers())
            ->setRules(
                array_merge(
                    static::getPresetRules(),
                    static::getNameResolutionRules(),
                    static::getBooleanRules(),
                    static::getConfiguredRules($header),
                )
            )
            ->setFinder($finder);
    }

    /**
     * Builds the parallel runner configuration.
     */
    protected static function getParallelConfig(): ParallelConfig
    {
        return new ParallelConfig(
            5,
            10,
            240
        );
    }

    /**
     * The custom fixers this configuration registers.
     *
     * @return list<FixerInterface>
     */
    protected static function getCustomFixers(): array
    {
        return [
        ];
    }

    /**
     * The rule sets this configuration inherits.
     *
     * @return array<string, bool>
     */
    protected static function getPresetRules(): array
    {
        return [
            '@PHP80Migration:risky' => true,
            '@PHP81Migration'       => true,
            '@PER-CS'               => true,
            '@PER-CS:risky'         => true,
            '@Symfony'              => true,
            '@Symfony:risky'        => true,
        ];
    }

    /**
     * The rules for how a file names a function or a class.
     *
     * `no_unused_imports` removes an import that the file does not name.
     *
     * `native_function_invocation` makes a call to a native function resolve at compile time. In a
     * namespaced file an unqualified call to a builtin function emits a runtime lookup for a
     * namespace-local function. Path coverage counts that lookup as a branch that no test covers. A
     * qualified call removes the branch, and the `global_namespace_import` rule in
     * `getConfiguredRules()` imports the function again, so the file keeps the short call.
     *
     * @return array<string, bool|array<string, mixed>>
     */
    protected static function getNameResolutionRules(): array
    {
        return [
            'no_unused_imports'          => true,
            'native_function_invocation' => [
                'include' => ['@all'],
                'scope'   => 'namespaced',
                'strict'  => true,
            ],
        ];
    }

    /**
     * The rules that take no options.
     *
     * The value turns the rule on or off.
     *
     * @return array<string, bool>
     */
    protected static function getBooleanRules(): array
    {
        return [
            'align_multiline_comment'                  => true,
            'array_indentation'                        => true,
            'assign_null_coalescing_to_coalesce_equal' => true,
            'combine_consecutive_issets'               => true,
            'combine_consecutive_unsets'               => true,
            'comment_to_phpdoc'                        => true,
            'declare_strict_types'                     => true,
            'method_chaining_indentation'              => true,
            'modernize_types_casting'                  => true,
            'no_unreachable_default_argument_value'    => true,
            'no_superfluous_elseif'                    => true,
            'no_superfluous_phpdoc_tags'               => false,
            'no_useless_else'                          => true,
            'no_useless_return'                        => true,
            'phpdoc_var_annotation_correct_order'      => true,
            'php_unit_strict'                          => true,
            'simplified_null_return'                   => true,
            'simple_to_complex_string_variable'        => true,
            'single_line_throw'                        => false,
            'static_lambda'                            => true,
            'strict_comparison'                        => true,
            'strict_param'                             => true,
            'trailing_comma_in_multiline'              => true,
            'unary_operator_spaces'                    => false,
            'void_return'                              => true,
        ];
    }

    /**
     * The rules that take an options array.
     *
     * @param string $header The copyright header the `header_comment` rule writes
     *
     * @return array<string, array<string, mixed>>
     */
    protected static function getConfiguredRules(string $header): array
    {
        return [
            'array_syntax'                           => [
                'syntax' => 'short',
            ],
            'blank_line_before_statement'            => static::getBlankLineBeforeStatementOptions(),
            'binary_operator_spaces'                 => [
                'operators' => [
                    '='  => 'align_single_space',
                    '=>' => 'align_single_space_minimal_by_scope',
                ],
            ],
            'concat_space'                           => [
                'spacing' => 'one',
            ],
            'global_namespace_import'                => [
                'import_classes'   => true,
                'import_constants' => true,
                'import_functions' => true,
            ],
            'header_comment'                         => [
                'header'       => $header,
                'comment_type' => 'comment',
                'location'     => 'after_declare_strict',
            ],
            'increment_style'                        => [
                'style' => 'post',
            ],
            'method_argument_space'                  => [
                'keep_multiple_spaces_after_comma' => false,
                'on_multiline'                     => 'ensure_fully_multiline',
                'after_heredoc'                    => true,
            ],
            'multiline_whitespace_before_semicolons' => [
                'strategy' => 'no_multi_line',
            ],
            'nullable_type_declaration'              => [
                'syntax' => 'union',
            ],
            'no_alias_functions'                     => [
                'sets' => ['@all'],
            ],
            'operator_linebreak'                     => [
                'only_booleans' => false,
                'position'      => 'beginning',
            ],
            'ordered_class_elements'                 => static::getOrderedClassElementsOptions(),
            'ordered_imports'                        => [
                'sort_algorithm' => 'alpha',
                'imports_order'  => ['class', 'function', 'const'],
            ],
            'phpdoc_add_missing_param_annotation'    => [
                'only_untyped' => true,
            ],
            'phpdoc_order'                           => [
                'order' => ['param', 'throws', 'return'],
            ],
            'phpdoc_tag_type'                        => [
                'tags' => ['inheritDoc' => 'annotation'],
            ],
            'phpdoc_to_comment'                      => [
                'ignored_tags' => ['todo', 'var', 'psalm-suppress'],
            ],
            'phpdoc_types_order'                     => [
                'sort_algorithm'  => 'none',
                'null_adjustment' => 'always_last',
            ],
            'php_unit_test_case_static_method_calls' => static::getPhpUnitTestCaseStaticMethodCallsOptions(),
            'yoda_style'                             => [
                'equal'            => false,
                'identical'        => false,
                'less_and_greater' => false,
            ],
        ];
    }

    /**
     * The options for the `blank_line_before_statement` rule.
     *
     * @return array<string, mixed>
     */
    protected static function getBlankLineBeforeStatementOptions(): array
    {
        return [
            'statements' => [
                'break',
                'continue',
                'declare',
                'default',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
                'yield_from',
            ],
        ];
    }

    /**
     * The options for the `ordered_class_elements` rule.
     *
     * @return array<string, mixed>
     */
    protected static function getOrderedClassElementsOptions(): array
    {
        return [
            'order'          => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'property_public_readonly',
                'property_protected_readonly',
                'property_private_readonly',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'method_public_static',
                'method_public_abstract_static',
                'method_protected_static',
                'method_protected_abstract_static',
                'method_private_static',
                'method_private_abstract_static',
                'phpunit',
                'method_public',
                'method_public_abstract',
                'method_protected',
                'method_protected_abstract',
                'method_private',
                'method_private_abstract',
            ],
            'sort_algorithm' => 'none',
        ];
    }

    /**
     * The options for the `php_unit_test_case_static_method_calls` rule.
     *
     * @return array<string, mixed>
     */
    protected static function getPhpUnitTestCaseStaticMethodCallsOptions(): array
    {
        return [
            'call_type' => 'self',
            'methods'   => [
                'any'         => 'this',
                'atLeastOnce' => 'this',
                'exactly'     => 'this',
                'once'        => 'this',
                'never'       => 'this',
            ],
        ];
    }
}
