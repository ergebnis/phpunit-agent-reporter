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
use JsonSchema\Validator;
use PHPUnit\Framework;

trait Helper
{
    final protected static function assertJsonSatisfiesAgentReportSchema(string $json): void
    {
        Framework\Assert::assertJson($json);

        $schemaFile = self::schemaFile();

        Framework\Assert::assertFileExists($schemaFile);

        $data = \json_decode($json);

        $validator = new Validator();

        $validator->validate(
            $data,
            (object) [
                '$ref' => \sprintf(
                    'file://%s',
                    \realpath($schemaFile),
                ),
            ],
        );

        $errors = $validator->getErrors();

        self::assertIsArray($errors);

        Framework\Assert::assertTrue($validator->isValid(), \sprintf(
            <<<'TXT'
Failed asserting that the JSON string satisfies the agent report schema:

%s
TXT
            ,
            \implode('', \array_map(static function (array $error): string {
                self::assertArrayHasKey('property', $error);
                self::assertIsString($error['property']);
                self::assertArrayHasKey('message', $error);
                self::assertIsString($error['message']);

                return \sprintf(
                    '- [%s] %s%s',
                    $error['property'],
                    $error['message'],
                    \PHP_EOL,
                );
            }, $errors)),
        ));
    }

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

    final protected static function schemaFile(): string
    {
        return __DIR__ . '/../../schema/agent-report-schema.json';
    }
}
