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

namespace Ergebnis\PHPUnit\AgentReporter\Test\Unit\Agent;

use Ergebnis\PHPUnit\AgentReporter\Agent;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Agent\Detector::class)]
final class DetectorTest extends Framework\TestCase
{
    public function testIsAgentReturnsFalseWhenNoAgentEnvironmentVariableIsSet(): void
    {
        $detector = new Agent\Detector();

        $result = $detector->isAgent([]);

        self::assertFalse($result);
    }

    #[Framework\Attributes\DataProvider('provideAgentEnvironmentVariable')]
    public function testIsAgentReturnsTrueWhenAgentEnvironmentVariableIsSet(string $variable): void
    {
        $detector = new Agent\Detector();

        $result = $detector->isAgent([
            $variable => '1',
        ]);

        self::assertTrue($result);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideAgentEnvironmentVariable(): iterable
    {
        $variables = [
            'AI_AGENT',
            'AMP_CURRENT_THREAD_ID',
            'ANTIGRAVITY_AGENT',
            'AUGMENT_AGENT',
            'CLAUDECODE',
            'CLAUDE_CODE',
            'CLAUDE_CODE_IS_COWORK',
            'CODEX_CI',
            'CODEX_SANDBOX',
            'CODEX_THREAD_ID',
            'COPILOT_ALLOW_ALL',
            'COPILOT_GITHUB_TOKEN',
            'COPILOT_MODEL',
            'CURSOR_AGENT',
            'CURSOR_TRACE_ID',
            'GEMINI_CLI',
            'OPENCODE',
            'OPENCODE_CLIENT',
            'REPL_ID',
        ];

        foreach ($variables as $variable) {
            yield $variable => [
                $variable,
            ];
        }
    }
}
