<?php

declare(strict_types=1);

use Composer\IO\BufferIO;
use Composer\Script\Event;
use HookPress\Hooks\HookRunner;
use Mockery as m;

it('skips when artisan is missing and logs to IO', function (): void {
    // Ensure no artisan in base path
    @unlink(base_path('artisan'));

    $io = new BufferIO;
    $event = m::mock(Event::class);
    $event->shouldReceive('getIO')->andReturn($io);

    HookRunner::postInstall($event);

    $output = $io->getOutput();
    expect($output)->toContain('artisan not found; skipping.');
});

it('attempts to run artisan when present (returns 0)', function (): void {
    // Create a minimal artisan stub that always exits 0
    $artisan = base_path('artisan');
    file_put_contents($artisan, <<<'PHP'
#!/usr/bin/env php
<?php
// Accept any args and exit success to simulate running a command.
exit(0);
PHP);
    @chmod($artisan, 0755);

    $io = new BufferIO;
    $event = m::mock(Event::class);
    $event->shouldReceive('getIO')->andReturn($io);

    HookRunner::postUpdate($event);

    $out = $io->getOutput();

    expect($out)->toContain('HookPress:');
});
