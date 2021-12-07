<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$fileHeaderComment = <<<'EOF'
    This file is part of the JoliNotif project.

    (c) Loïck Piera <pyrech@gmail.com>

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
    EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->append([
        __FILE__,
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP74Migration' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'php_unit_internal_class' => false, // From @PhpCsFixer but we don't want it
        'php_unit_test_class_requires_covers' => false, // From @PhpCsFixer but we don't want it
        'phpdoc_add_missing_param_annotation' => false, // From @PhpCsFixer but we don't want it
        'header_comment' => ['header' => $fileHeaderComment],
        'concat_space' => ['spacing' => 'one'],
        'ordered_class_elements' => true, // Symfony(PSR12) override the default value, but we don't want
        'blank_line_before_statement' => true, // Symfony(PSR12) override the default value, but we don't want
    ])
    ->setFinder($finder)
;
