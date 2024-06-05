<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Event
 * @Extender\Mixin
 */
class Event extends \XLite\Core\Event
{
    /**
     * @inheritdoc
     */
    public function display()
    {
        parent::display();

        \QSL\Segment\Core\Mediator::getInstance()->displayAJAXMessages();
    }
}
