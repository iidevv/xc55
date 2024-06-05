<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Event extends \XLite\Core\Event
{
    public function display()
    {
        $events = Mediator::getInstance()->getMessages();
        if ($events) {
            $this->trigger('concierge.push', ['list' => $events]);
        }

        parent::display();
    }
}
