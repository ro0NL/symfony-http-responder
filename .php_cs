<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    '@PHP71Migration' => true,
    '@PHP71Migration:risky' => true,
    '@PHPUnit75Migration:risky' => true,
    'header_comment' => ['header' => ''],
    'list_syntax' => ['syntax' => 'short'],
    'nullable_type_declaration_for_default_null_value' => true,
    'phpdoc_line_span' => ['const' => 'single', 'method' => 'single', 'property' => 'single'],
    'self_static_accessor' => true,
];

return Config::create()
    ->setUsingCache(true)
    ->setCacheFile(__DIR__.'/var/php-cs-fixer.cache')
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setFinder(
        Finder::create()
            ->in([
                __DIR__.'/src',
                __DIR__.'/tests',
            ])
            ->append([
                __FILE__,
            ])
    )
;
