<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/phpunit-agent-reporter
 */

use Ergebnis\Rector;

$ruleSet = Rector\Config\RuleSet\Php81::create()->withRules(Rector\Config\Rules::fromArray([
    Rector\Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector::class => [
        'comparison_function' => 'strcmp',
        'direction' => 'asc',
    ],
    Rector\Rules\Files\ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector::class => [
        'discoverNamespacePrefixes' => true,
        'parentNamespacePrefixes' => [
            'Ergebnis\PHPUnit',
            'Symfony\Component',
        ],
    ],
]));

return Rector\Config\Factory::fromRuleSet($ruleSet)
    ->withCache(__DIR__ . '/.build/rector/')
    ->withPaths([
        __DIR__ . '/src/',
        __DIR__ . '/test/',
        __DIR__ . '/.php-cs-fixer.php',
        __DIR__ . '/rector.php',
    ]);
