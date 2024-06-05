<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Transport\Sender;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class SendersLocator implements SendersLocatorInterface
{
    protected SendersLocatorInterface $decoratedService;

    protected bool $backgroundJobsEnabled;

    public function __construct(SendersLocatorInterface $decoratedService, bool $backgroundJobsEnabled)
    {
        $this->decoratedService = $decoratedService;
        $this->backgroundJobsEnabled = $backgroundJobsEnabled;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        if ($this->backgroundJobsEnabled) {
            return $this->decoratedService->getSenders($envelope);
        }

        // Don't provide any transport for a message,
        // so it will be executed synchronously.
        return [];
    }
}
