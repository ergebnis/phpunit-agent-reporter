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

use Ergebnis\PHPUnit\AgentReporter;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(AgentReporter\Report\Report::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Actual::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ComparisonFailure::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Count::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Deprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\DeprecationList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Diff::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ErroredTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ErroredTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Expected::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\FailedTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\FailedTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\File::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\IncompleteTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\IncompleteTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Line::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Message::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Notice::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\NoticeList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitDeprecation::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitDeprecationList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitNotice::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitNoticeList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitWarning::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\PhpunitWarningList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Result::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\RiskyTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\RiskyTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\ShellExitCode::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\SkippedTest::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\SkippedTestList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifier::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\TestIdentifierList::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\Warning::class)]
#[Framework\Attributes\UsesClass(AgentReporter\Report\WarningList::class)]
final class ReportTest extends Framework\TestCase
{
    use AgentReporter\Test\Util\Helper;

    public function testResultReturnsSuccessWhenShellExitCodeIsSuccess(): void
    {
        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::success(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt(0),
            AgentReporter\Report\Count::fromInt(0),
        );

        $result = $report->result();

        self::assertTrue($result->equals(AgentReporter\Report\Result::success()));
    }

    public function testResultReturnsExceptionWhenShellExitCodeIsException(): void
    {
        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::exception(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt(0),
            AgentReporter\Report\Count::fromInt(0),
        );

        $result = $report->result();

        self::assertTrue($result->equals(AgentReporter\Report\Result::exception()));
    }

    public function testResultReturnsFailureWhenShellExitCodeIsFailure(): void
    {
        $report = AgentReporter\Report\Report::create(
            AgentReporter\Report\ShellExitCode::failure(),
            AgentReporter\Report\ErroredTestList::create(),
            AgentReporter\Report\FailedTestList::create(),
            AgentReporter\Report\IncompleteTestList::create(),
            AgentReporter\Report\SkippedTestList::create(),
            AgentReporter\Report\RiskyTestList::create(),
            AgentReporter\Report\DeprecationList::create(),
            AgentReporter\Report\NoticeList::create(),
            AgentReporter\Report\WarningList::create(),
            AgentReporter\Report\PhpunitDeprecationList::create(),
            AgentReporter\Report\PhpunitNoticeList::create(),
            AgentReporter\Report\PhpunitWarningList::create(),
            AgentReporter\Report\Count::fromInt(0),
            AgentReporter\Report\Count::fromInt(0),
        );

        $result = $report->result();

        self::assertTrue($result->equals(AgentReporter\Report\Result::failure()));
    }

    public function testCreateReturnsReport(): void
    {
        $faker = self::faker();

        $erroredTestList = AgentReporter\Report\ErroredTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\ErroredTest {
            return AgentReporter\Report\ErroredTest::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $failedTestList = AgentReporter\Report\FailedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\FailedTest {
            return AgentReporter\Report\FailedTest::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
                AgentReporter\Report\ComparisonFailure::create(
                    AgentReporter\Report\Actual::fromString($faker->text()),
                    AgentReporter\Report\Diff::fromString($faker->text()),
                    AgentReporter\Report\Expected::fromString($faker->text()),
                ),
            );
        }, \range(0, 2)));

        $incompleteTestList = AgentReporter\Report\IncompleteTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\IncompleteTest {
            return AgentReporter\Report\IncompleteTest::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $skippedTestList = AgentReporter\Report\SkippedTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\SkippedTest {
            return AgentReporter\Report\SkippedTest::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $riskyTestList = AgentReporter\Report\RiskyTestList::create(...\array_map(static function () use ($faker): AgentReporter\Report\RiskyTest {
            return AgentReporter\Report\RiskyTest::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $deprecationList = AgentReporter\Report\DeprecationList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Deprecation {
            return AgentReporter\Report\Deprecation::create(
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
                AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                    return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, 2)));

        $noticeList = AgentReporter\Report\NoticeList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Notice {
            return AgentReporter\Report\Notice::create(
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
                AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                    return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, 2)));

        $warningList = AgentReporter\Report\WarningList::create(...\array_map(static function () use ($faker): AgentReporter\Report\Warning {
            return AgentReporter\Report\Warning::create(
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
                AgentReporter\Report\TestIdentifierList::create(...\array_map(static function () use ($faker): AgentReporter\Report\TestIdentifier {
                    return AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                        '%s::%s',
                        $faker->word(),
                        $faker->word(),
                    ));
                }, \range(0, 2))),
            );
        }, \range(0, 2)));

        $phpunitDeprecationList = AgentReporter\Report\PhpunitDeprecationList::create(...\array_map(static function () use ($faker): AgentReporter\Report\PhpunitDeprecation {
            return AgentReporter\Report\PhpunitDeprecation::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $phpunitNoticeList = AgentReporter\Report\PhpunitNoticeList::create(...\array_map(static function () use ($faker): AgentReporter\Report\PhpunitNotice {
            return AgentReporter\Report\PhpunitNotice::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $phpunitWarningList = AgentReporter\Report\PhpunitWarningList::create(...\array_map(static function () use ($faker): AgentReporter\Report\PhpunitWarning {
            return AgentReporter\Report\PhpunitWarning::create(
                AgentReporter\Report\TestIdentifier::fromString(\sprintf(
                    '%s::%s',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\File::fromString(\sprintf(
                    '%s/%s.php',
                    $faker->word(),
                    $faker->word(),
                )),
                AgentReporter\Report\Line::fromInt($faker->numberBetween(1, 500)),
                AgentReporter\Report\Message::fromString($faker->sentence()),
            );
        }, \range(0, 2)));

        $totalAssertionCount = AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100));
        $totalTestCount = AgentReporter\Report\Count::fromInt($faker->numberBetween(1, 100));
        $shellExitCode = AgentReporter\Report\ShellExitCode::fromInt($faker->numberBetween(0, 2));

        $report = AgentReporter\Report\Report::create(
            $shellExitCode,
            $erroredTestList,
            $failedTestList,
            $incompleteTestList,
            $skippedTestList,
            $riskyTestList,
            $deprecationList,
            $noticeList,
            $warningList,
            $phpunitDeprecationList,
            $phpunitNoticeList,
            $phpunitWarningList,
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
        self::assertSame($phpunitDeprecationList, $report->phpunitDeprecationList());
        self::assertSame($phpunitNoticeList, $report->phpunitNoticeList());
        self::assertSame($phpunitWarningList, $report->phpunitWarningList());
        self::assertSame($totalAssertionCount, $report->totalAssertionCount());
        self::assertSame($totalTestCount, $report->totalTestCount());
    }
}
