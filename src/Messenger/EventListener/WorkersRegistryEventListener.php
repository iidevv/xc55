<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;

final class WorkersRegistryEventListener implements EventSubscriberInterface
{
    private Filesystem $filesystem;

    private string $workersPoolPath;

    /**
     * @var false|int
     */
    private $pid;

    public function __construct(Filesystem $filesystem, string $workersPoolPath)
    {
        $this->filesystem = $filesystem;
        $this->workersPoolPath = $workersPoolPath;

        $this->pid = getmypid();
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerStartedEvent::class => 'onWorkerStarted',
            WorkerStoppedEvent::class => 'onWorkerStopped',
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }

    public function onWorkerStarted(): void
    {
        $this->filesystem->dumpFile($this->workersPoolPath . $this->pid, microtime(true));

        if (function_exists('pcntl_signal')) {
            pcntl_signal(\SIGINT, function () {
                $this->onWorkerStopped();
                // We should only write remove our flag and exit, should not resume execution or wait for graceful stop
                exit();
            });
        }
    }

    public function onWorkerRunning(): void
    {
        $this->filesystem->dumpFile($this->workersPoolPath . $this->pid, microtime(true));
    }

    public function onWorkerStopped(): void
    {
        $this->filesystem->remove($this->workersPoolPath . $this->pid);
    }
}
