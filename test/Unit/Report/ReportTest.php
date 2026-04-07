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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Unit\Report;

use Ergebnis\PHPUnit\AgentReporter\Report;
use Ergebnis\PHPUnit\AgentReporter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Report\Report::class)]
#[Framework\Attributes\UsesClass(Report\Actual::class)]
#[Framework\Attributes\UsesClass(Report\ComparisonFailure::class)]
#[Framework\Attributes\UsesClass(Report\Count::class)]
#[Framework\Attributes\UsesClass(Report\Deprecation::class)]
#[Framework\Attributes\UsesClass(Report\DeprecationList::class)]
#[Framework\Attributes\UsesClass(Report\Diff::class)]
#[Framework\Attributes\UsesClass(Report\ErroredTest::class)]
#[Framework\Attributes\UsesClass(Report\ErroredTestList::class)]
#[Framework\Attributes\UsesClass(Report\Expected::class)]
#[Framework\Attributes\UsesClass(Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(Report\FailedTestList::class)]
#[Framework\Attributes\UsesClass(Report\File::class)]
#[Framework\Attributes\UsesClass(Report\IncompleteTest::class)]
#[Framework\Attributes\UsesClass(Report\IncompleteTestList::class)]
#[Framework\Attributes\UsesClass(Report\Line::class)]
#[Framework\Attributes\UsesClass(Report\Message::class)]
#[Framework\Attributes\UsesClass(Report\Notice::class)]
#[Framework\Attributes\UsesClass(Report\NoticeList::class)]
#[Framework\Attributes\UsesClass(Report\Result::class)]
#[Framework\Attributes\UsesClass(Report\RiskyTest::class)]
#[Framework\Attributes\UsesClass(Report\RiskyTestList::class)]
#[Framework\Attributes\UsesClass(Report\ShellExitCode::class)]
#[Framework\Attributes\UsesClass(Report\SkippedTest::class)]
#[Framework\Attributes\UsesClass(Report\SkippedTestList::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(Report\TestIdentifierList::class)]
#[Framework\Attributes\UsesClass(Report\Warning::class)]
#[Framework\Attributes\UsesClass(Report\WarningList::class)]
final class ReportTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsReport(): void
    {
        $faker = self::faker();

        $erroredTestList = Report\ErroredTestList::create(...\array_map(static function () use ($faker): Report\ErroredTest {
            return Report\ErroredTest::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $failedTestList = Report\FailedTestList::create(...\array_map(static function () use ($faker): Report\FailedTest {
            return Report\FailedTest::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
                Report\ComparisonFailure::create(
                    Report\Actual::fromString($faker->text()),
                    Report\Diff::fromString($faker->text()),
                    Report\Expected::fromString($faker->text()),
                ),
            );
        }, \range(0, 2)));

        $incompleteTestList = Report\IncompleteTestList::create(...\array_map(static function () use ($faker): Report\IncompleteTest {
            return Report\IncompleteTest::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $skippedTestList = Report\SkippedTestList::create(...\array_map(static function () use ($faker): Report\SkippedTest {
            return Report\SkippedTest::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $riskyTestList = Report\RiskyTestList::create(...\array_map(static function () use ($faker): Report\RiskyTest {
            return Report\RiskyTest::create(
                Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $deprecationList = Report\DeprecationList::create(...\array_map(static function () use ($faker): Report\Deprecation {
            return Report\Deprecation::create(
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
                Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                    return Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, 2)));

        $noticeList = Report\NoticeList::create(...\array_map(static function () use ($faker): Report\Notice {
            return Report\Notice::create(
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
                Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                    return Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, 2)));

        $warningList = Report\WarningList::create(...\array_map(static function () use ($faker): Report\Warning {
            return Report\Warning::create(
                Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                Report\Line::fromInt($faker->numberBetween(1, 500)),
                Report\Message::fromString($faker->sentence()),
                Report\TestIdentifierList::create(...\array_map(static function () use ($faker): Report\TestIdentifier {
                    return Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, 2)));

        $totalAssertionCount = Report\Count::fromInt($faker->numberBetween(1, 100));
        $totalTestCount = Report\Count::fromInt($faker->numberBetween(1, 100));
        $shellExitCode = Report\ShellExitCode::fromInt($faker->numberBetween(0, 2));

        $report = Report\Report::create(
            $shellExitCode,
            $erroredTestList,
            $failedTestList,
            $incompleteTestList,
            $skippedTestList,
            $riskyTestList,
            $deprecationList,
            $noticeList,
            $warningList,
            $totalAssertionCount,
            $totalTestCount,
        );

        self::assertSame($shellExitCode, $report->shellExitCode());
        self::assertSame($erroredTestList, $report->erroredTestList());
        self::assertSame($failedTestList, $report->failedTestList());
        self::assertSame($incompleteTestList, $report->incompleteTestList());
        self::assertSame($skippedTestList, $report->skippedTestList());
        self::assertSame($riskyTestList, $report->riskyTestList());
        self::assertSame($deprecationList, $report->deprecationList());
        self::assertSame($noticeList, $report->noticeList());
        self::assertSame($warningList, $report->warningList());
        self::assertSame($totalAssertionCount, $report->totalAssertionCount());
        self::assertSame($totalTestCount, $report->totalTestCount());
    }
}
