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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Unit\Output;

use Ergebnis\PHPUnit\AgentReporter\Output;
use Ergebnis\PHPUnit\AgentReporter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Output\Printer::class)]
final class PrinterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testPrintWritesText(): void
    {
        $text = self::faker()->sentence();

        $output = self::outputStream();

        $printer = new Output\Printer($output);

        $printer->print($text);

        self::assertOutputIsIdenticalTo($text, $output);
    }

    public function testPrintDoesNotStartWithLineBreakWhenTextPrintedElsewhereEndsWithLineBreak(): void
    {
        $text = self::faker()->sentence();

        $output = self::outputStream();

        $printer = new Output\Printer($output);

        $printer->printedElsewhere("foo\n");
        $printer->print($text);

        self::assertOutputIsIdenticalTo($text, $output);
    }

    public function testPrintStartsWithLineBreakWhenTextPrintedElsewhereDoesNotEndWithLineBreak(): void
    {
        $faker = self::faker();

        $text = $faker->sentence();
        $otherText = $faker->sentence();

        $output = self::outputStream();

        $printer = new Output\Printer($output);

        $printer->printedElsewhere('foo');
        $printer->print($text);
        $printer->print($otherText);

        self::assertOutputIsIdenticalTo(\PHP_EOL . $text . $otherText, $output);
    }

    public function testPrintKeepsPendingLineBreakWhenTextIsEmpty(): void
    {
        $text = self::faker()->sentence();

        $output = self::outputStream();

        $printer = new Output\Printer($output);

        $printer->printedElsewhere('foo');
        $printer->printedElsewhere('');
        $printer->print('');
        $printer->print($text);

        self::assertOutputIsIdenticalTo(\PHP_EOL . $text, $output);
    }
}
