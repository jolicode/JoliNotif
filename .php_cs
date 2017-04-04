<?php

$header = <<<'EOF'
This file is part of the JoliNotif project.

(c) Loïck Piera <pyrech@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(array(
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => array('header' => $header),
        'array_syntax' => array('syntax' => 'short'),
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'heredoc_to_nowdoc' => true,
        'php_unit_strict' => true,
        'php_unit_construct' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'no_extra_consecutive_blank_lines' => array('break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'),
        'no_short_echo_tag' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'semicolon_after_instruction' => true,
        'combine_consecutive_unsets' => true,
    ))
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
    )
;
