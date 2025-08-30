<?php

declare(strict_types=1);

namespace HookPress\Hooks;

use Composer\Script\Event;

/**
 * Lightweight Composer hook that shells out to Artisan.
 * This avoids having to manually bootstrap the Laravel app here.
 */
class HookRunner
{
    public static function postInstall(Event $event): void
    {
        static::runArtisan(configKey: 'hook-press.composer.artisan_command', defaultCommand: 'hook-press.:build', event: $event);
    }

    public static function postUpdate(Event $event): void
    {
        static::runArtisan(configKey: 'hook-press.composer.artisan_command', defaultCommand: 'hook-press.:build', event: $event);
    }

    protected static function runArtisan(string $configKey, string $defaultCommand, Event $event): void
    {
        $io = $event->getIO();

        $php = escapeshellcmd(PHP_BINARY);
        $command = $defaultCommand;

        // Try to read config if Laravel helpers are available
        if (function_exists('config')) {
            $configured = config($configKey);
            if (is_string($configured) && $configured !== '') {
                $command = $configured;
            }
        }

        $cmd = "{$php} artisan {$command} --no-ansi --no-interaction";
        $io->write("<info>HookPress:</info> running `{$cmd}`");

        // Fallback if artisan is missing
        if (! file_exists(getcwd().DIRECTORY_SEPARATOR.'artisan')) {
            $io->writeError('<comment>HookPress:</comment> artisan not found; skipping.');

            return;
        }

        // Execute
        $exitCode = 0;
        passthru($cmd, $exitCode);

        if ($exitCode !== 0) {
            $io->writeError("<error>HookPress:</error> artisan command failed with code {$exitCode}.");
        }
    }
}
