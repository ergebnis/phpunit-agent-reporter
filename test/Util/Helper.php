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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Util;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Event;
use PHPUnit\Metadata;

trait Helper
{
    final protected static function faker(string $locale = 'en_US'): Generator
    {
        /**
         * @var array<string, Generator> $fakers
         */
        static $fakers = [];

        if (!\array_key_exists($locale, $fakers)) {
            $faker = Factory::create($locale);

            $faker->seed(9001);

            $fakers[$locale] = $faker;
        }

        return $fakers[$locale];
    }

    /**
     * @param resource $stream
     */
    final protected static function contentsOf($stream): string
    {
        \rewind($stream);

        $contents = \stream_get_contents($stream);

        self::assertIsString($contents);

        return $contents;
    }

    /**
     * @return resource
     */
    final protected static function memoryStream()
    {
        $stream = \fopen(
            'php://memory',
            'w+b',
        );

        self::assertIsResource($stream);

        return $stream;
    }

    final protected static function testMethod(Event\TestData\TestDataCollection $testData): Event\Code\TestMethod
    {
        $faker = self::faker();

        $className = self::class;
        $methodName = 'test' . \ucfirst($faker->word());

        return new Event\Code\TestMethod(
            $className,
            $methodName,
            '/' . $faker->word() . '.php',
            __LINE__,
            new Event\Code\TestDox(
                $className,
                $methodName,
                $methodName,
            ),
            Metadata\MetadataCollection::fromArray([]),
            $testData,
        );
    }
}
