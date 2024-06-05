<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Twig\Environment;
use XLite\Core\Exception\ClosedStorefrontException;
use XLite\Logger;

class ExceptionListener
{
    private Logger $logger;

    private Environment $twig;

    public function __construct(
        Logger $logger,
        Environment $twig
    ) {
        $this->logger = $logger;
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof ClosedStorefrontException) {
            $event->stopPropagation();

            $event->setResponse(new Response($this->twig->render('closed.twig')));
        } else {
            $this->logger->executePostponedLogs();
            $this->logger->registerException($event->getThrowable());
        }

        // This is required to show the error page without HTML garbage
        while (ob_get_level() !== 0) {
            ob_end_clean();
        }
    }
}
