<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\reCAPTCHA\Core;

use XCart\Event\Service\ViewListMutationEvent;

final class EventListener
{
    public function onCollectViewListMutationsAfter(ViewListMutationEvent $event): void
    {
        $event->addRemoveMutation('modules/CDev/ContactUs/contact_us/fields/field.captcha.twig');
    }
}
