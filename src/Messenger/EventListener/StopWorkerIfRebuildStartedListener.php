<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;

final class StopWorkerIfRebuildStartedListener implements EventSubscriberInterface
{
    private RebuildFlag $rebuildFlag;

    private ?LoggerInterface $logger;

    public function __construct(RebuildFlag $rebuildFlag, LoggerInterface $logger = null)
    {
        $this->rebuildFlag = $rebuildFlag;
        $this->logger      = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerStartedEvent::class => 'onWorkerStarted',
            WorkerRunningEvent::class => 'onWorkerRunning', // Bulletproofing, rebuild will request workers stop itself
        ];
    }

    public function onWorkerStarted(WorkerStartedEvent $event): void
    {
        try {
            $this->rebuildFlag->check();
        } catch (RebuildInProgressException $e) {
            if ($this->logger) {
                $this->logger->warning('Rebuild in progress, stopping');
            }
            $event->getWorker()->stop();
        }
    }

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        try {
            $this->rebuildFlag->check();
        } catch (RebuildInProgressException $e) {
            if ($this->logger) {
                $this->logger->warning('Rebuild in progress, stopping');
            }
            $event->getWorker()->stop();
        }
    }
}
