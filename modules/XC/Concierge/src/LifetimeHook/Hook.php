<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\LifetimeHook;

use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Contracts\EventDispatcher\Event;
use XLite\Core\Config;
use XC\Concierge\Main;

class Hook
{
    public function onInit(Event $event): void
    {
        if ($event instanceof ConsoleEvent) {
            return;
        }

        // For the admin zone $event->getController() returns the array [{XCart\Controller\XCartController}, 'admin']
        // See config/routes.yaml
        $isAdminZone = (
            is_array($event->getController())
            && isset($event->getController()[1])
            && ($event->getController()[1] === 'admin')
        );

        if (
            $isAdminZone
            && Config::getInstance()->XC->Concierge->additional_config_loaded !== 'true'
            && !\XLite\Core\Request::getInstance()->isAJAX()
        ) {
            Main::fillDefaultConciergeOptions();
        }

        if (Config::getInstance()->XC->Concierge->is_user_id_correct !== 'true') {
            Main::checkAndCorrectUserId();
            Config::updateInstance();
        }
    }
}
