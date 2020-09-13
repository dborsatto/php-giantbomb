<?php

$fileHeaderComment = <<<COMMENT
This file is part of the dborsatto/php-giantbomb package.

@license MIT
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('spec')
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setRules([
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'combine_nested_dirname' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'dir_constant' => true,
        'ereg_to_preg' => true,
        'escape_implicit_backslashes' => false,
        'heredoc_indentation' => true,
        'function_to_constant' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'header_comment' => [
            'commentType' => 'PHPDoc',
            'header' => $fileHeaderComment,
            'separate' => 'both',
        ],
        'implode_call' => true,
        'is_null' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'logical_operators' => true,
        'mb_str_functions' => true,
        'modernize_types_casting' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'native_constant_invocation' => true,
        'native_function_invocation' => true,
        'no_alias_functions' => true,
        'no_superfluous_elseif' => false,
        'no_superfluous_phpdoc_tags' => false,
        'no_unneeded_final_method' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_add_missing_param_annotation' => false,
        'phpdoc_line_span' => [
            'const' => 'multi',
            'method' => 'multi',
            'property' => 'multi',
        ],
        'pow_to_exponentiation' => true,
        'random_api_migration' => true,
        'self_accessor' => true,
        'self_static_accessor' => true,
        'set_type_to_cast' => true,
        'single_line_throw' => false,
        'strict_comparison' => true,
        'strict_param' => true,
        'ternary_to_null_coalescing' => true,
    ])
;
